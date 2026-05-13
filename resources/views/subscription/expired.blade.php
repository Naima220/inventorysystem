
<!DOCTYPE html>
<html>
<head>
    <title>Subscription Expired</title>
</head>
<body style="
    background: linear-gradient(to right, #dbeafe, #eff6ff);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    font-family:sans-serif;
">

    <div style="
        background:white;
        padding:40px;
        border-radius:15px;
        text-align:center;
        box-shadow:0 15px 30px rgba(0,0,0,0.15);
        width:400px;
    ">

        <h2 style="color:#dc2626; margin-bottom:20px;">
            Your Subscription Has Expired
        </h2>

        <p style="margin-bottom:30px; color:#1e3a8a;">
            Please contact Super Admin to renew your subscription.
        </p>

        <a href="{{ route('login') }}" 
           style="
            background:#2563eb;
            color:white;
            padding:10px 20px;
            border-radius:8px;
            text-decoration:none;
            font-weight:bold;
           ">
           Back to Login
        </a>
        

    </div>

</body>
</html>