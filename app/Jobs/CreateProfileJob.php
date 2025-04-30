<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CreateProfileJob implements ShouldQueue
{
    use Dispatchable, Queueable;
    public $tries = 3;
    public $backoff = 5;
    protected $user;
    protected $token;
    /**
     * Create a new job instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $response = Http::withToken($this->token)
                ->accept('application/json')
                ->post('http://127.0.0.1:8001/api/profile', [
                    'user_id' => $this->user->id,
                    'name' => $this->user->name,
                ]);

            if (!$response->successful()) {

                $message = 'Unknown error occurred';

                // Cek apakah responsenya JSON, baru ambil field message
                if ($response->header('Content-Type') === 'application/json') {
                    $message = $response->json('message') ?? $response->json('error') ?? $message;
                }
                Log::error('Failed Profile creation for user: ', [
                    'user_id' => $this->user->id,
                    'status_code' => $response->status(),
                    'message' => $message,
                ]);
                throw new \Exception('Failed to create profile: ' . $message . 'status code: ' . $response->status());
            }
            Log::info('Profile created successfully for user: ' . $this->user->id . ' with status code: ' . $response->status());
        } catch (\Exception $err) {
            // Optionally, you can rethrow the exception to trigger the failed method
            throw $err;
        }
    }

    public function failed(\Exception $exception)
    {
        // Handle the failure, e.g., log it or notify someone
        Log::error('CreateProfileJob failed: ' . $exception->getMessage());
    }
}
