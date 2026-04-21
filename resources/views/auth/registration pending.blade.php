<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Received — Smart Chama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <style>
        body { background: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .pending-card {
            background: white; border-radius: 20px; padding: 48px 40px;
            max-width: 480px; width: 100%; text-align: center;
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
        }
        .pending-icon {
            width: 80px; height: 80px; border-radius: 50%;
            background: #fffbeb; border: 3px solid #fef3c7;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; margin: 0 auto 24px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245,158,11,.3); }
            50% { transform: scale(1.04); box-shadow: 0 0 0 12px rgba(245,158,11,0); }
        }
        .pending-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 10px; font-family: 'DM Sans', sans-serif; }
        .pending-name { color: #1d4ed8; }
        .pending-sub { font-size: 15px; color: #64748b; line-height: 1.7; margin-bottom: 28px; }
        .steps-list { list-style: none; padding: 0; margin: 0 0 28px; text-align: left; }
        .steps-list li {
            display: flex; align-items: flex-start; gap: 12px;
            padding: 12px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #475569;
        }
        .steps-list li:last-child { border-bottom: none; }
        .step-num {
            width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
            background: #1d4ed8; color: white; font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .step-num.done { background: #16a34a; }
        .chama-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
            border-radius: 8px; padding: 8px 16px; font-size: 13px; font-weight: 600;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="pending-card">

    <div class="pending-icon">⏳</div>

    <h1 class="pending-title">
        You're registered, <span class="pending-name">{{ session('registered_name', 'there') }}!</span>
    </h1>

    <div class="chama-badge">
        <i class="bi bi-people-fill"></i>
        {{ session('registered_chama', 'Your Chama') }}
    </div>

    <p class="pending-sub">
        Your account has been created and is now waiting for the group admin to approve it.
        You'll be able to log in once they approve your request.
    </p>

    <ul class="steps-list">
        <li>
            <div class="step-num done"><i class="bi bi-check" style="font-size:12px"></i></div>
            <div>
                <strong>Account created</strong><br>
                Your details have been saved successfully.
            </div>
        </li>
        <li>
            <div class="step-num">2</div>
            <div>
                <strong>Admin approval</strong><br>
                The group admin will review and approve your account. This usually takes a few hours.
            </div>
        </li>
        <li>
            <div class="step-num">3</div>
            <div>
                <strong>Log in &amp; contribute</strong><br>
                Once approved, log in and start tracking your contributions.
            </div>
        </li>
    </ul>

    <a href="{{ route('login') }}"
       style="display:block;background:#1d4ed8;color:white;padding:13px 24px;border-radius:8px;font-size:15px;font-weight:700;text-decoration:none;font-family:'DM Sans',sans-serif;transition:background .2s"
       onmouseover="this.style.background='#1e40af'"
       onmouseout="this.style.background='#1d4ed8'">
        <i class="bi bi-box-arrow-in-right"></i> Go to Login
    </a>

    <p style="font-size:12px;color:#94a3b8;margin-top:16px">
        Registered with email: <strong>{{ session('registered_email', '') }}</strong>
    </p>

</div>
</body>
</html>