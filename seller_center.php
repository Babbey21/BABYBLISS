<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Seller Center – BabyBliss</title>
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

    .hero { background: linear-gradient(135deg, #6678CC, #4455AA); padding: 60px 48px; text-align: center; color: var(--white); }
    .hero h1 { font-family: 'Playfair Display', serif; font-size: 42px; margin-bottom: 12px; }
    .hero p { font-size: 16px; opacity: 0.85; max-width: 600px; margin: 0 auto; }

    .container { max-width: 1100px; margin: 0 auto; padding: 48px 24px; }
    
    .dashboard-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 48px; }
    .dash-card {
      background: var(--white); border-radius: 20px; padding: 28px 20px;
      text-align: center; box-shadow: 0 4px 20px var(--shadow);
      transition: all 0.3s;
    }
    .dash-card:hover { transform: translateY(-4px); }
    .dash-card .icon { font-size: 36px; margin-bottom: 12px; }
    .dash-card .value { font-size: 32px; font-weight: 700; color: var(--deep-rose); }
    .dash-card .label { font-size: 13px; color: var(--text-light); margin-top: 4px; }

    .section { margin-bottom: 40px; }
    .section h2 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--text-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #F0E4DC; }
    .section p { margin-bottom: 14px; font-size: 15px; color: var(--text-mid); }
    .section ul { margin-left: 24px; margin-bottom: 16px; }
    .section ul li { margin-bottom: 8px; font-size: 15px; color: var(--text-mid); }

    .tool-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .tool-card {
      background: var(--white); border-radius: 16px; padding: 24px;
      box-shadow: 0 2px 12px var(--shadow); cursor: pointer;
      border: 2px solid transparent; transition: all 0.2s;
    }
    .tool-card:hover { border-color: var(--rose); transform: translateY(-2px); }
    .tool-card .icon { font-size: 28px; margin-bottom: 10px; }
    .tool-card h4 { font-size: 16px; margin-bottom: 6px; }
    .tool-card p { font-size: 13px; color: var(--text-light); margin: 0; }

    .highlight-box {
      background: linear-gradient(135deg, #E8F0FF, #D4E0FF);
      border-left: 4px solid #6678CC; border-radius: 0 12px 12px 0;
      padding: 20px 24px; margin: 20px 0;
    }
    .highlight-box p { margin: 0; font-size: 14px; }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .dashboard-grid { grid-template-columns: repeat(2, 1fr); }
      .tool-grid { grid-template-columns: 1fr; }
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
  <h1>Seller Center 📊</h1>
  <p>Your one-stop hub for managing your BabyBliss seller account, products, orders, and growth.</p>
</section>

<div class="container">

  <div class="dashboard-grid">
    <div class="dash-card">
      <div class="icon">📦</div>
      <div class="value">1,247</div>
      <div class="label">Total Orders</div>
    </div>
    <div class="dash-card">
      <div class="icon">💰</div>
      <div class="value">$24.5K</div>
      <div class="label">Total Revenue</div>
    </div>
    <div class="dash-card">
      <div class="icon">⭐</div>
      <div class="value">4.8</div>
      <div class="label">Average Rating</div>
    </div>
    <div class="dash-card">
      <div class="icon">👀</div>
      <div class="value">8.2K</div>
      <div class="label">Product Views</div>
    </div>
  </div>

  <div class="section">
    <h2>Quick Access Tools</h2>
    <div class="tool-grid">
      <div class="tool-card" onclick="alert('Coming soon: Product Management Dashboard')">
        <div class="icon">📝</div>
        <h4>Manage Products</h4>
        <p>Add, edit, and organize your product listings</p>
      </div>
      <div class="tool-card" onclick="alert('Coming soon: Orders Dashboard')">
        <div class="icon">📋</div>
        <h4>View Orders</h4>
        <p>Track and manage all incoming orders</p>
      </div>
      <div class="tool-card" onclick="alert('Coming soon: Analytics Dashboard')">
        <div class="icon">📈</div>
        <h4>Analytics</h4>
        <p>Sales reports, trends, and customer insights</p>
      </div>
      <div class="tool-card" onclick="alert('Coming soon: Inventory Management')">
        <div class="icon">📦</div>
        <h4>Inventory</h4>
        <p>Track stock levels and set alerts</p>
      </div>
      <div class="tool-card" onclick="alert('Coming soon: Messages')">
        <div class="icon">💬</div>
        <h4>Messages</h4>
        <p>Communicate with buyers and support</p>
      </div>
      <div class="tool-card" onclick="alert('Coming soon: Promotions')">
        <div class="icon">🏷️</div>
        <h4>Promotions</h4>
        <p>Create coupons, sales, and featured listings</p>
      </div>
    </div>
  </div>

  <div class="section">
    <h2>Seller Guidelines</h2>
    <p>To maintain a trusted marketplace, all sellers must adhere to the following guidelines:</p>
    <ul>
      <li><strong>Product Accuracy:</strong> Ensure all product descriptions, images, and specifications are accurate and up-to-date.</li>
      <li><strong>Shipping Timeliness:</strong> Ship orders within 1-2 business days and provide valid tracking information.</li>
      <li><strong>Customer Communication:</strong> Respond to buyer inquiries within 24 hours during business days.</li>
      <li><strong>Quality Standards:</strong> All products must meet applicable safety standards and regulations.</li>
      <li><strong>Prohibited Items:</strong> Do not list counterfeit, recalled, or prohibited products.</li>
      <li><strong>Return Policy:</strong> Honor BabyBliss return policies and process refunds promptly.</li>
    </ul>
    <div class="highlight-box">
      <p><strong>📚 Resources:</strong> Download our <a href="#" style="color:#6678CC;font-weight:600;">Seller Handbook</a> for detailed guides on listing optimization, photography tips, and marketing strategies.</p>
    </div>
  </div>

  <div class="section">
    <h2>Performance Metrics</h2>
    <p>Your seller performance is evaluated based on the following key metrics:</p>
    <ul>
      <li><strong>Order Defect Rate:</strong> Keep below 1% (includes negative feedback, disputes, and chargebacks).</li>
      <li><strong>On-Time Shipping:</strong> Ship 95%+ of orders within your stated handling time.</li>
      <li><strong>Response Time:</strong> Respond to buyer messages within 24 hours.</li>
      <li><strong>Customer Rating:</strong> Maintain an average rating of 4.0 stars or higher.</li>
    </ul>
    <p>Sellers who consistently meet or exceed these standards may qualify for the "Top Rated Seller" badge, which increases visibility and buyer trust.</p>
  </div>

  <div class="section">
    <h2>Getting Paid</h2>
    <p>Payments are processed securely through our platform and deposited to your linked bank account:</p>
    <ul>
      <li><strong>Payout Schedule:</strong> Weekly payouts every Tuesday for the previous week's sales.</li>
      <li><strong>Minimum Payout:</strong> $25 minimum balance required for automatic payout.</li>
      <li><strong>Commission:</strong> 8-15% commission depending on product category.</li>
      <li><strong>Payment Methods:</strong> Direct deposit to US bank accounts or PayPal for international sellers.</li>
    </ul>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

</body>
</html>