<?php


namespace App\Services;

use App\Jobs\SendEmailJob;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\DB;
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
        if ($user->email_verified_at == null) {
            throw new \Exception('Email not verified', 403);
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

    public function register(array $data)
    {
        DB::beginTransaction();

        try {
            $checkEmail = $this->authRepository->findByEmail($data['email']);
            if ($checkEmail) {
                throw new \Exception('Email already exists', 409);
            }

            $data['password'] = bcrypt($data['password']);

            $user = $this->authRepository->register($data);

            dispatch(new SendEmailJob($user));

            DB::commit();

            return $user;
        } catch (\Exception $err) {
            DB::rollBack();
            throw new \Exception('Registration failed: ' . $err->getMessage(), 500);
        }
    }
    public function forgotPassword(string $email)
    {
        $user = $this->authRepository->findByEmail($email);
        if (!$user) {
            throw new \Exception('User not found', 404);
        }

        if ($user->email_verified_at == null) {
            throw new \Exception('Email not verified', 403);
        }

        dispatch(new SendEmailJob($user, 'password'));

        return true;
    }
    public function refreshToken()
    {
        return $this->authRepository->refreshToken();
    }
    public function logout()
    {
        return $this->authRepository->logout();
    }
}
