<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends ApiController
{
    public function __construct()
    {
        // $this->middleware('auth', ['only' => 'getAuthUser']);
        $this->middleware('guest', ['only' => ['signup', 'signin']]);
    }
    public function signup(Request $request)
    {
        // Validate user input
        $validator = \Validator::make($request->all(), [
            'name'      => 'required|max:255',
            'email'     => 'required|email|max:255|unique:users',
            'username'  => 'required|alpha_num|max:20|unique:users',
            'password'  => 'required|min:6',
        ]);

        if ($validator->fails())
        {
            return $this->respondValidationError($validator->errors());
        }

        // Create new user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'username' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
        ]);

        if ($user)
        {
            return $this->setStatusCode(201)
                        ->respondSuccessWithData($user, 'New user has been created!');
        }
    }

    public function signin(Request $request)
    {
        // Validate user input
        $validator = \Validator::make($request->all(), [
            'email'     => 'required|email|max:255',
            'password'  => 'required|min:6',
        ]);

        // Check if email exist
        $user = User::where('email', $request->input('email'))->first();
        if (!$user)
        {
            $validator->after(function($validator) {
                $validator->errors()->add('email', 'Email does not exist in our system.');
            });
        } else {
            if (!\Hash::check($request->input('password'), $user->password))
            {
                $validator->after(function($validator) {
                    $validator->errors()->add('email', 'Email and password combination does not match.');
                });
            }
        }

        if ($validator->fails())
        {
            return $this->respondValidationError($validator->errors());
        }

        $token = JWTAuth::fromUser($user);
        $fractal = fractal()
                ->item($user)
                ->transformWith(new UserTransformer)
                ->addMeta(['token' => $token])
                ->toArray();
        return $this->respondSuccessWithData([
            'user' => $fractal['data'],
            'token' => $fractal['meta']['token'],
        ]);
    }

    public function getAuthUser(Request $request)
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate())
            {
                return $this->respondNotFound("User not found");
            }

        } catch (TokenExpiredException $e)
        {
            return $this->setStatusCode($e->getStatusCode())
                        ->respondWithError("Token has been expired");

        } catch (TokenInvalidException $e)
        {
            return $this->setStatusCode($e->getStatusCode())
                        ->respondWithError("Token is invalid");

        } catch (JWTException $e)
        {
            return $this->setStatusCode($e->getStatusCode())
                        ->respondWithError("Token is absent");
        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }
}
