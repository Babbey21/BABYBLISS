<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manufacturer') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Pending Verification - BabyBliss</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root { --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A; --deep-rose: #C44D65; --mint-dark: #5FB8A0; --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A; }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .pending-card { background: var(--white); border-radius: 24px; padding: 48px; text-align: center; max-width: 480px; box-shadow: 0 20px 60px rgba(196,77,101,0.15); }
        .pending-icon { width: 100px; height: 100px; background: linear-gradient(135deg, #FFF8E0, #FFE4A0); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px; margin: 0 auto 24px; }
        h1 { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--text-dark); margin-bottom: 12px; }
        p { color: var(--text-light); font-size: 15px; line-height: 1.7; margin-bottom: 24px; }
        .info-box { background: var(--cream); border-radius: 12px; padding: 20px; margin-bottom: 24px; text-align: left; }
        .info-box h4 { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
        .info-box ul { list-style: none; padding: 0; }
        .info-box ul li { font-size: 13px; color: var(--text-mid); padding: 6px 0; display: flex; align-items: center; gap: 8px; }
        .info-box ul li i { color: var(--mint-dark); }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: white; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 15px; transition: all 0.25s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(196,77,101,0.35); }
        .logout { display: block; margin-top: 16px; color: var(--text-light); font-size: 14px; text-decoration: none; }
        .logout:hover { color: var(--rose); }
    </style>
</head>
<body>
    <div class="pending-card">
        <div class="pending-icon">⏳</div>
        <h1>Account Under Review</h1>
        <p>Thank you for registering as a manufacturer on BabyBliss Marketplace! Our team is currently reviewing your application to ensure quality and authenticity.</p>
        <div class="info-box">
            <h4><i class="fas fa-info-circle" style="color: var(--rose); margin-right: 6px;"></i>What happens next?</h4>
            <ul>
                <li><i class="fas fa-check"></i> Our team reviews your business information</li>
                <li><i class="fas fa-check"></i> Verification usually takes 1-2 business days</li>
                <li><i class="fas fa-check"></i> You'll receive an email once approved</li>
                <li><i class="fas fa-check"></i> Then you can start listing products immediately</li>
            </ul>
        </div>
        <a href="index.php" class="btn"><i class="fas fa-store"></i> Browse Marketplace</a>
        <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
    </div>
</body>
</html>