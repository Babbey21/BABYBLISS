<?php
session_start();

$success = "";
$error = "";

if (isset($_POST['submit_report'])) {
    $reporter_name = htmlspecialchars(trim($_POST['reporter_name']));
    $reporter_email = htmlspecialchars(trim($_POST['reporter_email']));
    $product_name = htmlspecialchars(trim($_POST['product_name']));
    $product_id = htmlspecialchars(trim($_POST['product_id']));
    $report_type = htmlspecialchars(trim($_POST['report_type']));
    $report_reason = htmlspecialchars(trim($_POST['report_reason']));
    
    if (empty($reporter_name) || empty($reporter_email) || empty($product_name) || empty($report_type) || empty($report_reason)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($reporter_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $success = "Thank you for your report. Our team will review it within 24-48 hours. 🔍";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Report a Product – BabyBliss</title>
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

    .hero { background: linear-gradient(135deg, #E8A800, #C88A00); padding: 60px 48px; text-align: center; color: var(--white); }
    .hero h1 { font-family: 'Playfair Display', serif; font-size: 42px; margin-bottom: 12px; }
    .hero p { font-size: 16px; opacity: 0.85; max-width: 600px; margin: 0 auto; }

    .container { max-width: 900px; margin: 0 auto; padding: 48px 24px; }
    
    .alert {
      padding: 14px 20px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; font-weight: 600;
      display: flex; align-items: center; gap: 10px;
    }
    .alert-success { background: #EAF8F4; border: 1px solid #B0E0D0; color: #2E7D62; }
    .alert-error { background: #FFF0F3; border: 1px solid #F5B8C8; color: var(--deep-rose); }

    .section { margin-bottom: 40px; }
    .section h2 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--text-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #F0E4DC; }
    .section p { margin-bottom: 14px; font-size: 15px; color: var(--text-mid); }
    .section ul { margin-left: 24px; margin-bottom: 16px; }
    .section ul li { margin-bottom: 8px; font-size: 15px; color: var(--text-mid); }

    .report-types { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 32px; }
    .report-type-card {
      background: var(--white); border-radius: 16px; padding: 24px;
      box-shadow: 0 2px 12px var(--shadow); cursor: pointer;
      border: 2px solid transparent; transition: all 0.2s;
    }
    .report-type-card:hover, .report-type-card.active {
      border-color: var(--rose); transform: translateY(-2px);
    }
    .report-type-card .icon { font-size: 32px; margin-bottom: 12px; }
    .report-type-card h4 { font-size: 16px; margin-bottom: 6px; }
    .report-type-card p { font-size: 13px; color: var(--text-light); margin: 0; }

    .form-card {
      background: var(--white); border-radius: 20px; padding: 40px;
      box-shadow: 0 4px 24px var(--shadow);
    }
    .form-card h3 { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 8px; }
    .form-card > p { color: var(--text-light); margin-bottom: 24px; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--text-mid); margin-bottom: 7px; }
    .form-group label span { font-weight: 400; color: var(--text-light); }
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

    .highlight-box {
      background: linear-gradient(135deg, #FFF8E0, #FFE8A8);
      border-left: 4px solid var(--gold); border-radius: 0 12px 12px 0;
      padding: 20px 24px; margin: 20px 0;
    }
    .highlight-box p { margin: 0; font-size: 14px; }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .report-types { grid-template-columns: 1fr; }
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
  <h1>Report a Product 🚨</h1>
  <p>Help us keep BabyBliss safe for everyone. Report products that violate our policies or pose safety concerns.</p>
</section>

<div class="container">

  <div class="section">
    <h2>Why Report a Product?</h2>
    <p>Your reports help us maintain a safe and trustworthy marketplace for parents and children. We take every report seriously and investigate promptly.</p>
    <ul>
      <li><strong>Safety First:</strong> Protect children from unsafe or recalled products.</li>
      <li><strong>Quality Assurance:</strong> Ensure all products meet our quality standards.</li>
      <li><strong>Policy Enforcement:</strong> Remove counterfeit, prohibited, or misleading items.</li>
      <li><strong>Community Trust:</strong> Maintain honest and reliable shopping experience.</li>
    </ul>
  </div>

  <div class="section">
    <h2>What Can You Report?</h2>
    <div class="report-types">
      <div class="report-type-card" onclick="selectReportType('counterfeit')">
        <div class="icon">🎭</div>
        <h4>Counterfeit Product</h4>
        <p>Fake or imitation items pretending to be genuine brands</p>
      </div>
      <div class="report-type-card" onclick="selectReportType('unsafe')">
        <div class="icon">⚠️</div>
        <h4>Unsafe for Children</h4>
        <p>Products with safety hazards, recalls, or age-inappropriate items</p>
      </div>
      <div class="report-type-card" onclick="selectReportType('misleading')">
        <div class="icon">📸</div>
        <h4>Misleading Information</h4>
        <p>False descriptions, fake reviews, or inaccurate product images</p>
      </div>
      <div class="report-type-card" onclick="selectReportType('prohibited')">
        <div class="icon">🚫</div>
        <h4>Prohibited Item</h4>
        <p>Items that violate our prohibited products policy</p>
      </div>
    </div>
  </div>

  <div class="highlight-box">
    <p><strong>🔒 Confidential:</strong> All reports are handled confidentially. We do not share your identity with the seller or other third parties without your consent, except as required by law.</p>
  </div>

  <div class="form-card">
    <h3>Submit a Report 📝</h3>
    <p>Provide as much detail as possible to help us investigate effectively.</p>
    
    <?php if($success): ?>
      <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
    <?php endif; ?>
    <?php if($error): ?>
      <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
    <?php endif; ?>

    <form action="report.php" method="POST" enctype="multipart/form-data">
      <div class="form-row">
        <div class="form-group">
          <label>Your Name *</label>
          <input type="text" name="reporter_name" placeholder="John Doe" required/>
        </div>
        <div class="form-group">
          <label>Your Email *</label>
          <input type="email" name="reporter_email" placeholder="john@example.com" required/>
        </div>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label>Product Name *</label>
          <input type="text" name="product_name" placeholder="e.g. Cuddle Bear Plush" required/>
        </div>
        <div class="form-group">
          <label>Product ID <span>(if available)</span></label>
          <input type="text" name="product_id" placeholder="BB-12345678"/>
        </div>
      </div>
      
      <div class="form-group">
        <label>Report Type *</label>
        <select name="report_type" id="reportType" required>
          <option value="">Select report type...</option>
          <option value="counterfeit">Counterfeit Product</option>
          <option value="unsafe">Unsafe for Children</option>
          <option value="misleading">Misleading Information</option>
          <option value="prohibited">Prohibited Item</option>
          <option value="inappropriate">Inappropriate Content</option>
          <option value="price_gouging">Price Gouging</option>
          <option value="other">Other</option>
        </select>
      </div>
      
      <div class="form-group">
        <label>Detailed Reason *</label>
        <textarea name="report_reason" placeholder="Please describe the issue in detail. What makes this product problematic? Include any specific concerns about safety, authenticity, or accuracy." required></textarea>
      </div>
      
      <div class="form-group">
        <label>Evidence Upload <span>(optional - screenshots, photos, documents)</span></label>
        <input type="file" name="evidence[]" multiple accept="image/*,.pdf,.doc,.docx" style="padding: 10px;"/>
      </div>
      
      <button type="submit" name="submit_report" class="btn-submit">
        <i class="fas fa-flag"></i> Submit Report
      </button>
    </form>
  </div>

  <div class="section" style="margin-top: 40px;">
    <h2>What Happens Next?</h2>
    <ul>
      <li><strong>Acknowledgment:</strong> You will receive an email confirmation within minutes.</li>
      <li><strong>Review:</strong> Our Trust & Safety team reviews all reports within 24-48 hours.</li>
      <li><strong>Investigation:</strong> We may contact you for additional information if needed.</li>
      <li><strong>Action:</strong> If the report is validated, we will take appropriate action, which may include removing the product, suspending the seller, or reporting to authorities.</li>
      <li><strong>Follow-up:</strong> You will be notified of the outcome of your report.</li>
    </ul>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

<script>
  function selectReportType(type) {
    document.getElementById('reportType').value = type;
    document.querySelectorAll('.report-type-card').forEach(card => card.classList.remove('active'));
    event.currentTarget.classList.add('active');
  }
</script>

</body>
</html>'