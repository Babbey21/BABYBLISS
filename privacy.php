privacy.php
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Privacy Policy – BabyBliss</title>
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
      background: linear-gradient(135deg, var(--mint-dark) 0%, #3A9E88 100%);
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
    .toc a { color: var(--mint-dark); text-decoration: none; font-weight: 500; font-size: 15px; transition: color 0.2s; }
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

    .privacy-card {
      background: var(--white); border-radius: 16px; padding: 24px; margin-bottom: 20px;
      box-shadow: 0 2px 12px var(--shadow); border-left: 4px solid var(--mint-dark);
    }
    .privacy-card h4 { font-size: 16px; color: var(--text-dark); margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
    .privacy-card p { font-size: 14px; margin: 0; }

    .highlight-box {
      background: linear-gradient(135deg, #E8F8F5, #D4F5EC);
      border-left: 4px solid var(--mint-dark); border-radius: 0 12px 12px 0;
      padding: 20px 24px; margin: 20px 0;
    }
    .highlight-box p { margin: 0; font-size: 14px; }

    .data-table {
      width: 100%; border-collapse: collapse; margin: 20px 0;
      background: var(--white); border-radius: 12px; overflow: hidden;
      box-shadow: 0 2px 12px var(--shadow);
    }
    .data-table th {
      background: var(--cream); padding: 14px 18px; text-align: left;
      font-size: 13px; font-weight: 700; color: var(--text-dark);
      border-bottom: 2px solid #F0E4DC;
    }
    .data-table td {
      padding: 14px 18px; font-size: 14px; color: var(--text-mid);
      border-bottom: 1px solid #F5E6DF;
    }
    .data-table tr:last-child td { border-bottom: none; }

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
      .data-table { font-size: 13px; }
      .data-table th, .data-table td { padding: 10px 12px; }
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
  <h1>Privacy Policy</h1>
  <p>We are committed to protecting your privacy and ensuring your personal information is handled safely and responsibly. This policy explains how we collect, use, and safeguard your data.</p>
  <div class="last-updated">Last Updated: June 17, 2026</div>
</section>

<div class="container">

  <div class="toc">
    <h2>📋 Table of Contents</h2>
    <ul>
      <li><a href="#intro">1. Introduction</a></li>
      <li><a href="#collect">2. Information We Collect</a></li>
      <li><a href="#how">3. How We Use Your Information</a></li>
      <li><a href="#share">4. Information Sharing</a></li>
      <li><a href="#cookies">5. Cookies & Tracking</a></li>
      <li><a href="#security">6. Data Security</a></li>
      <li><a href="#rights">7. Your Rights & Choices</a></li>
      <li><a href="#children">8. Children's Privacy</a></li>
      <li><a href="#retention">9. Data Retention</a></li>
      <li><a href="#transfer">10. International Transfers</a></li>
      <li><a href="#changes">11. Changes to This Policy</a></li>
      <li><a href="#contact">12. Contact Us</a></li>
    </ul>
  </div>

  <div class="section" id="intro">
    <h2>1. Introduction</h2>
    <p>At BabyBliss, we take your privacy seriously. This Privacy Policy describes how we collect, use, store, and protect your personal information when you visit our website, create an account, make purchases, or interact with our Services.</p>
    <p>By using BabyBliss, you consent to the practices described in this Privacy Policy. If you do not agree with this policy, please do not use our Services.</p>
    <div class="highlight-box">
      <p><strong>💚 Our Promise:</strong> We will never sell your personal information to third parties. Your data is used solely to provide, improve, and secure our Services, and to communicate with you about your orders and preferences.</p>
    </div>
  </div>

  <div class="section" id="collect">
    <h2>2. Information We Collect</h2>
    <p>We collect different types of information depending on how you interact with our Services:</p>

    <h3>2.1 Information You Provide Directly</h3>
    <table class="data-table">
      <tr><th>Information</th><th>When We Collect</th><th>Purpose</th></tr>
      <tr><td>Name (first & last)</td><td>Account registration</td><td>Order fulfillment, personalization</td></tr>
      <tr><td>Email address</td><td>Account registration, newsletter</td><td>Communication, order updates</td></tr>
      <tr><td>Phone number</td><td>Account registration, checkout</td><td>Delivery coordination, support</td></tr>
      <tr><td>Shipping address</td><td>Checkout</td><td>Product delivery</td></tr>
      <tr><td>Billing address</td><td>Checkout</td><td>Payment processing</td></tr>
      <tr><td>Payment details</td><td>Checkout</td><td>Transaction processing (encrypted)</td></tr>
      <tr><td>Baby's age range</td><td>Account registration (optional)</td><td>Personalized recommendations</td></tr>
      <tr><td>Account password</td><td>Registration</td><td>Account security (hashed)</td></tr>
    </table>

    <h3>2.2 Information Collected Automatically</h3>
    <p>When you visit our website, we automatically collect certain information through cookies and similar technologies:</p>
    <ul>
      <li><strong>Device Information:</strong> IP address, browser type, operating system, device type</li>
      <li><strong>Usage Data:</strong> Pages visited, time spent, clicks, products viewed, search queries</li>
      <li><strong>Location Data:</strong> General geographic location based on IP address</li>
      <li><strong>Referral Data:</strong> How you found our website (search engine, social media, etc.)</li>
    </ul>
  </div>

  <div class="section" id="how">
    <h2>3. How We Use Your Information</h2>
    <p>We use your personal information for the following purposes:</p>

    <div class="privacy-card">
      <h4>🛒 Order Processing</h4>
      <p>To process and fulfill your orders, send order confirmations, handle returns, and provide customer support related to your purchases.</p>
    </div>
    <div class="privacy-card">
      <h4>🔐 Account Management</h4>
      <p>To create and maintain your account, verify your identity, and provide personalized features like order history and saved addresses.</p>
    </div>
    <div class="privacy-card">
      <h4>📧 Communication</h4>
      <p>To send you transactional emails (order updates, shipping notifications) and, with your consent, marketing communications about new products, promotions, and parenting tips.</p>
    </div>
    <div class="privacy-card">
      <h4>🎯 Personalization</h4>
      <p>To recommend products based on your baby's age, browsing history, and purchase patterns, making your shopping experience more relevant.</p>
    </div>
    <div class="privacy-card">
      <h4>📊 Analytics & Improvement</h4>
      <p>To analyze website usage, troubleshoot issues, improve our Services, and develop new features based on user behavior and feedback.</p>
    </div>
    <div class="privacy-card">
      <h4>⚖️ Legal Compliance</h4>
      <p>To comply with applicable laws, respond to legal requests, prevent fraud, and protect the rights and safety of our users and business.</p>
    </div>
  </div>

  <div class="section" id="share">
    <h2>4. Information Sharing</h2>
    <p>We do not sell, rent, or trade your personal information. We only share your data in the following limited circumstances:</p>
    <ul>
      <li><strong>Service Providers:</strong> We share information with trusted third-party vendors who help us operate our business (payment processors, shipping carriers, email service providers, analytics platforms). These providers are contractually bound to use your data only for the services they provide to us.</li>
      <li><strong>Legal Requirements:</strong> We may disclose information if required by law, court order, or government request, or to protect our rights, property, or safety, or that of our users.</li>
      <li><strong>Business Transfers:</strong> In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of the business transaction. We will notify you of any such change.</li>
      <li><strong>With Your Consent:</strong> We may share information with your explicit permission for specific purposes.</li>
    </ul>
    <div class="highlight-box">
      <p><strong>🔒 Important:</strong> We require all third-party service providers to maintain the confidentiality and security of your personal information and to process it in accordance with applicable data protection laws.</p>
    </div>
  </div>

  <div class="section" id="cookies">
    <h2>5. Cookies & Tracking Technologies</h2>
    <p>We use cookies and similar technologies to enhance your browsing experience, analyze site traffic, and understand where our visitors come from.</p>
    <h3>5.1 Types of Cookies We Use</h3>
    <ul>
      <li><strong>Essential Cookies:</strong> Required for the website to function properly (shopping cart, login sessions, security).</li>
      <li><strong>Functional Cookies:</strong> Remember your preferences and settings (language, currency, recently viewed items).</li>
      <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with our website (Google Analytics, heatmaps).</li>
      <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements and measure their effectiveness (Facebook Pixel, Google Ads).</li>
    </ul>
    <h3>5.2 Your Cookie Choices</h3>
    <p>You can manage your cookie preferences through your browser settings. Most browsers allow you to block or delete cookies. However, disabling essential cookies may affect the functionality of our website. You can also opt out of targeted advertising through the <a href="https://optout.aboutads.info" target="_blank" style="color:var(--rose);">Digital Advertising Alliance</a> or <a href="https://youradchoices.com" target="_blank" style="color:var(--rose);">Your Ad Choices</a>.</p>
  </div>

  <div class="section" id="security">
    <h2>6. Data Security</h2>
    <p>We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction:</p>
    <ul>
      <li><strong>Encryption:</strong> All data transmitted between your browser and our servers is encrypted using SSL/TLS technology (HTTPS).</li>
      <li><strong>Password Hashing:</strong> Your account password is hashed using industry-standard algorithms (bcrypt) and is never stored in plain text.</li>
      <li><strong>Secure Payment Processing:</strong> Payment card details are processed by PCI-DSS compliant payment providers. We do not store your full credit card numbers on our servers.</li>
      <li><strong>Access Controls:</strong> Only authorized personnel with a legitimate business need can access personal information, and they are bound by confidentiality obligations.</li>
      <li><strong>Regular Audits:</strong> We conduct security assessments and monitor our systems for vulnerabilities and suspicious activity.</li>
    </ul>
    <p>While we strive to use commercially acceptable means to protect your data, no method of transmission over the internet or electronic storage is 100% secure. We cannot guarantee absolute security but are committed to promptly notifying you and relevant authorities in the event of a data breach.</p>
  </div>

  <div class="section" id="rights">
    <h2>7. Your Rights & Choices</h2>
    <p>Depending on your location, you may have certain rights regarding your personal information:</p>
    <ul>
      <li><strong>Access:</strong> You can request a copy of the personal information we hold about you.</li>
      <li><strong>Correction:</strong> You can update or correct inaccurate information through your account settings or by contacting us.</li>
      <li><strong>Deletion:</strong> You can request deletion of your personal information, subject to certain legal exceptions.</li>
      <li><strong>Portability:</strong> You can request your data in a structured, machine-readable format.</li>
      <li><strong>Restriction:</strong> You can request that we limit the processing of your data in certain circumstances.</li>
      <li><strong>Objection:</strong> You can object to the processing of your data for direct marketing purposes at any time.</li>
      <li><strong>Withdraw Consent:</strong> Where we rely on your consent, you can withdraw it at any time without affecting the lawfulness of processing based on consent before its withdrawal.</li>
    </ul>
    <p>To exercise any of these rights, please contact us at <strong>privacy@babybliss.com</strong> or through the contact details provided below. We will respond to your request within 30 days.</p>
    <h3>7.1 Marketing Preferences</h3>
    <p>You can opt out of receiving marketing emails by clicking the "Unsubscribe" link at the bottom of any marketing email or by updating your preferences in your account settings. Please note that you will still receive transactional emails related to your orders.</p>
  </div>

  <div class="section" id="children">
    <h2>8. Children's Privacy</h2>
    <p>BabyBliss is a general audience website, but our products are designed for children. We take special care regarding children's privacy:</p>
    <ul>
      <li>We do not knowingly collect personal information from children under 13 without verifiable parental consent.</li>
      <li>When you register and provide your child's age range, this information is used solely to provide age-appropriate product recommendations.</li>
      <li>We do not use children's information for targeted advertising.</li>
      <li>If we learn that we have collected personal information from a child under 13 without parental consent, we will delete that information promptly.</li>
    </ul>
    <p>If you believe your child has provided us with personal information without your consent, please contact us immediately at <strong>privacy@babybliss.com</strong>.</p>
  </div>

  <div class="section" id="retention">
    <h2>9. Data Retention</h2>
    <p>We retain your personal information for as long as necessary to fulfill the purposes for which it was collected, including:</p>
    <ul>
      <li>Providing our Services and maintaining your account</li>
      <li>Complying with legal obligations (tax, accounting, consumer protection laws)</li>
      <li>Resolving disputes and enforcing our agreements</li>
      <li>Historical analysis and business improvement</li>
    </ul>
    <p>Typically, we retain order and account information for 7 years after your last activity, after which it is either deleted or anonymized. You may request earlier deletion by contacting us, though we may need to retain certain information for legal purposes.</p>
  </div>

  <div class="section" id="transfer">
    <h2>10. International Transfers</h2>
    <p>BabyBliss is based in the United States. If you are accessing our Services from outside the US, please be aware that your information may be transferred to, stored, and processed in the United States or other countries where our service providers operate.</p>
    <p>These countries may have data protection laws that differ from those in your jurisdiction. By using our Services, you consent to the transfer of your information to the United States and other countries as described in this policy.</p>
    <p>For transfers from the European Economic Area (EEA), United Kingdom, or Switzerland, we implement appropriate safeguards, such as Standard Contractual Clauses approved by the European Commission, to ensure your data receives adequate protection.</p>
  </div>

  <div class="section" id="changes">
    <h2>11. Changes to This Policy</h2>
    <p>We may update this Privacy Policy from time to time to reflect changes in our practices, legal requirements, or business operations. When we make material changes, we will:</p>
    <ul>
      <li>Post the updated policy on this page with a revised "Last Updated" date</li>
      <li>Notify you via email if you have an account with us</li>
      <li>Display a prominent notice on our website for significant changes</li>
    </ul>
    <p>We encourage you to review this Privacy Policy periodically to stay informed about how we protect your information.</p>
  </div>

  <div class="section" id="contact">
    <h2>12. Contact Us</h2>
    <p>If you have any questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:</p>
    <ul>
      <li><strong>Email:</strong> privacy@babybliss.com</li>
      <li><strong>General Inquiries:</strong> hello@babybliss.com</li>
      <li><strong>Phone:</strong> +1 (800) 555-BABY</li>
      <li><strong>Address:</strong> 123 Blossom Lane, Suite 400, San Francisco, CA 94105, USA</li>
      <li><strong>Data Protection Officer:</strong> dpo@babybliss.com</li>
    </ul>
    <p>We are committed to resolving any privacy concerns promptly and transparently. If you are not satisfied with our response, you may have the right to lodge a complaint with your local data protection authority.</p>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

</body>
</html>