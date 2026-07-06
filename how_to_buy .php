<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>How to Buy – BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A;
      --deep-rose: #C44D65; --mint: #A8D8C8; --mint-dark: #5FB8A0;
      --gold: #F5C842; --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A;
      --shadow: rgba(196,77,101,0.12);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--text-dark); line-height: 1.7; }
    
    header {
      background: var(--white); position: sticky; top: 0; z-index: 999;
      box-shadow: 0 2px 20px var(--shadow);
    }
    .header-main {
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 48px; height: 72px; max-width: 1400px; margin: 0 auto;
    }
    .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .logo-icon { width: 44px; height: 44px; background: linear-gradient(135deg, var(--blush), var(--deep-rose)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; }
    .logo-text { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: var(--deep-rose); }
    .btn-back { padding: 9px 22px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; border: 2px solid var(--rose); color: var(--rose); background: transparent; transition: all 0.2s; text-decoration: none; }
    .btn-back:hover { background: var(--rose); color: var(--white); }

    .hero { background: linear-gradient(135deg, var(--deep-rose), #A03050); padding: 60px 48px; text-align: center; color: var(--white); }
    .hero h1 { font-family: 'Playfair Display', serif; font-size: 42px; margin-bottom: 12px; }
    .hero p { font-size: 16px; opacity: 0.85; max-width: 600px; margin: 0 auto; }

    .container { max-width: 900px; margin: 0 auto; padding: 48px 24px; }
    
    .step { display: flex; gap: 24px; margin-bottom: 40px; }
    .step-num {
      width: 60px; height: 60px; border-radius: 50%;
      background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); display: flex; align-items: center; justify-content: center;
      font-size: 24px; font-weight: 700; flex-shrink: 0;
      box-shadow: 0 4px 16px var(--shadow);
    }
    .step-content h3 { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 8px; }
    .step-content p { color: var(--text-mid); margin-bottom: 12px; }
    .step-content ul { margin-left: 20px; }
    .step-content ul li { color: var(--text-mid); margin-bottom: 6px; }

    .tip-box {
      background: linear-gradient(135deg, #E8F8F5, #D4F5EC);
      border-left: 4px solid var(--mint-dark); border-radius: 0 12px 12px 0;
      padding: 16px 20px; margin: 16px 0;
    }
    .tip-box p { margin: 0; font-size: 14px; color: var(--text-mid); }

    .cta-banner {
      background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      border-radius: 20px; padding: 40px; text-align: center; color: var(--white);
      margin-top: 48px;
    }
    .cta-banner h2 { font-family: 'Playfair Display', serif; font-size: 28px; margin-bottom: 12px; }
    .cta-banner p { opacity: 0.9; margin-bottom: 20px; }
    .cta-btn {
      display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px;
      background: var(--white); color: var(--deep-rose); border-radius: 12px;
      font-weight: 700; text-decoration: none; transition: all 0.2s;
    }
    .cta-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .step { flex-direction: column; gap: 12px; }
    }
  </style>
</head>
<body>

<header>
  <div class="header-main">
    <a href="index.php" class="logo">
      <div class="logo-icon">🍼</div>
      <div class="logo-text">BabyBliss</div>
    </a>
    <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Shop</a>
  </div>
</header>

<section class="hero">
  <h1>How to Buy 🛒</h1>
  <p>Shopping on BabyBliss is easy, safe, and designed with busy parents in mind. Follow these simple steps.</p>
</section>

<div class="container">

  <div class="step">
    <div class="step-num">1</div>
    <div class="step-content">
      <h3>Browse & Discover</h3>
      <p>Explore our curated collection of baby products. Use the search bar, category filters, or age recommendations to find exactly what you need.</p>
      <ul>
        <li>Use filters for age range, price, category, and brand</li>
        <li>Read product descriptions and reviews</li>
        <li>Check age recommendations for safety</li>
      </ul>
      <div class="tip-box">
        <p><strong>💡 Tip:</strong> Save items to your wishlist by clicking the heart icon for easy access later.</p>
      </div>
    </div>
  </div>

  <div class="step">
    <div class="step-num">2</div>
    <div class="step-content">
      <h3>Add to Cart</h3>
      <p>Found something you love? Click "Add to Cart" to save it for checkout. You can adjust quantities or remove items anytime before payment.</p>
      <ul>
        <li>Review your cart by clicking the cart icon</li>
        <li>Apply discount codes if you have any</li>
        <li>Check the order summary for accuracy</li>
      </ul>
    </div>
  </div>

  <div class="step">
    <div class="step-num">3</div>
    <div class="step-content">
      <h3>Checkout Securely</h3>
      <p>Proceed to checkout and enter your shipping details. Choose your preferred payment method and review your order one last time.</p>
      <ul>
        <li>Enter or select a saved shipping address</li>
        <li>Choose from credit card, PayPal, or Apple Pay</li>
        <li>Review shipping options and costs</li>
      </ul>
      <div class="tip-box">
        <p><strong>🔒 Security:</strong> All payments are encrypted with SSL. We never store your full credit card details.</p>
      </div>
    </div>
  </div>

  <div class="step">
    <div class="step-num">4</div>
    <div class="step-content">
      <h3>Track Your Order</h3>
      <p>After placing your order, you will receive a confirmation email with your order details and tracking information.</p>
      <ul>
        <li>Track your package in real-time from your account</li>
        <li>Receive email updates at every stage</li>
        <li>Contact support if you have any delivery questions</li>
      </ul>
    </div>
  </div>

  <div class="step">
    <div class="step-num">5</div>
    <div class="step-content">
      <h3>Enjoy & Review</h3>
      <p>Once your order arrives, inspect your items and start enjoying them with your little one. Do not forget to leave a review to help other parents!</p>
      <ul>
        <li>Rate the product and share your experience</li>
        <li>Upload photos to show the product in use</li>
        <li>Earn loyalty points for every review</li>
      </ul>
    </div>
  </div>

  <div class="cta-banner">
    <h2>Ready to Start Shopping? 🍼</h2>
    <p>Discover amazing products for your little one today.</p>
    <a href="index.php" class="cta-btn"><i class="fas fa-shopping-bag"></i> Shop Now</a>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

</body>
</html