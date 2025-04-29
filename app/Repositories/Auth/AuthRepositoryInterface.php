<?php

namespace App\Repositories\Auth;

interface AuthRepositoryInterface
{
    public function login(array $credentials);
    public function logout();
    public function register(array $data);
    public function refreshToken();
    public function findByEmail(string $email);
}
