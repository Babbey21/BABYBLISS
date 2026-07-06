<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Seller Protection – BabyBliss</title>
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

    .hero { background: linear-gradient(135deg, var(--mint-dark), #3A9E88); padding: 60px 48px; text-align: center; color: var(--white); }
    .hero h1 { font-family: 'Playfair Display', serif; font-size: 42px; margin-bottom: 12px; }
    .hero p { font-size: 16px; opacity: 0.85; max-width: 600px; margin: 0 auto; }

    .container { max-width: 900px; margin: 0 auto; padding: 48px 24px; }
    
    .protection-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 48px; }
    .protection-card {
      background: var(--white); border-radius: 20px; padding: 32px 24px;
      text-align: center; box-shadow: 0 4px 20px var(--shadow);
      transition: all 0.3s; border: 2px solid transparent;
    }
    .protection-card:hover { transform: translateY(-4px); border-color: var(--mint-dark); }
    .protection-card .icon { font-size: 44px; margin-bottom: 14px; }
    .protection-card h3 { font-size: 17px; margin-bottom: 10px; }
    .protection-card p { font-size: 14px; color: var(--text-light); }

    .section { margin-bottom: 40px; }
    .section h2 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--text-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #F0E4DC; }
    .section p { margin-bottom: 14px; font-size: 15px; color: var(--text-mid); }
    .section ul { margin-left: 24px; margin-bottom: 16px; }
    .section ul li { margin-bottom: 8px; font-size: 15px; color: var(--text-mid); }

    .highlight-box {
      background: linear-gradient(135deg, #E8F8F5, #D4F5EC);
      border-left: 4px solid var(--mint-dark); border-radius: 0 12px 12px 0;
      padding: 20px 24px; margin: 20px 0;
    }
    .highlight-box p { margin: 0; font-size: 14px; }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .protection-cards { grid-template-columns: 1fr; }
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
  <h1>Seller Protection 🛡️</h1>
  <p>We have got your back. Our Seller Protection program safeguards you against unfair claims, chargebacks, and fraudulent activity.</p>
</section>

<div class="container">

  <div class="protection-cards">
    <div class="protection-card">
      <div class="icon">🛡️</div>
      <h3>Chargeback Protection</h3>
      <p>We cover eligible chargebacks so you do not lose money on fraudulent claims</p>
    </div>
    <div class="protection-card">
      <div class="icon">🔍</div>
      <h3>Fraud Detection</h3>
      <p>Advanced AI monitors transactions to flag suspicious activity before it affects you</p>
    </div>
    <div class="protection-card">
      <div class="icon">⚖️</div>
      <h3>Fair Dispute Resolution</h3>
      <p>Neutral review process ensures both buyer and seller are heard fairly</p>
    </div>
  </div>

  <div class="section">
    <h2>What Is Protected</h2>
    <p>Our Seller Protection program covers you in the following situations, provided you meet our seller requirements:</p>
    <ul>
      <li><strong>Unauthorized Payment Claims:</strong> When a buyer claims they did not authorize the payment, but you shipped to the verified address.</li>
      <li><strong>Item Not Received (INR) Claims:</strong> When a buyer claims they never received the item, but you provided valid tracking showing delivery.</li>
      <li><strong>Significantly Not As Described (SNAD) Claims:</strong> When a buyer's claim is found to be unsubstantiated after review.</li>
      <li><strong>Fraudulent Chargebacks:</strong> When a buyer initiates a chargeback for reasons covered by our protection policy.</li>
      <li><strong>Return Fraud:</strong> When a buyer returns an item different from what was originally shipped.</li>
    </ul>
  </div>

  <div class="section">
    <h2>Eligibility Requirements</h2>
    <p>To qualify for Seller Protection, you must meet the following criteria:</p>
    <ul>
      <li>Ship to the address provided in the order confirmation (never to an alternate address requested by the buyer).</li>
      <li>Use a shipping method with tracking and delivery confirmation.</li>
      <li>Ship the item within your stated handling time.</li>
      <li>Maintain accurate product descriptions and images.</li>
      <li>Respond to buyer inquiries and disputes within 24 hours.</li>
      <li>Keep detailed records of all transactions, communications, and shipping documentation.</li>
    </ul>
    <div class="highlight-box">
      <p><strong>✅ Pro Tip:</strong> Always photograph items before shipping, especially high-value products. This documentation can be crucial in dispute resolution.</p>
    </div>
  </div>

  <div class="section">
    <h2>How Protection Works</h2>
    <p>When a claim or chargeback is filed against you, here is what happens:</p>
    <ul>
      <li><strong>Notification:</strong> You receive an immediate email alert with details of the claim.</li>
      <li><strong>Evidence Submission:</strong> You have 5 business days to submit tracking info, photos, communications, and any other relevant evidence.</li>
      <li><strong>Review:</strong> Our Dispute Resolution Team reviews all evidence within 3-5 business days.</li>
      <li><strong>Resolution:</strong> If the claim is found in your favor, the funds are released back to you and the claim is closed.</li>
    </ul>
    <p>If the claim is not covered by Seller Protection, you may still appeal the decision within 10 days with additional evidence.</p>
  </div>

  <div class="section">
    <h2>What Is Not Covered</h2>
    <p>Seller Protection does not apply in the following situations:</p>
    <ul>
      <li>Items shipped to an address other than the one on the order.</li>
      <li>Items that violate our prohibited products policy.</li>
      <li>Sellers with a history of policy violations or poor performance metrics.</li>
      <li>Claims where valid tracking or delivery confirmation is not provided.</li>
      <li>Items described inaccurately or with misleading images.</li>
      <li>Digital goods or services not eligible for tracking.</li>
    </ul>
  </div>

  <div class="section">
    <h2>Fraud Prevention Tips</h2>
    <p>Protect yourself by following these best practices:</p>
    <ul>
      <li>Verify suspicious orders by contacting the buyer before shipping.</li>
      <li>Require signature confirmation for orders over $200.</li>
      <li>Be cautious of buyers requesting shipping to a different address.</li>
      <li>Keep all packaging and shipping receipts for at least 6 months.</li>
      <li>Report suspicious buyer behavior to our Trust & Safety team immediately.</li>
    </ul>
  </div>

  <div class="highlight-box">
    <p><strong>💚 We Are Here for You:</strong> Our dedicated Seller Protection team is available to assist you with any concerns. <a href="contact.php" style="color:var(--deep-rose);font-weight:600;">Contact us</a> anytime for support.</p>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

</body>
</html>