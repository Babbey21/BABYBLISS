'<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dispute Resolution – BabyBliss</title>
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

    .container { max-width: 900px; margin: 0 auto; padding: 48px 24px; }
    
    .process-steps { display: flex; justify-content: space-between; margin-bottom: 48px; position: relative; }
    .process-steps::before {
      content: ''; position: absolute; top: 24px; left: 10%; right: 10%; height: 3px;
      background: linear-gradient(90deg, var(--rose), var(--mint-dark));
    }
    .step { text-align: center; position: relative; z-index: 1; flex: 1; }
    .step-num {
      width: 50px; height: 50px; border-radius: 50%; background: var(--white);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 12px; font-weight: 700; color: var(--deep-rose);
      box-shadow: 0 4px 16px var(--shadow); border: 3px solid var(--rose);
    }
    .step h4 { font-size: 14px; font-weight: 600; color: var(--text-dark); }

    .section { margin-bottom: 40px; }
    .section h2 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--text-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #F0E4DC; }
    .section p { margin-bottom: 14px; font-size: 15px; color: var(--text-mid); }
    .section ul { margin-left: 24px; margin-bottom: 16px; }
    .section ul li { margin-bottom: 8px; font-size: 15px; color: var(--text-mid); }

    .highlight-box {
      background: linear-gradient(135deg, #E8F0FF, #D4E0FF);
      border-left: 4px solid #6678CC; border-radius: 0 12px 12px 0;
      padding: 20px 24px; margin: 20px 0;
    }
    .highlight-box p { margin: 0; font-size: 14px; }

    .form-card {
      background: var(--white); border-radius: 20px; padding: 40px;
      box-shadow: 0 4px 24px var(--shadow); margin-top: 32px;
    }
    .form-card h3 { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 8px; }
    .form-card p { color: var(--text-light); margin-bottom: 24px; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text-mid); margin-bottom: 7px; }
    .form-group input, .form-group select, .form-group textarea {
      width: 100%; padding: 13px 16px; border: 2px solid #F0E4DC; border-radius: 12px;
      font-family: 'DM Sans', sans-serif; font-size: 15px; color: var(--text-dark);
      outline: none; transition: all 0.2s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
      border-color: var(--rose); box-shadow: 0 0 0 4px rgba(232,115,138,0.1);
    }
    .form-group textarea { resize: vertical; min-height: 120px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .btn-submit {
      width: 100%; padding: 16px; background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); border: none; border-radius: 14px; font-size: 16px; font-weight: 700;
      cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
      transition: all 0.25s;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(196,77,101,0.35); }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .process-steps { flex-direction: column; gap: 24px; }
      .process-steps::before { display: none; }
      .form-row { grid-template-columns: 1fr; }
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
  <h1>Dispute Resolution ⚖️</h1>
  <p>We are committed to resolving any issues fairly and efficiently. Learn about our dispute process and how to file a claim.</p>
</section>

<div class="container">

  <div class="process-steps">
    <div class="step">
      <div class="step-num">1</div>
      <h4>Contact Support</h4>
    </div>
    <div class="step">
      <div class="step-num">2</div>
      <h4>Internal Review</h4>
    </div>
    <div class="step">
      <div class="step-num">3</div>
      <h4>Mediation</h4>
    </div>
    <div class="step">
      <div class="step-num">4</div>
      <h4>Resolution</h4>
    </div>
  </div>

  <div class="section">
    <h2>Our Commitment to Fair Resolution</h2>
    <p>At BabyBliss, we believe that every concern deserves a fair hearing. Whether you are a buyer experiencing an issue with an order or a seller facing a transaction dispute, we are here to help resolve the matter quickly and fairly.</p>
    <p>Our dispute resolution process is designed to be transparent, efficient, and accessible. We encourage all parties to communicate openly and provide complete information to facilitate a swift resolution.</p>
  </div>

  <div class="section">
    <h2>Types of Disputes We Handle</h2>
    <ul>
      <li><strong>Order Issues:</strong> Items not received, damaged goods, wrong items shipped, or items not as described.</li>
      <li><strong>Payment Disputes:</strong> Unauthorized charges, duplicate billing, or refund disagreements.</li>
      <li><strong>Quality Concerns:</strong> Product defects, safety issues, or dissatisfaction with product quality.</li>
      <li><strong>Return & Refund Disputes:</strong> Disagreements about return eligibility, refund amounts, or restocking fees.</li>
      <li><strong>Account Issues:</strong> Unauthorized account access, suspension disputes, or verification problems.</li>
    </ul>
  </div>

  <div class="section">
    <h2>Resolution Process</h2>
    <h3>Step 1: Contact Customer Support</h3>
    <p>Before filing a formal dispute, please contact our support team through the Help Center or by emailing support@babybliss.com. Many issues can be resolved quickly through direct communication. Please provide your order number, a clear description of the issue, and any supporting evidence (photos, receipts, etc.).</p>
    
    <h3>Step 2: Internal Review</h3>
    <p>If the issue cannot be resolved through initial contact, our dedicated Dispute Resolution Team will conduct a thorough review. This typically takes 3-5 business days. We may request additional documentation from both parties to make an informed decision.</p>
    
    <h3>Step 3: Mediation</h3>
    <p>For complex disputes, we offer voluntary mediation services. A neutral mediator will work with both parties to reach a mutually acceptable solution. Mediation is confidential, non-binding, and free of charge.</p>
    
    <h3>Step 4: Final Resolution</h3>
    <p>If mediation is unsuccessful or declined, BabyBliss will make a final determination based on all available evidence, our Terms of Service, and applicable consumer protection laws. Our decision is final and binding for transactions under $500.</p>
  </div>

  <div class="highlight-box">
    <p><strong>⏱️ Timeline:</strong> Most disputes are resolved within 7-10 business days. Complex cases may take up to 30 days. We will keep you updated throughout the process via email.</p>
  </div>

  <div class="section">
    <h2>Your Rights</h2>
    <p>You always retain the right to pursue legal remedies outside of our dispute process. Nothing in this policy limits your statutory rights as a consumer under applicable law. If you are not satisfied with our resolution, you may escalate the matter to:</p>
    <ul>
      <li>Your local consumer protection agency</li>
      <li>The Better Business Bureau (BBB)</li>
      <li>Small claims court for disputes under the jurisdictional limit</li>
      <li>Binding arbitration for disputes exceeding $500 (by mutual agreement)</li>
    </ul>
  </div>

  <div class="form-card">
    <h3>File a Dispute 📝</h3>
    <p>Please complete the form below with accurate details. Fields marked with * are required.</p>
    <form action="dispute.php" method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" name="full_name" placeholder="John Doe" required/>
        </div>
        <div class="form-group">
          <label>Email Address *</label>
          <input type="email" name="email" placeholder="john@example.com" required/>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Order Number *</label>
          <input type="text" name="order_number" placeholder="BB-12345678" required/>
        </div>
        <div class="form-group">
          <label>Dispute Type *</label>
          <select name="dispute_type" required>
            <option value="">Select type...</option>
            <option value="not_received">Item Not Received</option>
            <option value="damaged">Damaged/Defective Item</option>
            <option value="wrong_item">Wrong Item Received</option>
            <option value="not_as_described">Not As Described</option>
            <option value="refund_issue">Refund Issue</option>
            <option value="payment">Payment Dispute</option>
            <option value="other">Other</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Describe Your Issue *</label>
        <textarea name="description" placeholder="Please describe your issue in detail. Include dates, amounts, and any steps you've already taken to resolve it." required></textarea>
      </div>
      <div class="form-group">
        <label>Desired Resolution</label>
        <select name="resolution">
          <option value="">What outcome are you seeking?</option>
          <option value="refund">Full Refund</option>
          <option value="partial_refund">Partial Refund</option>
          <option value="replacement">Replacement Item</option>
          <option value="exchange">Exchange</option>
          <option value="other">Other</option>
        </select>
      </div>
      <button type="submit" name="submit_dispute" class="btn-submit">
        <i class="fas fa-paper-plane"></i> Submit Dispute
      </button>
    </form>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

</body>
</html>