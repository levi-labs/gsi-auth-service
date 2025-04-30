<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware('web')->group(function () {
    Route::get('/debug/request', function () {
        return response()->json([
            'is_https' => request()->isSecure(),
            'ip' => request()->ip(),
            'headers' => request()->headers->all(),
        ]);
    });
    Route::get('/email/verify/{user}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed') // hanya middleware signed
        ->name('verification.verify');
    Route::get('/forgot-password-form/{user}', [AuthController::class, 'resetPasswordForm'])
        ->middleware('signed') // hanya middleware signed
        ->name('form.password');
    Route::patch('update-password/{user}', [AuthController::class, 'updatePassword'])
        ->name('update.password');
});
