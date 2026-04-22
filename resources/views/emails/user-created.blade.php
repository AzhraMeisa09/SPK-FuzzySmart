<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; border: 1px solid #e1e1e1; border-radius: 8px; overflow: hidden; }
        .header { background-color: #16a34a; padding: 20px; text-align: center; color: white; }
        .content { padding: 30px; background-color: #ffffff; }
        .footer { background-color: #f9f9f9; padding: 15px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #eeeeee; }
        .info-box { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 15px; margin: 20px 0; }
        .info-item { margin-bottom: 8px; }
        .label { font-weight: bold; color: #166534; width: 100px; display: inline-block; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #16a34a; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        h2 { margin-top: 0; color: #111; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0; font-size: 24px;">TK Pembina</h1>
            <p style="margin:5px 0 0; font-size: 14px;">Sistem Penilaian Perkembangan Siswa</p>
        </div>
        <div class="content">
            <h2>Halo, {{ $nama_lengkap }}</h2>
            <p>Akun Anda telah berhasil dibuat oleh Admin dalam Sistem SPK Penilaian Siswa TK Pembina.</p>
            
            <p>Silakan gunakan informasi berikut untuk masuk ke dashboard Anda:</p>
            
            <div class="info-box">
                <div class="info-item">
                    <span class="label">Username:</span> {{ $username }}
                </div>
                <div class="info-item">
                    <span class="label">Email:</span> {{ $email }}
                </div>
                <div class="info-item">
                    <span class="label">Password:</span> <strong>{{ $password }}</strong>
                </div>
            </div>
            
            <p>Demi kenyamanan dan keamanan, kami sangat menyarankan Anda untuk <strong>segera mengubah password</strong> setelah login pertama kali.</p>
            
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="btn">Login ke Sistem</a>
            </div>
            
            <p style="font-size: 13px; margin-top: 30px; font-style: italic; color: #666;">
                *Jika Anda tidak merasa mendaftar di sistem ini, silakan abaikan email ini.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} TK Pembina — Negeri Teladan Jakarta<br>
            Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi RI
        </div>
    </div>
</body>
</html>
