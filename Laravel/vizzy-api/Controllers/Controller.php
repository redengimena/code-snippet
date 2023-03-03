<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result=null, $message=null)
    {
    	$response = ['success' => true];

        if ($result) {
            $response['data'] = $result;
        }

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 404)
    {
    	$response = ['success' => false];

        if($error){
            $response['message'] = $error;
        }

        return response()->json($response, $code);
    }

    /**
     * Override validation json response
     */
    public function validate(
        Request $request,
        array $rules,
        array $messages = [],
        array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()
            ->make(
                $request->all(),
                $rules, $messages,
                $customAttributes
            );
        if ($validator->fails()) {
            $errors = (new \Illuminate\Validation\ValidationException($validator))->errors();
            throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json(
                [
                    'success' => false,
                    'errors' => $errors,
                    'message' => "The given data was invalid."
                ], \Illuminate\Http\JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }
    }
}