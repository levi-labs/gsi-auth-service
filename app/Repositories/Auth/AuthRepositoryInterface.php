<?php

namespace App\Repositories\Interfaces;

interface AuthRepositoryInterface
{
    public function login(array $credentials);
    public function logout();
    public function register(array $data);
    public function refreshToken();
}
