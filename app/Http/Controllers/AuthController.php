<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
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
            ], $err->getCode() ?? 500);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|string',
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
                    'message' => 'Successfully registered please check your email to verify your account',
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
        try {
            $result = $this->authService->logout();
            return response()->json([
                'meta' => [
                    'success' => true,
                    'message' => 'Successfully logged out',
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
            ], $err->getCode() ?? 500);
        }
    }

    public function verifyEmail(Request $request, $user)
    {
        try {
            if (!$request->hasValidSignature()) {
                return response()->json(['message' => 'Invalid or expired signature.'], 403);
            }
            $this->authService->verifyEmail($user);

            return response()->json(['message' => 'Email verified successfully.']);
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
    public function forgotPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 422);
        }

        try {
            $result = $this->authService->forgotPassword($validation->validated()['email']);
            return response()->json([
                'meta' => [
                    'success' => true,
                    'message' => 'Successfully sent email verification',
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
    }

    public function resetPasswordForm(Request $request, $user)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['message' => 'Invalid or expired signature.'], 403);
        }
        $user = User::findOrFail($user);


        return view('password.password', compact('user'));
    }

    public function updatePassword(Request $request, $user)
    {
        $validation = Validator::make($request->all(), [
            'password' => 'required|string',
            'password_confirmation' => 'required|string|same:password',
        ]);

        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 422);
        }

        try {
            $this->authService->updatePassword($user, $validation->validated()['password']);
            return response()->json(['message' => 'Password updated successfully.']);
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

    public function refreshToken()
    {
        try {
            $result = $this->authService->refreshToken();
            return response()->json([
                'meta' => [
                    'success' => true,
                    'message' => 'Successfully refreshed token',
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
            ], $err->getCode() ?? 500);
        }
    }
}
