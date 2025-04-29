<?php


namespace App\Services;

use App\Repositories\Auth\AuthRepositoryInterface;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function login(array $credentials)
    {
        $user = $this->authRepository->login($credentials);

        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        $token = JWTAuth::attempt($credentials);

        if (!$token) {
            throw new \Exception('Invalid credentials', 401);
        }
        $result = $user;
        $result->token = $token;
        $result->token_expires = JWTAuth::factory()->getTTL() * 60;
        $result->token_type = 'Bearer';
        return $result;
    }
    public function logout()
    {
        return $this->authRepository->logout();
    }
    public function register(array $data)
    {


        $checkEmail = $this->authRepository->findByEmail($data['email']);
        if ($checkEmail) {
            throw new \Exception('Email already exists', 409);
        }

        $data['password'] = bcrypt($data['password']);


        return $this->authRepository->register($data);
    }
    public function refreshToken()
    {
        return $this->authRepository->refreshToken();
    }
}
