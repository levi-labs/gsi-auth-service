<?php

use App\Repositories\Profile\ProfileRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    protected $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function getProfileByUserId(int $userId)
    {
        return $this->profileRepository->getProfileByUserId($userId);
    }

    public function updateProfile(array $data)
    {
        $userId = $this->profileRepository->getProfileByUserId($data['user_id'])->user_id;
        $image = $data['image'] ?? null;
        if ($image) {
            $existsStorage = Storage::disk('public')->exists('profiles/' . $image->getClientOriginalName());
            if ($existsStorage) {
                Storage::disk('public')->delete('profiles/' . $image->getClientOriginalName());
            }
            $imagePath = $image->storeAs('profiles', $image->getClientOriginalName(), 'public');
            $data['image'] = $imagePath;
        }
        $data['updated_at'] = now();

        return $this->profileRepository->updateProfile($userId, $data);
    }
}
