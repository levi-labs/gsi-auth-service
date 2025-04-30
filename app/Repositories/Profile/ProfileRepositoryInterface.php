<?php

namespace App\Repositories\Profile;

interface ProfileRepositoryInterface
{
    public function getProfileByUserId(int $userId);
    public function updateProfile(array $data);
}
