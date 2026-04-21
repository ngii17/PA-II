<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Akun</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;">
        <h2 style="color: #333;">Halo!</h2>
        <p>Terima kasih telah mendaftar di <strong>Purnama Hotel & Resto</strong>.</p>
        <p>Gunakan kode OTP di bawah ini untuk memverifikasi akun Anda:</p>
        
        <div style="background: #f4f4f4; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #007bff; border-radius: 5px;">
            {{ $otp }}
        </div>

        <p>Kode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapapun demi keamanan akun Anda.</p>
        <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>
        <hr>
        <p style="font-size: 12px; color: #777;">&copy; 2024 Purnama Hotel & Resto. All rights reserved.</p>
    </div>
</body>
</html>