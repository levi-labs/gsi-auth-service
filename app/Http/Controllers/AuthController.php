<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 422);
        }

        try {
            $result = $this->authService->login($validation->validated());

            return response()->json([
                'meta' => [
                    'success' => true,
                    'message' => 'Successfully logged in',
                    'statusCode' => 200,
                ],
                'data' => $result
            ], 200);
        } catch (\Exception $err) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'message' => $err->getMessage(),
                    'statusCode' => $err->getCode(),
                ],
            ], 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 422);
        }

        try {
            $result = $this->authService->register($validation->validated());
            return response()->json([
                'meta' => [
                    'success' => true,
                    'message' => 'Successfully registered',
                    'statusCode' => 201,
                ],
                'data' => $result
            ], 201);
        } catch (\Exception $err) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'message' => $err->getMessage(),
                    'statusCode' => $err->getCode(),
                ],
            ], 500);
        }
    }

    public function logout()
    {
        auth()->guard('api')->logout();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
