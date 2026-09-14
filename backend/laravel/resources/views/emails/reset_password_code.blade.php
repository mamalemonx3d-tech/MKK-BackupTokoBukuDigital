<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode Verifikasi Lupa Password</title>
    <style>
        body { font-family: sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 20px; }
        .card { max-width: 500px; margin: 0 auto; background: #ffffff; padding: 32px; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo span { font-size: 32px; }
        .logo h2 { margin: 8px 0 0 0; color: #4f46e5; font-size: 24px; font-weight: 800; }
        .title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
        .text { font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 24px; }
        .code-box { background: #f1f5f9; border: 2px dashed #6366f1; border-radius: 16px; padding: 20px; text-align: center; margin-bottom: 24px; }
        .code-box span { font-family: monospace; font-size: 36px; font-weight: 800; letter-spacing: 8px; color: #4338ca; }
        .footer { font-size: 12px; color: #94a3b8; text-align: center; border-top: 1px solid #f1f5f9; pt: 16px; margin-top: 24px; }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">
            <span>📚</span>
            <h2>BookStore</h2>
        </div>

        <div class="title">Halo, {{ $name }}!</div>
        
        <div class="text">
            Kami menerima permintaan untuk mereset password akun BookStore Anda. Gunakan <strong>kode verifikasi 6 digit</strong> di bawah ini untuk melanjutkan proses reset password:
        </div>

        <div class="code-box">
            <span>{{ $code }}</span>
        </div>

        <div class="text" style="font-size: 12px; color: #64748b;">
            • Kode ini berlaku selama <strong>15 menit</strong>.<br>
            • Jangan berikan kode ini kepada siapa pun untuk keamanan akun Anda.<br>
            • Jika Anda tidak meminta reset password, abaikan email ini.
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} BookStore Project. All rights reserved.
        </div>
    </div>

</body>
</html>
