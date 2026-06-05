<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Anda Telah Dibuat - SPK TK Negeri Pembina</title>
    <style>
        /* Base Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }

        /* General Styles */
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #F4F6F0;
            color: #334155;
            line-height: 1.6;
        }

        a {
            color: #6A783D;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Responsive Wrapper */
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #F4F6F0;
            padding: 40px 20px;
        }

        .main-card {
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(106, 120, 61, 0.08);
            border: 1px solid #E2E8F0;
        }

        /* Banner Header */
        .header-banner {
            background: linear-gradient(135deg, #6A783D 0%, #84934A 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .header-banner h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-banner p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 13px;
            font-weight: 600;
            margin: 6px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Content Body */
        .content-body {
            padding: 40px 35px;
        }

        .welcome-title {
            font-size: 22px;
            font-weight: 800;
            color: #1E293B;
            margin-top: 0;
            margin-bottom: 12px;
            letter-spacing: -0.3px;
        }

        .welcome-desc {
            font-size: 14px;
            color: #64748B;
            margin-bottom: 30px;
        }

        /* Role Badges */
        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 24px;
        }

        .badge-admin {
            background-color: #FEF2F2;
            color: #EF4444;
            border: 1px solid #FEE2E2;
        }

        .badge-guru {
            background-color: #ECFDF5;
            color: #10B981;
            border: 1px solid #D1FAE5;
        }

        .badge-kepala-sekolah {
            background-color: #F5F3FF;
            color: #8B5CF6;
            border: 1px solid #EDE9FE;
        }

        .badge-wali-murid {
            background-color: #EFF6FF;
            color: #3B82F6;
            border: 1px solid #DBEAFE;
        }

        /* Credentials Box */
        .credentials-card {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 24px;
            margin: 25px 0;
        }

        .credentials-header {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94A3B8;
            margin-bottom: 16px;
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 8px;
        }

        .credential-row {
            padding: 8px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .credential-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748B;
        }

        .credential-value {
            font-size: 14px;
            font-weight: 700;
            color: #1E293B;
            font-family: 'Courier New', Courier, monospace;
        }

        .credential-password {
            background-color: #F1F5F9;
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px dashed #CBD5E1;
            font-weight: 800;
            color: #0F172A;
            font-size: 14px;
        }

        /* Action Button */
        .btn-container {
            text-align: center;
            margin: 35px 0 25px 0;
        }

        .action-button {
            display: inline-block;
            background-color: #6A783D;
            color: #ffffff !important;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none !important;
            padding: 14px 32px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(106, 120, 61, 0.2);
            transition: all 0.2s ease-in-out;
        }

        /* Tips Alert Box */
        .tips-box {
            background-color: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 16px;
            padding: 20px;
            margin-top: 30px;
            display: flex;
            gap: 12px;
        }

        .tips-icon {
            font-size: 20px;
            line-height: 1;
        }

        .tips-content h4 {
            margin: 0 0 4px 0;
            font-size: 13px;
            font-weight: 700;
            color: #B45309;
        }

        .tips-content p {
            margin: 0;
            font-size: 12px;
            color: #D97706;
            line-height: 1.5;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
            font-size: 11px;
            color: #94A3B8;
        }

        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main-card" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td>
                    <!-- Header Banner -->
                    <div class="header-banner">
                        <h1>TK NEGERI PEMBINA PADANG PANJANG</h1>
                        <p>Sistem Informasi & Penilaian Perkembangan Siswa</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="content-body">
                    <!-- Greeting -->
                    <h2 class="welcome-title">Halo, {{ $nama_lengkap }}!</h2>
                    
                    <!-- Dynamic Role Badge -->
                    @php
                        $roleClass = 'badge-wali-murid';
                        $roleLabel = 'Wali Murid / Orang Tua';
                        
                        $cleanRole = strtolower($role);
                        if ($cleanRole === 'admin') {
                            $roleClass = 'badge-admin';
                            $roleLabel = 'Administrator Sistem';
                        } elseif ($cleanRole === 'guru') {
                            $roleClass = 'badge-guru';
                            $roleLabel = 'Guru / Tenaga Pendidik';
                        } elseif ($cleanRole === 'kepala sekolah') {
                            $roleClass = 'badge-kepala-sekolah';
                            $roleLabel = 'Kepala Sekolah';
                        }
                    @endphp
                    <div class="role-badge {{ $roleClass }}">
                        Role: {{ $roleLabel }}
                    </div>

                    <p class="welcome-desc">
                        Akun pengguna Anda telah berhasil didaftarkan oleh Administrator ke dalam <strong>Sistem Pendukung Keputusan (SPK) Penilaian Perkembangan Siswa dengan Metode Fuzzy SMART</strong> TK Negeri Pembina Padang Panjang.
                    </p>

                    <!-- Credentials Details Card -->
                    <div class="credentials-card">
                        <div class="credentials-header">Informasi Akun Login Anda</div>
                        
                        <div class="credential-row">
                            <span class="credential-label">Username</span>
                            <span class="credential-value" style="color: #6A783D;">{{ $username }}</span>
                        </div>
                        
                        <div class="credential-row">
                            <span class="credential-label">Email Terdaftar</span>
                            <span class="credential-value">{{ $email }}</span>
                        </div>
                        
                        <div class="credential-row" style="margin-top: 8px;">
                            <span class="credential-label">Password Sementara</span>
                            <span class="credential-password">{{ $password }}</span>
                        </div>
                    </div>

                    <!-- Call To Action -->
                    <div class="btn-container">
                        <a href="{{ config('app.url') }}" target="_blank" class="action-button">Masuk ke Dashboard</a>
                    </div>

                    <!-- Security Alert Tips -->
                    <div class="tips-box">
                        <span class="tips-icon">🔒</span>
                        <div class="tips-content">
                            <h4>Demi Keamanan Akun Anda</h4>
                            <p>Password di atas dibuat secara otomatis oleh sistem. Kami sangat merekomendasikan Anda untuk <strong>segera mengganti password</strong> Anda melalui menu pengaturan profil setelah berhasil masuk untuk pertama kalinya.</p>
                        </div>
                    </div>

                    <!-- Disclaimer -->
                    <p style="font-size: 11px; color: #94A3B8; margin-top: 35px; font-style: italic; text-align: center;">
                        *Email ini dikirimkan secara otomatis oleh sistem. Jika Anda merasa tidak pernah mendaftar atau tidak memiliki hubungan dengan TK Negeri Pembina, abaikan email ini dengan aman.
                    </p>

                    <!-- Footer -->
                    <div class="footer">
                        <p style="font-weight: 700; color: #64748B;">TK Negeri Pembina Padang Panjang</p>
                        <p>Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi Republik Indonesia</p>
                        <p style="margin-top: 8px;">&copy; {{ date('Y') }} SPK Fuzzy SMART. All rights reserved.</p>
                    </div>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
