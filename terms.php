terms.php
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Terms of Service – BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A;
      --deep-rose: #C44D65; --mint-dark: #5FB8A0;
      --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A;
      --shadow: rgba(196,77,101,0.12);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--text-dark); line-height: 1.7; }

    /* Header */
    header {
      background: var(--white); position: sticky; top: 0; z-index: 999;
      box-shadow: 0 2px 20px var(--shadow);
    }
    .header-main {
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 48px; height: 72px; max-width: 1400px; margin: 0 auto;
    }
    .logo {
      display: flex; align-items: center; gap: 10px; text-decoration: none;
    }
    .logo-icon {
      width: 44px; height: 44px; background: linear-gradient(135deg, var(--blush), var(--deep-rose));
      border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px;
    }
    .logo-text { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: var(--deep-rose); }
    .btn-back {
      padding: 9px 22px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer;
      border: 2px solid var(--rose); color: var(--rose); background: transparent; transition: all 0.2s; text-decoration: none;
    }
    .btn-back:hover { background: var(--rose); color: var(--white); }

    /* Hero */
    .hero {
      background: linear-gradient(135deg, var(--deep-rose) 0%, #A03050 100%);
      padding: 60px 48px; text-align: center; color: var(--white);
    }
    .hero h1 { font-family: 'Playfair Display', serif; font-size: 42px; margin-bottom: 12px; }
    .hero p { font-size: 16px; opacity: 0.85; max-width: 600px; margin: 0 auto; }
    .hero .last-updated { margin-top: 16px; font-size: 13px; opacity: 0.7; }

    /* Content */
    .container { max-width: 800px; margin: 0 auto; padding: 48px 24px; }
    .toc { 
      background: var(--white); border-radius: 20px; padding: 28px 32px; 
      margin-bottom: 40px; box-shadow: 0 4px 20px var(--shadow);
    }
    .toc h2 { font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 16px; color: var(--text-dark); }
    .toc ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .toc a { color: var(--rose); text-decoration: none; font-weight: 500; font-size: 15px; transition: color 0.2s; }
    .toc a:hover { color: var(--deep-rose); }
    .toc a::before { content: '→ '; opacity: 0.5; }

    .section { margin-bottom: 40px; scroll-margin-top: 100px; }
    .section h2 {
      font-family: 'Playfair Display', serif; font-size: 26px; color: var(--text-dark);
      margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #F0E4DC;
    }
    .section h3 { font-size: 18px; color: var(--deep-rose); margin: 24px 0 12px; font-weight: 600; }
    .section p { margin-bottom: 14px; font-size: 15px; color: var(--text-mid); }
    .section ul { margin-left: 24px; margin-bottom: 16px; }
    .section ul li { margin-bottom: 8px; font-size: 15px; color: var(--text-mid); }
    .section strong { color: var(--text-dark); }

    .highlight-box {
      background: linear-gradient(135deg, #FFF0F3, #FFE4EA);
      border-left: 4px solid var(--rose); border-radius: 0 12px 12px 0;
      padding: 20px 24px; margin: 20px 0;
    }
    .highlight-box p { margin: 0; font-size: 14px; }

    /* Footer */
    .page-footer {
      background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center;
      padding: 32px 24px; font-size: 13px;
    }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .container { padding: 32px 20px; }
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
  <h1>Terms of Service</h1>
  <p>Please read these terms carefully before using our website and services. By accessing BabyBliss, you agree to be bound by these terms.</p>
  <div class="last-updated">Last Updated: June 17, 2026</div>
</section>

<div class="container">

  <div class="toc">
    <h2>📋 Table of Contents</h2>
    <ul>
      <li><a href="#acceptance">1. Acceptance of Terms</a></li>
      <li><a href="#eligibility">2. Eligibility</a></li>
      <li><a href="#account">3. Account Registration</a></li>
      <li><a href="#products">4. Products & Pricing</a></li>
      <li><a href="#orders">5. Orders & Payment</a></li>
      <li><a href="#shipping">6. Shipping & Delivery</a></li>
      <li><a href="#returns">7. Returns & Refunds</a></li>
      <li><a href="#conduct">8. User Conduct</a></li>
      <li><a href="#ip">9. Intellectual Property</a></li>
      <li><a href="#liability">10. Limitation of Liability</a></li>
      <li><a href="#privacy">11. Privacy</a></li>
      <li><a href="#changes">12. Changes to Terms</a></li>
      <li><a href="#contact">13. Contact Us</a></li>
    </ul>
  </div>

  <div class="section" id="acceptance">
    <h2>1. Acceptance of Terms</h2>
    <p>Welcome to BabyBliss! These Terms of Service ("Terms") govern your access to and use of the BabyBliss website, mobile applications, and services (collectively, the "Services"). By accessing or using our Services, you agree to be bound by these Terms and our Privacy Policy.</p>
    <p>If you do not agree to these Terms, you may not access or use our Services. We reserve the right to modify these Terms at any time, and such modifications shall be effective immediately upon posting.</p>
  </div>

  <div class="section" id="eligibility">
    <h2>2. Eligibility</h2>
    <p>You must be at least 18 years old to use our Services or make purchases. By using BabyBliss, you represent and warrant that:</p>
    <ul>
      <li>You are at least 18 years of age or have reached the age of majority in your jurisdiction.</li>
      <li>You have the legal capacity to enter into a binding contract.</li>
      <li>You are not barred from using the Services under any applicable law.</li>
      <li>If you are creating an account on behalf of a business, you have authority to bind that business.</li>
    </ul>
  </div>

  <div class="section" id="account">
    <h2>3. Account Registration</h2>
    <p>To access certain features of our Services, you may need to create an account. When you register, you agree to:</p>
    <ul>
      <li>Provide accurate, current, and complete information about yourself.</li>
      <li>Maintain and promptly update your account information to keep it accurate.</li>
      <li>Keep your password secure and confidential.</li>
      <li>Notify us immediately of any unauthorized access or breach of security.</li>
      <li>Accept responsibility for all activities that occur under your account.</li>
    </ul>
    <div class="highlight-box">
      <p><strong>🔒 Security Tip:</strong> Choose a strong, unique password and never share your login credentials with anyone. BabyBliss will never ask for your password via email or phone.</p>
    </div>
  </div>

  <div class="section" id="products">
    <h2>4. Products & Pricing</h2>
    <p>All products listed on BabyBliss are subject to availability. We make every effort to display product colors and images as accurately as possible, but we cannot guarantee that your device's display will accurately reflect the actual product.</p>
    <h3>4.1 Pricing</h3>
    <p>Prices are listed in US Dollars (USD) unless otherwise stated. We reserve the right to change prices at any time without notice. In the event of a pricing error, we reserve the right to cancel any orders placed at the incorrect price.</p>
    <h3>4.2 Product Information</h3>
    <p>We provide detailed descriptions, age recommendations, and safety information for all products. It is your responsibility to review this information before purchasing to ensure the product is suitable for your child's age and developmental stage.</p>
  </div>

  <div class="section" id="orders">
    <h2>5. Orders & Payment</h2>
    <p>By placing an order on BabyBliss, you are making an offer to purchase the selected products. We reserve the right to accept or decline your order for any reason, including:</p>
    <ul>
      <li>Product unavailability or discontinuation</li>
      <li>Errors in product or pricing information</li>
      <li>Suspected fraudulent activity</li>
      <li>Issues with payment authorization</li>
    </ul>
    <h3>5.1 Payment Methods</h3>
    <p>We accept major credit cards (Visa, MasterCard, American Express), PayPal, Apple Pay, and other payment methods as indicated at checkout. All payments are processed securely through encrypted connections.</p>
    <h3>5.2 Order Confirmation</h3>
    <p>Upon successful payment, you will receive an order confirmation email. This email confirms that we have received your order but does not constitute acceptance of your offer.</p>
  </div>

  <div class="section" id="shipping">
    <h2>6. Shipping & Delivery</h2>
    <p>We offer various shipping options as displayed at checkout. Estimated delivery times are provided as guidelines and are not guaranteed. Actual delivery dates may vary based on:</p>
    <ul>
      <li>Your location and shipping address</li>
      <li>Product availability and warehouse location</li>
      <li>Carrier schedules and external factors (weather, holidays, etc.)</li>
    </ul>
    <p><strong>Free Shipping:</strong> Orders over $50 qualify for free standard shipping within the contiguous United States. International shipping rates and delivery times vary by destination.</p>
  </div>

  <div class="section" id="returns">
    <h2>7. Returns & Refunds</h2>
    <p>We want you to be completely satisfied with your purchase. Our return policy allows you to return most items within 30 days of delivery for a full refund or exchange.</p>
    <h3>7.1 Return Conditions</h3>
    <ul>
      <li>Items must be unused, in original packaging, and in resellable condition.</li>
      <li>Proof of purchase (order number or receipt) is required.</li>
      <li>Personalized or custom items cannot be returned unless defective.</li>
      <li>Items marked as "Final Sale" are not eligible for return.</li>
    </ul>
    <h3>7.2 Refund Process</h3>
    <p>Refunds will be issued to the original payment method within 5-10 business days after we receive and inspect the returned item. Shipping costs for returns are the customer's responsibility unless the item was defective or incorrectly shipped.</p>
  </div>

  <div class="section" id="conduct">
    <h2>8. User Conduct</h2>
    <p>You agree not to use our Services for any unlawful purpose or in any way that could damage, disable, or impair our Services. Prohibited activities include:</p>
    <ul>
      <li>Attempting to gain unauthorized access to our systems or other users' accounts</li>
      <li>Using automated systems (bots, scrapers) to access our Services</li>
      <li>Posting false, misleading, or defamatory content in reviews or comments</li>
      <li>Interfering with the proper working of our Services</li>
      <li>Violating any applicable local, state, national, or international law</li>
    </ul>
    <p>We reserve the right to terminate or suspend your account and access to our Services for violations of these rules.</p>
  </div>

  <div class="section" id="ip">
    <h2>9. Intellectual Property</h2>
    <p>All content on BabyBliss, including but not limited to text, graphics, logos, images, audio clips, digital downloads, and software, is the property of BabyBliss or its content suppliers and is protected by international copyright, trademark, and other intellectual property laws.</p>
    <p>You may not reproduce, distribute, modify, create derivative works from, publicly display, or exploit any content from our Services without our prior written consent. You are granted a limited, non-exclusive, non-transferable license to access and use our Services for personal, non-commercial purposes.</p>
  </div>

  <div class="section" id="liability">
    <h2>10. Limitation of Liability</h2>
    <p>To the fullest extent permitted by law, BabyBliss and its affiliates, officers, directors, employees, and agents shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising out of or relating to your use of our Services.</p>
    <p>Our total liability to you for any claim arising from these Terms or your use of the Services shall not exceed the amount you paid to us in the 12 months preceding the claim, or $100, whichever is greater.</p>
    <div class="highlight-box">
      <p><strong>⚠️ Important:</strong> BabyBliss products are designed for use under adult supervision. Always follow age recommendations and safety guidelines. We are not liable for injuries resulting from misuse or failure to supervise children during product use.</p>
    </div>
  </div>

  <div class="section" id="privacy">
    <h2>11. Privacy</h2>
    <p>Your privacy is important to us. Our <a href="privacy.php" style="color:var(--rose);font-weight:600;">Privacy Policy</a> explains how we collect, use, protect, and disclose your personal information when you use our Services. By using BabyBliss, you consent to the collection and use of your information as described in our Privacy Policy.</p>
  </div>

  <div class="section" id="changes">
    <h2>12. Changes to Terms</h2>
    <p>We may update these Terms from time to time to reflect changes in our practices, legal requirements, or business operations. When we make material changes, we will notify you by:</p>
    <ul>
      <li>Posting a prominent notice on our website</li>
      <li>Sending an email to the address associated with your account</li>
      <li>Updating the "Last Updated" date at the top of this page</li>
    </ul>
    <p>Your continued use of our Services after changes are posted constitutes your acceptance of the updated Terms.</p>
  </div>

  <div class="section" id="contact">
    <h2>13. Contact Us</h2>
    <p>If you have any questions, concerns, or feedback about these Terms, please contact us:</p>
    <ul>
      <li><strong>Email:</strong> hello@babybliss.com</li>
      <li><strong>Phone:</strong> +1 (800) 555-BABY</li>
      <li><strong>Address:</strong> 123 Blossom Lane, Suite 400, San Francisco, CA 94105, USA</li>
      <li><strong>Live Chat:</strong> Available 24/7 on our website</li>
    </ul>
    <p>We aim to respond to all inquiries within 24 hours during business days.</p>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

</body>
</html>