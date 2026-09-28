<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perubahan Password Akun SKIN ITG</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 style="color: #1e3a8a; margin: 0;">Sistem Informasi Kemahasiswaan (SKIN)</h2>
        <p style="color: #666; margin: 5px 0 0 0; font-size: 13px;">Institut Teknologi Garut</p>
    </div>

    <p>Halo, <strong>{{ $user->name }}</strong>,</p>

    <p>Password akun Anda baru saja diubah melalui {{ $sumber }}.</p>

    <div style="background-color: #fffbeb; border-left: 4px solid #b45309; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <div style="font-size: 13px; color: #4b5563;">Waktu: <strong>{{ now()->translatedFormat('d F Y, H:i') }} WIB</strong></div>
        <div style="font-size: 13px; color: #4b5563; margin-top: 5px;">Email akun: <strong>{{ $user->email }}</strong></div>
    </div>

    <p>Jika Anda tidak merasa melakukan perubahan ini, segera hubungi Biro Kemahasiswaan (BKHM) ITG dan minta password direset ulang.</p>

    <p style="font-size: 12px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 30px;">
        Email ini dikirimkan otomatis oleh SKIN Institut Teknologi Garut.
    </p>
</body>
</html>
