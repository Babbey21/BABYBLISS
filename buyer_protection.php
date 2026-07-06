<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Buyer Protection – BabyBliss</title>
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
    
    .guarantee-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 48px; }
    .guarantee-card {
      background: var(--white); border-radius: 20px; padding: 32px 24px;
      text-align: center; box-shadow: 0 4px 20px var(--shadow);
      transition: all 0.3s; border: 2px solid transparent;
    }
    .guarantee-card:hover { transform: translateY(-4px); border-color: var(--mint-dark); }
    .guarantee-card .icon { font-size: 48px; margin-bottom: 16px; }
    .guarantee-card h3 { font-size: 18px; margin-bottom: 10px; }
    .guarantee-card p { font-size: 14px; color: var(--text-light); }

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
      .guarantee-cards { grid-template-columns: 1fr; }
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
  <h1>Buyer Protection 🛡️</h1>
  <p>Shop with confidence knowing that every purchase on BabyBliss is protected by our comprehensive buyer protection program.</p>
</section>

<div class="container">

  <div class="guarantee-cards">
    <div class="guarantee-card">
      <div class="icon">🛡️</div>
      <h3>Full Refund Guarantee</h3>
      <p>If your item never arrives or is significantly not as described, you are eligible for a full refund.</p>
    </div>
    <div class="guarantee-card">
      <div class="icon">🔒</div>
      <h3>Secure Payments</h3>
      <p>Your payment information is encrypted and processed through PCI-DSS compliant providers.</p>
    </div>
    <div class="guarantee-card">
      <div class="icon">📦</div>
      <h3>Delivery Protection</h3>
      <p>We monitor all shipments and step in if your package is lost, damaged, or delayed.</p>
    </div>
  </div>

  <div class="section">
    <h2>What Is Covered</h2>
    <p>Our Buyer Protection program covers you in the following situations:</p>
    <ul>
      <li><strong>Item Not Received:</strong> You paid for an item but never received it within the estimated delivery timeframe.</li>
      <li><strong>Item Not As Described:</strong> The item you received is significantly different from the seller's description (wrong size, color, model, or condition).</li>
      <li><strong>Damaged Item:</strong> The item arrived damaged due to shipping or packaging issues.</li>
      <li><strong>Counterfeit Product:</strong> The item is proven to be a fake or imitation of the advertised brand.</li>
      <li><strong>Missing Parts:</strong> The item is incomplete and missing essential components listed in the description.</li>
    </ul>
  </div>

  <div class="section">
    <h2>How to File a Claim</h2>
    <p>If you encounter any of the issues above, follow these steps to file a Buyer Protection claim:</p>
    <ul>
      <li><strong>Step 1:</strong> Log into your account and go to "My Orders."</li>
      <li><strong>Step 2:</strong> Find the order in question and click "Report an Issue."</li>
      <li><strong>Step 3:</strong> Select the problem type and provide detailed information.</li>
      <li><strong>Step 4:</strong> Upload any supporting evidence (photos, tracking info, communication).</li>
      <li><strong>Step 5:</strong> Our team will review your claim within 3-5 business days and contact you with a resolution.</li>
    </ul>
    <div class="highlight-box">
      <p><strong>⏰ Important:</strong> Claims must be filed within 30 days of the estimated delivery date or within 7 days of receiving a damaged/incorrect item.</p>
    </div>
  </div>

  <div class="section">
    <h2>Refund Process</h2>
    <p>Once your claim is approved, refunds are processed as follows:</p>
    <ul>
      <li><strong>Full Refund:</strong> The entire purchase amount, including original shipping, is refunded to your original payment method.</li>
      <li><strong>Partial Refund:</strong> A portion of the purchase price is refunded if the item has minor issues that do not warrant a full return.</li>
      <li><strong>Replacement:</strong> We can arrange for a replacement item to be shipped to you at no additional cost.</li>
      <li><strong>Return Required:</strong> In some cases, you may need to return the item before receiving a refund. We provide a prepaid return label.</li>
    </ul>
    <p>Refunds typically appear in your account within 5-10 business days, depending on your payment provider.</p>
  </div>

  <div class="section">
    <h2>What Is Not Covered</h2>
    <p>Our Buyer Protection does not cover the following:</p>
    <ul>
      <li>Buyer's remorse or change of mind (see our standard return policy for these cases)</li>
      <li>Items damaged due to misuse, negligence, or normal wear and tear</li>
      <li>Items purchased outside of BabyBliss platform</li>
      <li>Digital goods or services not delivered due to incorrect email provided by buyer</li>
      <li>Items that violate our prohibited products policy</li>
    </ul>
  </div>

  <div class="highlight-box">
    <p><strong>💚 Our Promise:</strong> At BabyBliss, your trust is our top priority. We are committed to ensuring every parent shops with peace of mind. If you have any questions about our Buyer Protection program, please <a href="contact.php" style="color:var(--deep-rose);font-weight:600;">contact our support team</a>.</p>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

</body>
</html>