<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 0; margin: 0;">
    <div
        style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div style="background-color: #007bff; color: white; padding: 20px 40px; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">📧 Verifikasi Email</h1>
        </div>
        <div style="padding: 30px 40px;">
            <p style="font-size: 16px;">Halo, <strong>{{ $user->name }}</strong>!</p>
            <p style="font-size: 16px;">Silakan klik tombol di bawah untuk memverifikasi email Anda:</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationLink }}"
                    style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 16px;">
                    Verifikasi Email
                </a>
            </div>
            {{-- 
            <div style="text-align: center; margin: 30px 0;">
                <p style="font-size: 16px;">Atau gunakan kode OTP berikut:</p>
                <div
                    style="background-color: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 16px;">
                    {{ $user->otp }}
                </div>

            </div> --}}

            <p style="font-size: 14px; color: #666;">Link ini akan kadaluarsa dalam 30 menit.</p>
            <p style="font-size: 16px;">Terima kasih!<br>Tim Kami</p>
        </div>
    </div>
</body>

</html>
