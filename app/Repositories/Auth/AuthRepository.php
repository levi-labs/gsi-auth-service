<?php


namespace App\Repositories\Auth;

//use auth


use App\Models\User;

use App\Repositories\Interfaces\AuthRepositoryInterface;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(array $credentials)
    {
        // Logic for user login
        return User::where('email', $credentials['email'])->first();
    }

    public function logout()
    {
        // Logic for user logout
        auth()->guard('api')->logout();
    }

    public function register(array $data)
    {
        // Logic for user registration
        return User::create($data);
    }

    public function refreshToken()
    {
        // Logic for refreshing token
        return JWTAuth::refresh(JWTAuth::getToken());
    }
}
