<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use App\Models\User;
use Carbon\Traits\Serialization;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $user;
    protected $type;
    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $type = 'verification')
    {
        $this->user = $user;
        $this->type = $type;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Logic to send email
        // You can use the Mail facade or any other method to send the email
        // For example:
        // Mail::to($this->user->email)->send(new SendEmail());
        // Or you can use a service class to handle the email sending logic

        try {
            if ($this->type == 'verification') {
                URL::forceRootUrl(config('app.url'));
                $verificationLink = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(30),
                    ['user' => $this->user->id]
                );
                Log::info('Verification link: ' . $verificationLink);
                Mail::to($this->user->email)->send(new SendEmail($this->user, $verificationLink));
            } else {
                URL::forceRootUrl(config('app.url'));
                $verificationLink = URL::temporarySignedRoute(
                    'form.password',
                    now()->addMinutes(30),
                    ['user' => $this->user->id]
                );
                Log::info('Password reset link: ' . $verificationLink);
                Mail::to($this->user->email)->send(new SendEmail($this->user, $verificationLink));
            }
        } catch (\Exception $err) {
            // Handle the error
            // You can log the error or take any other action
            Log::error('Failed to send email: ' . $err->getMessage());
            throw $err;
        }
    }
}
