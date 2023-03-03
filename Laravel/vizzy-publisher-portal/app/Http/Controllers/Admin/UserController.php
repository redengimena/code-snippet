<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\Reseller;
use App\Models\Client;
use App\Rules\IsValidPassword;
use Hash;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'firstname'=>'required',
            'lastname'=>'required',
            'company'=>'required',
            'phone'=>'required'
        ]);

        try {
            DB::beginTransaction();

            $oldData = clone $user;

            $user->firstname =  $request->get('firstname');
            $user->lastname =  $request->get('lastname');
            $user->company =  $request->get('company');
            $user->phone =  $request->get('phone');
            $user->save();

            if ($request->user()->hasRole('admin')) {
                $user->roles()->detach();
                if ($request->get('is_admin')) {
                    $role = Role::where('slug', 'admin')->first();
                    $user->roles()->attach($role);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
        }

        return back()->with('success', 'Publisher updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        try {
            DB::beginTransaction();

            $user->podcasts()->delete();
            $user->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Error deleting user!' . $e->getMessage());
        }

        return redirect(route('admin.users.index'))->with('success', 'Publisher updated!');
    }
}
