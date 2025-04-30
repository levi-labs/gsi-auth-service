<?php


namespace App\Repositories\Profile;

use App\Models\Profile;
use App\Repositories\Profile\ProfileRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProfileRepository implements ProfileRepositoryInterface
{
    public function getProfileByUserId(int $userId): Profile
    {
        return Profile::where('user_id', $userId)->first();
    }

    public function updateProfile(array $data): Profile
    {
        DB::beginTransaction();
        try {
            $profile = Profile::where('user_id', $data['user_id'])->first();
            if (!$profile) {
                throw new \Exception('Profile not found');
            }

            $profile->update($data);
            DB::commit();
            return $profile;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating profile: ' . $e->getMessage());
            throw $e;
        }
    }
}
