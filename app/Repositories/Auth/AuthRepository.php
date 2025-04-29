<?php


namespace App\Repositories\Auth;

//use auth


use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthRepository implements AuthRepositoryInterface
{
    public function login(array $credentials)
    {
        // Logic for user login
        return User::where('username', $credentials['username'])->first();
    }

    public function findByEmail(string $email)
    {
        // Logic to find user by email
        return User::where('email', $email)->first();
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
