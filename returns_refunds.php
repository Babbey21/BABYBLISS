<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Returns & Refunds – BabyBliss</title>
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
    
    .policy-highlights { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 48px; }
    .highlight-card {
      background: var(--white); border-radius: 20px; padding: 28px 24px;
      text-align: center; box-shadow: 0 4px 20px var(--shadow);
    }
    .highlight-card .icon { font-size: 40px; margin-bottom: 12px; }
    .highlight-card h3 { font-size: 16px; margin-bottom: 6px; }
    .highlight-card p { font-size: 14px; color: var(--text-light); }

    .section { margin-bottom: 40px; }
    .section h2 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--text-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #F0E4DC; }
    .section p { margin-bottom: 14px; font-size: 15px; color: var(--text-mid); }
    .section ul { margin-left: 24px; margin-bottom: 16px; }
    .section ul li { margin-bottom: 8px; font-size: 15px; color: var(--text-mid); }

    .highlight-box {
      background: linear-gradient(135deg, #FFF0F3, #FFE4EA);
      border-left: 4px solid var(--rose); border-radius: 0 12px 12px 0;
      padding: 20px 24px; margin: 20px 0;
    }
    .highlight-box p { margin: 0; font-size: 14px; }

    .process-step { display: flex; gap: 16px; margin-bottom: 20px; align-items: flex-start; }
    .process-step .num {
      width: 36px; height: 36px; border-radius: 50%;
      background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 14px; flex-shrink: 0;
    }
    .process-step h4 { font-size: 16px; margin-bottom: 4px; }
    .process-step p { font-size: 14px; color: var(--text-light); margin: 0; }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .policy-highlights { grid-template-columns: 1fr; }
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
  <h1>Returns & Refunds 🔄</h1>
  <p>We want you to love every purchase. If you are not completely satisfied, our hassle-free return policy has you covered.</p>
</section>

<div class="container">

  <div class="policy-highlights">
    <div class="highlight-card">
      <div class="icon">📅</div>
      <h3>30-Day Returns</h3>
      <p>Return most items within 30 days of delivery</p>
    </div>
    <div class="highlight-card">
      <div class="icon">🆓</div>
      <h3>Free Return Shipping</h3>
      <p>On defective or incorrect items</p>
    </div>
    <div class="highlight-card">
      <div class="icon">💰</div>
      <h3>Full Refunds</h3>
      <p>Original payment method, no restocking fees</p>
    </div>
  </div>

  <div class="section">
    <h2>Return Policy Overview</h2>
    <p>At BabyBliss, we stand behind the quality of our products. If you are not satisfied with your purchase for any reason, you may return it under the following conditions:</p>
    <ul>
      <li>Items must be returned within 30 days of the delivery date.</li>
      <li>Products must be unused, unwashed, and in original packaging.</li>
      <li>All tags, labels, and accessories must be attached.</li>
      <li>Proof of purchase (order number or receipt) is required.</li>
    </ul>
    <div class="highlight-box">
      <p><strong>🎁 Gift Returns:</strong> Gift recipients can return items for store credit. The original purchaser will not be notified.</p>
    </div>
  </div>

  <div class="section">
    <h2>How to Return an Item</h2>
    <div class="process-step">
      <div class="num">1</div>
      <div>
        <h4>Start Your Return</h4>
        <p>Log into your account, go to "My Orders," and click "Return Item" next to the order you want to return.</p>
      </div>
    </div>
    <div class="process-step">
      <div class="num">2</div>
      <div>
        <h4>Select Reason & Method</h4>
        <p>Choose your return reason and whether you want a refund, exchange, or store credit.</p>
      </div>
    </div>
    <div class="process-step">
      <div class="num">3</div>
      <div>
        <h4>Print Return Label</h4>
        <p>For eligible returns, print your prepaid return label. For non-eligible returns, you are responsible for return shipping costs.</p>
      </div>
    </div>
    <div class="process-step">
      <div class="num">4</div>
      <div>
        <h4>Package & Ship</h4>
        <p>Pack the item securely in its original packaging, attach the return label, and drop it off at any authorized carrier location.</p>
      </div>
    </div>
    <div class="process-step">
      <div class="num">5</div>
      <div>
        <h4>Receive Your Refund</h4>
        <p>Once we receive and inspect your return, your refund will be processed within 5-10 business days.</p>
      </div>
    </div>
  </div>

  <div class="section">
    <h2>Refund Details</h2>
    <p>Refunds are issued to the original payment method used at checkout. Here is what to expect:</p>
    <ul>
      <li><strong>Credit/Debit Cards:</strong> 5-10 business days to appear on your statement.</li>
      <li><strong>PayPal:</strong> 3-5 business days to your PayPal account.</li>
      <li><strong>Store Credit:</strong> Immediately available in your BabyBliss account.</li>
      <li><strong>Gift Cards:</strong> Issued as a new digital gift card via email.</li>
    </ul>
    <p>Original shipping charges are refunded only if the return is due to our error (wrong item, defective product). Otherwise, return shipping costs are the customer's responsibility unless the item qualifies for free return shipping.</p>
  </div>

  <div class="section">
    <h2>Non-Returnable Items</h2>
    <p>The following items cannot be returned unless they arrive damaged or defective:</p>
    <ul>
      <li>Personalized or custom-made items</li>
      <li>Items marked "Final Sale" or "Non-Returnable"</li>
      <li>Gift cards and digital products</li>
      <li>Intimate apparel (for hygiene reasons)</li>
      <li>Items that have been used, washed, or altered</li>
      <li>Items missing original packaging, tags, or accessories</li>
    </ul>
  </div>

  <div class="section">
    <h2>Exchanges</h2>
    <p>We offer exchanges for different sizes or colors of the same item, subject to availability. To request an exchange:</p>
    <ul>
      <li>Start a return and select "Exchange" as your preferred resolution.</li>
      <li>Choose the new size or color you want.</li>
      <li>We will ship the replacement once we receive your returned item.</li>
    </ul>
    <p>If the desired item is out of stock, we will issue a refund or store credit instead.</p>
  </div>

  <div class="section">
    <h2>Damaged or Defective Items</h2>
    <p>If your item arrives damaged or defective, please contact us within 7 days of delivery. We will:</p>
    <ul>
      <li>Send a prepaid return label at no cost to you.</li>
      <li>Process a full refund including original shipping.</li>
      <li>Or ship a replacement immediately (your choice).</li>
    </ul>
    <p>Please take photos of the damage and keep all original packaging for our records.</p>
  </div>

  <div class="highlight-box">
    <p><strong>💚 Need Help?</strong> If you have questions about your return or need assistance, our support team is here to help. <a href="contact.php" style="color:var(--deep-rose);font-weight:600;">Contact us</a> anytime.</p>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

</body>
</html>