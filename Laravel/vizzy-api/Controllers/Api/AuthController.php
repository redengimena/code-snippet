<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\PasswordResetMail;
use App\WebServices\AppleToken;
use App\Events\ConsumerRegistered;
use App\Traits\ActivityLogTrait;
use App\Http\Resources\User as UserResource;
use Carbon\Carbon;
use Hash;

class AuthController extends Controller
{
    use ActivityLogTrait;

    /**
     * Registration
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            // 'name' => 'required|max:55',
            'email' => 'email|required|unique:users,email,'.$request->email.',id,provider_name,NULL',
            'password' => 'required|min:8',
            // 'email' => ['required', 'email',
            //     Rule::unique('users')->where(function ($query) {
            //         return $query->whereNull('provider_name');
            //     })
            // ]
        ]);

        $user = User::create([
            'name' => $request->name ? $request->name : '',
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        event(new ConsumerRegistered($user));

        $token = $user->createToken('Vizzy')->accessToken;

        $success['token'] =  $token;
        $success['firstLogin'] = true;

        $this->log('register', ['user_id' => $user->id]);

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $data = [
            'email' => trim($request->email),
            'password' => trim($request->password),
            'provider_id' => null
        ];

        if (auth()->attempt($data)) {
            $success['token'] = Auth()->user()->createToken('Vizzy')->accessToken;
            $success['firstLogin'] = false;
            $success['user'] = new UserResource(Auth()->user());
            $this->log('login', ['user_id' => Auth()->user()->id]);
            return $this->sendResponse($success, 'User login successfully.');
        } else {
            $user = User::where('email', $data['email'])->whereNull('provider_id')->first();
            if ($user) {
                $this->log('login failed', ['user_id' => $user->id]);
            } else {
                $this->log('login failed', ['user_id' => 0, 'content' => $data['email']]);
            }
            return $this->sendError('Invalid user credentials.', 401);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $user = auth('api')->user();

        if ($user) {
            $tokenRepository->revokeAccessToken($user->token()->id);
            $user->token()->delete();
            $this->log('logout', ['user_id' => $user->id]);
            return $this->sendResponse('', 'User logout successfully.');
        } else {
            return $this->sendError('Unauthorised.', 401);
        }
    }

    /**
     * Forgot password
     */
    public function forgot(Request $request)
    {
        $user = User::where('email', $request->input('email'))->whereNull('provider_name')->first();
        if ($user) {
            PasswordReset::where('email', $user->email)->delete();

            $passwordReset = new PasswordReset();
            $passwordReset->email = $user->email;
            $passwordReset->token = strtoupper(Str::random(6));
            $passwordReset->created_at = Carbon::now();
            $passwordReset->save();

            // send email
            Mail::to($user->email)->send(new PasswordResetMail($passwordReset));

            $this->log('password_reset_mail', ['user_id' => $user->id]);

            return $this->sendResponse('', 'Reset password email sent successfully.');
        } else {
            return $this->sendError('User not found.', 200);
        }
    }

    /**
     * Confirm password
     */
    public function forgotConfirm(Request $request)
    {
        $email = $request->input('email');
        $code = $request->input('code');

        $exists = PasswordReset::where('email', $email)->where('token', $code)->first();
        if ($exists) {
            return $this->sendResponse('', '');
        } else {
            return $this->sendError('Incorrect code.', 200);
        }
    }

    /**
     * Reset password
     */
    public function forgotReset(Request $request)
    {
        $this->validate($request, [
            'email' => 'email|required',
            'password' => 'required|min:8',
        ]);

        $email = $request->input('email');
        $code = $request->input('code');
        $password = $request->input('password');

        $exists = PasswordReset::where('email', $email)->where('token', $code)->first();
        if ($exists) {

            // delete code record
            PasswordReset::where('email', $email)->delete();

            // update password
            $user = User::where('email', $email)->whereNull('provider_name')->first();
            $user->password = Hash::make($password);
            $user->save();

            $this->log('password_reset_updated', ['user_id' => $user->id]);

            return $this->sendResponse('', 'Password has been updated.');
        } else {
            return $this->sendError('Invalid data provided.', 200);
        }
    }

    /**
     * Social login
     */
    public function socialLogin(Request $request, AppleToken $appleToken)
    {
        $provider = $request->input('provider');
        $token = $request->input('access_token');

        if ($provider == 'google')
        {
            $first_login = false;
            $client_id = config('services.google.client_id');
            $client = new \Google_Client(['client_id' => $client_id]);
            $payload = $client->verifyIdToken($token);
            if ($payload && $payload['aud'] == $client_id) {
                $user = User::where('provider_name', $provider)->where('provider_id', $payload['sub'])->first();    // if there is no record with these data, create a new user
                if($user == null){
                    $user = User::create([
                        'provider_name' => $provider,
                        'provider_id' => $payload['sub'],
                        'name' => $payload['name'],
                        'firstname' => $request->input('firstname'),
                        'lastname' => $request->input('lastname'),
                        'provider_image' => $payload['picture'],
                        'email' => $payload['email'],
                    ]);
                    $first_login = true;
                    event(new ConsumerRegistered($user));
                } else {
                    $updated = false;
                    if ($user->firstname != $request->input('firstname')) {
                        $user->firstname == $request->input('firstname');
                        $updated = true;
                    }
                    if ($user->lastname != $request->input('lastname')) {
                        $user->lastname == $request->input('lastname');
                        $updated = true;
                    }
                    if ($user->provider_image != $payload['picture']) {
                        $user->provider_image == $payload['picture'];
                        $updated = true;
                    }
                    if ($updated) {
                        $user->save();
                    }
                }

                $success['token'] = $user->createToken('Vizzy')->accessToken;
                $success['firstLogin'] = $first_login;
                $success['user'] = new UserResource($user);

                $this->log('social_login_google', ['user_id' => $user->id]);
                return $this->sendResponse($success, 'User login successfully.');

            } else {
              return $this->sendError('Invalid user credentials.', 401);
            }

        }
        elseif ($provider == 'apple')
        {
            config()->set('services.apple.client_secret', $appleToken->generate());
            $providerUser = Socialite::driver($provider)->userFromToken($token);
            if ($providerUser) {
                $first_login = false;
                $user = User::where('provider_name', $provider)->where('provider_id', $providerUser->id)->first();    // if there is no record with these data, create a new user
                if($user == null){
                    // apple id_token doesn't return the user name, so we get it from FE
                    $name = $providerUser->getName() ? $providerUser->getName() : $request->input('name');
                    $user = User::create([
                        'provider_name' => $provider,
                        'provider_id' => $providerUser->getId(),
                        'name' => $name ? $name : $provideruser->getEmail(),
                        'firstname' => $request->input('firstname'),
                        'lastname' => $request->input('lastname'),
                        'provider_image' => $providerUser->getAvatar(),
                        'email' => $providerUser->getEmail(),
                    ]);
                    $first_login = true;
                    event(new ConsumerRegistered($user));
                } else {
                    $updated = false;
                    if ($user->firstname != $request->input('firstname')) {
                        $user->firstname == $request->input('firstname');
                        $updated = true;
                    }
                    if ($user->lastname != $request->input('lastname')) {
                        $user->lastname == $request->input('lastname');
                        $updated = true;
                    }
                    if ($user->provider_image != $providerUser->getAvatar()) {
                        $user->provider_image == $providerUser->getAvatar();
                        $updated = true;
                    }
                    if ($updated) {
                        $user->save();
                    }
                }

                $success['token'] = $user->createToken('Vizzy')->accessToken;
                $success['firstLogin'] = $first_login;
                $success['user'] = new UserResource($user);

                $this->log('social_login_apple', ['user_id' => $user->id]);
                return $this->sendResponse($success, 'User login successfully.');

            } else {
                return $this->sendError('Invalid user credentials.', 401);
            }
        }
        else
        {
            $providerUser = Socialite::driver($provider)->userFromToken($token);
            if ($providerUser) {
                $first_login = false;
                $user = User::where('provider_name', $provider)->where('provider_id', $providerUser->id)->first();    // if there is no record with these data, create a new user
                if($user == null){
                    $user = User::create([
                        'provider_name' => $provider,
                        'provider_id' => $providerUser->getId(),
                        'name' => $providerUser->getName(),
                        'firstname' => $request->input('firstname'),
                        'lastname' => $request->input('lastname'),
                        'provider_image' => $providerUser->getAvatar(),
                        'email' => $providerUser->getEmail(),
                    ]);
                    $first_login = true;
                    event(new ConsumerRegistered($user));
                } else {
                    $updated = false;
                    if ($user->firstname != $request->input('firstname')) {
                        $user->firstname == $request->input('firstname');
                        $updated = true;
                    }
                    if ($user->lastname != $request->input('lastname')) {
                        $user->lastname == $request->input('lastname');
                        $updated = true;
                    }
                    if ($user->provider_image != $providerUser->getAvatar()) {
                        $user->provider_image == $providerUser->getAvatar();
                        $updated = true;
                    }
                    if ($updated) {
                        $user->save();
                    }
                }

                $success['token'] = $user->createToken('Vizzy')->accessToken;
                $success['firstLogin'] = $first_login;
                $success['user'] = new UserResource($user);

                $this->log('social_login_facebook', ['user_id' => $user->id]);
                return $this->sendResponse($success, 'User login successfully.');

            } else {
                return $this->sendError('Invalid user credentials.', 401);
            }
        }
    }

    /**
     * Edit user details
     */
    public function editDetails(Request $request)
    {
        $override = false;
        $user = $request->user();

        if ($request->firstname && $request->firstname != $user->firstname) {
            $user->firstname = $request->firstname;
            $override = true;
        }

        if ($request->lastname && $request->lastname != $user->lastname) {
            $user->lastname = $request->lastname;
            $override = true;
        }

        if (!$user->provider_id) {
            if ($request->email && $request->email != $user->email) {
                $exists = User::whereNull('provider_name')->where('email', $request->email)->where('id', '!=', $user->id)->first();
                if ($exists) {
                    return $this->sendError('Account already exists for this email.', 200);
                }
                $user->email = $request->email;
            }
        }

        if ($request->image_delete && $user->image && substr( $user->image, 0, 4 ) !== "http") {
            Storage::delete('/public/' . $user->image);
            $user->image = '';
        }

        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png|max:1014',
                ]);
                if ($user->image && substr( $user->image, 0, 4 ) !== "http") {
                    Storage::delete('/public/' . $user->image);
                }
                $extension = $request->image->extension();
                $filename = str_replace('-','_',strtolower($user->id));
                $request->image->storeAs('/public', 'profiles/'.$filename.".".$extension);
                $url = 'profiles/'.$filename.".".$extension;
                $user->image = $url;
            }
        }

        if ($user->provider_name) {
            $user->override_provider = $override;
        }

        $user->save();

        $this->log('update-user-details', ['user_id' => $user->id]);

        return $this->sendResponse(new UserResource($user), 'User updated successfully.');
    }
}
