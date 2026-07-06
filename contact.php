<?php
session_start();

$success = "";
$error = "";

if (isset($_POST['submit_contact'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $success = "Thank you for contacting us! We will respond within 24 hours. 💌";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us – BabyBliss</title>
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

    .hero { background: linear-gradient(135deg, var(--deep-rose), #A03050); padding: 80px 48px 120px; text-align: center; color: var(--white); position: relative; overflow: hidden; }
    .hero::before { content: ''; position: absolute; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); top: -100px; right: -100px; }
    .hero::after { content: ''; position: absolute; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%); bottom: -80px; left: -80px; }
    .hero h1 { font-family: 'Playfair Display', serif; font-size: 48px; margin-bottom: 12px; position: relative; z-index: 1; }
    .hero p { font-size: 17px; opacity: 0.85; max-width: 600px; margin: 0 auto; position: relative; z-index: 1; }

    .container { max-width: 1200px; margin: 0 auto; padding: 0 24px 48px; }

    .main-card {
      background: var(--white); border-radius: 32px;
      box-shadow: 0 20px 60px rgba(30,8,16,0.12);
      margin-top: -60px; position: relative; z-index: 10;
      overflow: hidden; display: grid; grid-template-columns: 380px 1fr;
    }

    /* Left Panel - Contact Info */
    .left-panel {
      background: linear-gradient(180deg, #1E0F0A 0%, #2E1219 100%);
      padding: 48px 36px; color: var(--white); position: relative; overflow: hidden;
    }
    .left-panel::before {
      content: ''; position: absolute; width: 200px; height: 200px;
      background: radial-gradient(circle, rgba(232,115,138,0.2) 0%, transparent 70%);
      top: -60px; right: -60px;
    }
    .left-panel h3 { font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 8px; position: relative; z-index: 1; }
    .left-panel > p { opacity: 0.7; font-size: 14px; margin-bottom: 36px; position: relative; z-index: 1; }

    .contact-method { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 28px; position: relative; z-index: 1; }
    .contact-method .icon-wrap {
      width: 44px; height: 44px; border-radius: 12px;
      background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }
    .contact-method h4 { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
    .contact-method p { font-size: 13px; opacity: 0.65; margin: 0; }
    .contact-method a { color: var(--blush); text-decoration: none; font-size: 14px; font-weight: 500; }

    .social-row { display: flex; gap: 10px; margin-top: 40px; position: relative; z-index: 1; }
    .social-row a {
      width: 40px; height: 40px; border-radius: 10px;
      background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.12);
      display: flex; align-items: center; justify-content: center;
      color: rgba(255,255,255,0.7); font-size: 16px; text-decoration: none;
      transition: all 0.2s;
    }
    .social-row a:hover { background: var(--rose); color: var(--white); border-color: var(--rose); }

    /* Right Panel - Form */
    .right-panel { padding: 48px; }
    .right-panel h3 { font-family: 'Playfair Display', serif; font-size: 28px; margin-bottom: 6px; }
    .right-panel > p { color: var(--text-light); margin-bottom: 32px; font-size: 15px; }

    .alert {
      padding: 14px 18px; border-radius: 14px; margin-bottom: 24px; font-size: 14px; font-weight: 600;
      display: flex; align-items: center; gap: 10px; animation: slideDown 0.3s ease;
    }
    @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    .alert-success { background: #EAF8F4; border: 1px solid #B0E0D0; color: #2E7D62; }
    .alert-error { background: #FFF0F3; border: 1px solid #F5B8C8; color: var(--deep-rose); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 4px; }
    .form-group { margin-bottom: 20px; position: relative; }
    .form-group label {
      display: block; font-size: 12px; font-weight: 700; text-transform: uppercase;
      letter-spacing: 1px; color: var(--text-mid); margin-bottom: 8px;
    }
    .form-group input, .form-group select, .form-group textarea {
      width: 100%; padding: 14px 16px 14px 48px;
      border: 2px solid #F0E4DC; border-radius: 14px;
      font-family: 'DM Sans', sans-serif; font-size: 15px; color: var(--text-dark);
      background: var(--cream); outline: none; transition: all 0.25s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
      border-color: var(--rose); background: var(--white);
      box-shadow: 0 0 0 5px rgba(232,115,138,0.08);
    }
    .form-group textarea { resize: vertical; min-height: 130px; padding-left: 16px; padding-top: 16px; }
    .form-group select { cursor: pointer; appearance: none; padding-right: 40px; }
    .form-group .input-icon {
      position: absolute; left: 16px; top: 44px; color: var(--text-light); font-size: 16px;
      transition: color 0.25s; pointer-events: none;
    }
    .form-group input:focus ~ .input-icon, .form-group select:focus ~ .input-icon {
      color: var(--rose);
    }
    .select-arrow {
      position: absolute; right: 16px; top: 44px; color: var(--text-light); font-size: 12px;
      pointer-events: none;
    }

    .char-count {
      text-align: right; font-size: 12px; color: var(--text-light); margin-top: 6px;
    }

    .btn-submit {
      width: 100%; padding: 16px;
      background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); border: none; border-radius: 14px;
      font-size: 16px; font-weight: 700; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 10px;
      transition: all 0.25s; margin-top: 8px;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(196,77,101,0.35); }
    .btn-submit:active { transform: translateY(0); }

    /* Floating contact bubbles */
    .float-bubbles { display: flex; gap: 16px; margin-top: 48px; justify-content: center; flex-wrap: wrap; }
    .bubble {
      background: var(--white); border-radius: 20px; padding: 24px 28px;
      box-shadow: 0 4px 20px var(--shadow); text-align: center;
      min-width: 180px; transition: all 0.3s; cursor: pointer;
    }
    .bubble:hover { transform: translateY(-6px); box-shadow: 0 12px 40px var(--shadow); }
    .bubble .emoji { font-size: 36px; margin-bottom: 10px; display: block; }
    .bubble h4 { font-size: 15px; margin-bottom: 4px; }
    .bubble p { font-size: 13px; color: var(--text-light); margin: 0; }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 900px) {
      .main-card { grid-template-columns: 1fr; }
      .left-panel { padding: 36px 28px; }
      .right-panel { padding: 32px 24px; }
      .form-row { grid-template-columns: 1fr; gap: 0; }
      .hero { padding: 60px 20px 100px; }
      .hero h1 { font-size: 32px; }
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
  <h1>Contact Us 📞</h1>
  <p>We would love to hear from you! Reach out for support, feedback, or just to say hello.</p>
</section>

<div class="container">
  <div class="main-card">

    <!-- Left Panel -->
    <div class="left-panel">
      <h3>Get in Touch</h3>
      <p>Our team is here to help you with any questions or concerns.</p>

      <div class="contact-method">
        <div class="icon-wrap">📧</div>
        <div>
          <h4>Email Us</h4>
          <a href="mailto:hello@babybliss.com">hello@babybliss.com</a>
          <p>Response within 24 hours</p>
        </div>
      </div>

      <div class="contact-method">
        <div class="icon-wrap">📞</div>
        <div>
          <h4>Call Us</h4>
          <a href="tel:+18005552229">+1 (800) 555-BABY</a>
          <p>Mon–Fri, 9AM – 6PM PST</p>
        </div>
      </div>

      <div class="contact-method">
        <div class="icon-wrap">💬</div>
        <div>
          <h4>Live Chat</h4>
          <p style="color: var(--blush); font-weight: 500;">Available 24/7</p>
          <p>Instant responses during business hours</p>
        </div>
      </div>

      <div class="contact-method">
        <div class="icon-wrap">📍</div>
        <div>
          <h4>Visit Us</h4>
          <p>123 Blossom Lane, Suite 400<br/>San Francisco, CA 94105</p>
        </div>
      </div>

      <div class="social-row">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-pinterest-p"></i></a>
        <a href="#"><i class="fab fa-tiktok"></i></a>
      </div>
    </div>

    <!-- Right Panel - Form -->
    <div class="right-panel">
      <h3>Send a Message ✉️</h3>
      <p>Fill out the form below and we will get back to you as soon as possible.</p>

      <?php if($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
      <?php endif; ?>
      <?php if($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
      <?php endif; ?>

      <form action="contact.php" method="POST" id="contactForm">
        <div class="form-row">
          <div class="form-group">
            <label>Your Name</label>
            <input type="text" name="name" placeholder="John Doe" required/>
            <i class="fas fa-user input-icon"></i>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="john@example.com" required/>
            <i class="fas fa-envelope input-icon"></i>
          </div>
        </div>
        <div class="form-group">
          <label>Subject</label>
          <select name="subject" required>
            <option value="">Select a topic...</option>
            <option value="order">Order Inquiry</option>
            <option value="shipping">Shipping Question</option>
            <option value="return">Return / Refund</option>
            <option value="product">Product Question</option>
            <option value="account">Account Issue</option>
            <option value="feedback">Feedback</option>
            <option value="partnership">Business Partnership</option>
            <option value="other">Other</option>
          </select>
          <i class="fas fa-tag input-icon"></i>
          <i class="fas fa-chevron-down select-arrow"></i>
        </div>
        <div class="form-group">
          <label>Message</label>
          <textarea name="message" placeholder="How can we help you today?" maxlength="1000" id="msgArea" required></textarea>
          <div class="char-count"><span id="charCount">0</span> / 1000</div>
        </div>
        <button type="submit" name="submit_contact" class="btn-submit">
          <i class="fas fa-paper-plane"></i> Send Message
        </button>
      </form>
    </div>
  </div>

  <!-- Quick contact bubbles -->
  <div class="float-bubbles">
    <div class="bubble" onclick="window.location.href='help_center.php'">
      <span class="emoji">🆘</span>
      <h4>Help Center</h4>
      <p>Browse FAQs</p>
    </div>
    <div class="bubble" onclick="window.location.href='dispute.php'">
      <span class="emoji">⚖️</span>
      <h4>Dispute</h4>
      <p>File a claim</p>
    </div>
    <div class="bubble" onclick="window.location.href='report.php'">
      <span class="emoji">🚨</span>
      <h4>Report</h4>
      <p>Flag a product</p>
    </div>
  </div>
</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

<script>
  const msgArea = document.getElementById('msgArea');
  const charCount = document.getElementById('charCount');
  msgArea.addEventListener('input', function() {
    charCount.textContent = this.value.length;
    if (this.value.length >= 900) {
      charCount.style.color = 'var(--deep-rose)';
    } else {
      charCount.style.color = 'var(--text-light)';
    }
  });
</script>

</body>
</html>