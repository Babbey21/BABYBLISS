'<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Help Center – BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A;
      --deep-rose: #C44D65; --mint: #A8D8C8; --mint-dark: #5FB8A0;
      --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A;
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

    .hero { background: linear-gradient(135deg, var(--deep-rose) 0%, #A03050 100%); padding: 60px 48px; text-align: center; color: var(--white); }
    .hero h1 { font-family: 'Playfair Display', serif; font-size: 42px; margin-bottom: 12px; }
    .hero p { font-size: 16px; opacity: 0.85; max-width: 600px; margin: 0 auto; }
    
    .search-box { max-width: 600px; margin: -28px auto 0; padding: 0 24px; position: relative; z-index: 10; }
    .search-input {
      width: 100%; padding: 18px 24px 18px 56px; border: none; border-radius: 16px;
      font-family: 'DM Sans', sans-serif; font-size: 16px; color: var(--text-dark);
      background: var(--white); box-shadow: 0 8px 32px var(--shadow);
      outline: none;
    }
    .search-box i { position: absolute; left: 44px; top: 50%; transform: translateY(-50%); color: var(--text-light); font-size: 18px; }

    .container { max-width: 1100px; margin: 0 auto; padding: 48px 24px; }
    
    .categories-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 48px; }
    .cat-card {
      background: var(--white); border-radius: 20px; padding: 32px 28px;
      box-shadow: 0 4px 20px var(--shadow); cursor: pointer; transition: all 0.3s;
      text-align: center; border: 2px solid transparent;
    }
    .cat-card:hover { transform: translateY(-4px); border-color: var(--rose); box-shadow: 0 12px 40px var(--shadow); }
    .cat-icon { font-size: 42px; margin-bottom: 16px; }
    .cat-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--text-dark); }
    .cat-card p { font-size: 14px; color: var(--text-light); }

    .faq-section { margin-bottom: 48px; }
    .faq-section h2 { font-family: 'Playfair Display', serif; font-size: 28px; margin-bottom: 24px; text-align: center; }
    .faq-item { background: var(--white); border-radius: 16px; margin-bottom: 12px; overflow: hidden; box-shadow: 0 2px 12px var(--shadow); }
    .faq-question { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-weight: 600; font-size: 15px; }
    .faq-question i { color: var(--rose); transition: transform 0.3s; }
    .faq-item.active .faq-question i { transform: rotate(180deg); }
    .faq-answer { padding: 0 24px; max-height: 0; overflow: hidden; transition: all 0.3s; }
    .faq-item.active .faq-answer { padding: 0 24px 20px; max-height: 300px; }
    .faq-answer p { font-size: 14px; color: var(--text-mid); }

    .contact-banner {
      background: linear-gradient(135deg, var(--mint-dark), #3A9E88);
      border-radius: 20px; padding: 40px; text-align: center; color: var(--white);
    }
    .contact-banner h2 { font-family: 'Playfair Display', serif; font-size: 28px; margin-bottom: 12px; }
    .contact-banner p { opacity: 0.9; margin-bottom: 20px; }
    .contact-btn {
      display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px;
      background: var(--white); color: var(--mint-dark); border-radius: 12px;
      font-weight: 700; text-decoration: none; transition: all 0.2s;
    }
    .contact-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .categories-grid { grid-template-columns: 1fr; }
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
  <h1>Help Center 🆘</h1>
  <p>Find answers to your questions about orders, shipping, returns, and more.</p>
</section>

<div class="search-box">
  <i class="fas fa-search"></i>
  <input type="text" class="search-input" placeholder="Search for help (e.g. 'track order', 'return policy')..." id="searchInput"/>
</div>

<div class="container">
  <div class="categories-grid">
    <div class="cat-card" onclick="filterFaq('orders')">
      <div class="cat-icon">📦</div>
      <h3>Orders & Shipping</h3>
      <p>Track orders, delivery times, shipping costs</p>
    </div>
    <div class="cat-card" onclick="filterFaq('returns')">
      <div class="cat-icon">🔄</div>
      <h3>Returns & Refunds</h3>
      <p>Return process, refund timelines, exchanges</p>
    </div>
    <div class="cat-card" onclick="filterFaq('account')">
      <div class="cat-icon">👤</div>
      <h3>Account & Payment</h3>
      <p>Login issues, payment methods, security</p>
    </div>
  </div>

  <div class="faq-section">
    <h2>Frequently Asked Questions</h2>
    
    <div class="faq-item" data-category="orders">
      <div class="faq-question" onclick="toggleFaq(this)">
        <span>How do I track my order?</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        <p>Once your order ships, you will receive an email with a tracking number. You can also track your order by logging into your account and visiting the "My Orders" section. Click on any order to see its current status and estimated delivery date.</p>
      </div>
    </div>

    <div class="faq-item" data-category="orders">
      <div class="faq-question" onclick="toggleFaq(this)">
        <span>How long does shipping take?</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        <p>Standard shipping takes 5-7 business days. Express shipping (2-3 business days) is available at checkout. Free standard shipping is offered on all orders over $50 within the contiguous United States.</p>
      </div>
    </div>

    <div class="faq-item" data-category="orders">
      <div class="faq-question" onclick="toggleFaq(this)">
        <span>Can I change or cancel my order?</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        <p>You can modify or cancel your order within 1 hour of placing it. After that, orders enter our fulfillment process and cannot be changed. Please contact our support team immediately if you need assistance.</p>
      </div>
    </div>

    <div class="faq-item" data-category="returns">
      <div class="faq-question" onclick="toggleFaq(this)">
        <span>What is your return policy?</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        <p>We offer a 30-day return policy for most items. Products must be unused, in original packaging, and in resellable condition. Personalized items and products marked "Final Sale" cannot be returned unless defective.</p>
      </div>
    </div>

    <div class="faq-item" data-category="returns">
      <div class="faq-question" onclick="toggleFaq(this)">
        <span>How long do refunds take to process?</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        <p>Once we receive your returned item, refunds are processed within 5-10 business days. The refund will be issued to your original payment method. You will receive an email confirmation once the refund is initiated.</p>
      </div>
    </div>

    <div class="faq-item" data-category="account">
      <div class="faq-question" onclick="toggleFaq(this)">
        <span>I forgot my password. How do I reset it?</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        <p>Click on "Forgot Password" on the login page. Enter your email address, and we will send you a password reset link. The link expires after 24 hours for security reasons. If you don't receive the email, check your spam folder.</p>
      </div>
    </div>

    <div class="faq-item" data-category="account">
      <div class="faq-question" onclick="toggleFaq(this)">
        <span>Is my payment information secure?</span>
        <i class="fas fa-chevron-down"></i>
      </div>
      <div class="faq-answer">
        <p>Yes! We use industry-standard SSL encryption for all transactions. Your payment details are processed by PCI-DSS compliant payment providers. We never store your full credit card numbers on our servers.</p>
      </div>
    </div>
  </div>

  <div class="contact-banner">
    <h2>Still Need Help? 💬</h2>
    <p>Our friendly support team is available 24/7 to assist you with any questions or concerns.</p>
    <a href="contact.php" class="contact-btn"><i class="fas fa-envelope"></i> Contact Support</a>
  </div>
</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

<script>
  function toggleFaq(el) {
    const item = el.parentElement;
    const wasActive = item.classList.contains('active');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
    if (!wasActive) item.classList.add('active');
  }
  
  function filterFaq(category) {
    document.querySelectorAll('.faq-item').forEach(item => {
      if (item.dataset.category === category) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  }
  
  document.getElementById('searchInput').addEventListener('input', function(e) {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('.faq-item').forEach(item => {
      const text = item.textContent.toLowerCase();
      item.style.display = text.includes(q) ? 'block' : 'none';
    });
  });
</script>
</body>
</html>
