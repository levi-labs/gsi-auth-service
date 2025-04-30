<?php


namespace App\Services;

use App\Jobs\CreateProfileJob;
use App\Jobs\SendEmailJob;
use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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
            if (!$user) {
                throw new \Exception('Registration failed', 500);
            }
            $token = JWTAuth::fromUser($user);

            if (!$token) {
                throw new \Exception('Token generation failed', 500);
            }
            dispatch(new CreateProfileJob($user, $token));

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

        return $user;
    }
    public function verifyEmail($user)
    {
        $user = User::findOrFail($user);
        if ($user->is_verified) {
            return response()->json(['message' => 'Email already verified.']);
        }
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }

    public function updatePassword($user, string $password)
    {
        $user = User::findOrFail($user);
        $user->password = bcrypt($password);
        return $user;
    }
    public function refreshToken()
    {

        try {
            $newToken = JWTAuth::parseToken()->refresh();
            if (!$newToken) {
                throw new \Exception('Token is invalid or expired', 401);
            }

            $result = [
                'token' => $newToken,
                'token_expires' => JWTAuth::factory()->getTTL() * 60,
                'token_type' => 'Bearer',
            ];
            return $result;
        } catch (TokenExpiredException | TokenInvalidException $err) {
            throw new \Exception('Token is invalid or expired', 401);
        }
    }
    public function logout()
    {


        $token = JWTAuth::parseToken();

        if (!$token->check()) {
            throw new \Exception('Token is invalid or expired', 401);
        }

        $token->invalidate();

        return true;
    }
}
