<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shipping Guide – BabyBliss</title>
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

    .container { max-width: 1000px; margin: 0 auto; padding: 48px 24px; }

    .shipping-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 48px; }
    .shipping-card {
      background: var(--white); border-radius: 20px; padding: 32px 24px;
      text-align: center; box-shadow: 0 4px 20px var(--shadow);
      transition: all 0.3s; border: 2px solid transparent;
    }
    .shipping-card:hover { transform: translateY(-4px); border-color: var(--rose); }
    .shipping-card .icon { font-size: 40px; margin-bottom: 16px; }
    .shipping-card h3 { font-size: 18px; margin-bottom: 8px; }
    .shipping-card .price { font-size: 28px; font-weight: 700; color: var(--deep-rose); margin: 8px 0; }
    .shipping-card .price span { font-size: 14px; color: var(--text-light); font-weight: 400; }
    .shipping-card p { font-size: 14px; color: var(--text-light); }

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

    .table-wrap { overflow-x: auto; margin: 20px 0; }
    .data-table {
      width: 100%; border-collapse: collapse; background: var(--white);
      border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px var(--shadow);
    }
    .data-table th { background: var(--cream); padding: 14px 18px; text-align: left; font-size: 13px; font-weight: 700; color: var(--text-dark); border-bottom: 2px solid #F0E4DC; }
    .data-table td { padding: 14px 18px; font-size: 14px; color: var(--text-mid); border-bottom: 1px solid #F5E6DF; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table .flag { font-size: 18px; margin-right: 6px; }
    .data-table .free { color: var(--mint-dark); font-weight: 700; }
    .data-table .na { color: var(--text-light); font-style: italic; }

    .region-tabs { display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap; }
    .region-tab {
      padding: 10px 20px; border-radius: 20px; font-size: 14px; font-weight: 600;
      border: 2px solid #F0E4DC; background: var(--white); color: var(--text-mid);
      cursor: pointer; transition: all 0.2s;
    }
    .region-tab:hover { border-color: var(--rose); color: var(--rose); }
    .region-tab.active { background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: var(--white); border-color: var(--rose); }

    .region-content { display: none; }
    .region-content.active { display: block; animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

    .delivery-note {
      background: linear-gradient(135deg, #FFF8E0, #FFE8A8);
      border-left: 4px solid var(--gold); border-radius: 0 12px 12px 0;
      padding: 16px 20px; margin: 16px 0;
    }
    .delivery-note p { margin: 0; font-size: 14px; color: var(--text-mid); }

    .page-footer { background: #1E0F0A; color: rgba(255,255,255,0.6); text-align: center; padding: 32px 24px; font-size: 13px; margin-top: 48px; }
    .page-footer a { color: var(--blush); text-decoration: none; }

    @media (max-width: 768px) {
      .header-main { padding: 0 20px; }
      .hero { padding: 40px 20px; }
      .hero h1 { font-size: 28px; }
      .shipping-options { grid-template-columns: 1fr; }
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
  <h1>Shipping Guide 🚚</h1>
  <p>Fast and reliable delivery across Tanzania, Zanzibar, and the East African Community.</p>
</section>

<div class="container">

  <div class="shipping-options">
    <div class="shipping-card">
      <div class="icon">🚚</div>
      <h3>Standard Delivery</h3>
      <div class="price">5,000 <span>TZS</span></div>
      <p>3-5 business days<br/>Tanzania Bara & Zanzibar</p>
    </div>
    <div class="shipping-card">
      <div class="icon">⚡</div>
      <h3>Express Delivery</h3>
      <div class="price">12,000 <span>TZS</span></div>
      <p>1-2 business days<br/>Dar, Arusha, Mwanza, ZNZ</p>
    </div>
    <div class="shipping-card">
      <div class="icon">🚀</div>
      <h3>Same Day</h3>
      <div class="price">20,000 <span>TZS</span></div>
      <p>Same day delivery<br/>Dar es Salaam only</p>
    </div>
  </div>

  <div class="section">
    <h2>Shipping Rates by Region</h2>

    <div class="region-tabs">
      <button class="region-tab active" onclick="showRegion('tz')">🇹🇿 Tanzania</button>
      <button class="region-tab" onclick="showRegion('eac')">🌍 East Africa</button>
      <button class="region-tab" onclick="showRegion('intl')">🌐 International</button>
    </div>

    <!-- Tanzania -->
    <div class="region-content active" id="tz">
      <h3 style="margin-bottom:16px; color:var(--deep-rose);">🇹🇿 Tanzania Bara & Zanzibar</h3>
      <div class="table-wrap">
        <table class="data-table">
          <tr><th>Destination</th><th>Standard (3-5 days)</th><th>Express (1-2 days)</th><th>Same Day</th></tr>
          <tr><td><span class="flag">🏙️</span> Dar es Salaam</td><td class="free">FREE over 50,000 TZS</td><td>12,000 TZS</td><td>20,000 TZS</td></tr>
          <tr><td><span class="flag">🌴</span> Zanzibar (Unguja)</td><td class="free">FREE over 50,000 TZS</td><td>15,000 TZS</td><td class="na">Not available</td></tr>
          <tr><td><span class="flag">🌺</span> Pemba Island</td><td>8,000 TZS</td><td>18,000 TZS</td><td class="na">Not available</td></tr>
          <tr><td><span class="flag">🗻</span> Arusha / Moshi</td><td>5,000 TZS</td><td>12,000 TZS</td><td class="na">Not available</td></tr>
          <tr><td><span class="flag">🌊</span> Mwanza</td><td>5,000 TZS</td><td>12,000 TZS</td><td class="na">Not available</td></tr>
          <tr><td><span class="flag">🏔️</span> Dodoma</td><td>5,000 TZS</td><td>12,000 TZS</td><td class="na">Not available</td></tr>
          <tr><td><span class="flag">🦁</span> Mbeya</td><td>6,000 TZS</td><td>14,000 TZS</td><td class="na">Not available</td></tr>
          <tr><td><span class="flag">🌅</span> Tanga</td><td>5,000 TZS</td><td>12,000 TZS</td><td class="na">Not available</td></tr>
          <tr><td><span class="flag">🌿</span> Morogoro</td><td>5,000 TZS</td><td>12,000 TZS</td><td class="na">Not available</td></tr>
          <tr><td><span class="flag">🐘</span> Other Regions</td><td>6,000 – 10,000 TZS</td><td>15,000 – 25,000 TZS</td><td class="na">Not available</td></tr>
        </table>
      </div>
      <div class="delivery-note">
        <p><strong>🚢 Zanzibar Note:</strong> Orders to Zanzibar are shipped via Azam Marine / Fast Ferries. Express delivery includes ferry transport + local courier. Standard delivery may take an extra 1-2 days for ferry scheduling.</p>
      </div>
    </div>

    <!-- EAC -->
    <div class="region-content" id="eac">
      <h3 style="margin-bottom:16px; color:var(--deep-rose);">🌍 East African Community (EAC)</h3>
      <div class="table-wrap">
        <table class="data-table">
          <tr><th>Country</th><th>Standard (5-10 days)</th><th>Express (3-5 days)</th><th>Courier Partner</th></tr>
          <tr><td><span class="flag">🇰🇪</span> Kenya (Nairobi, Mombasa)</td><td>15,000 TZS</td><td>30,000 TZS</td><td>G4S, Fargo Courier</td></tr>
          <tr><td><span class="flag">🇰🇪</span> Kenya (Other towns)</td><td>20,000 TZS</td><td>40,000 TZS</td><td>G4S, Posta Kenya</td></tr>
          <tr><td><span class="flag">🇺🇬</span> Uganda (Kampala)</td><td>18,000 TZS</td><td>35,000 TZS</td><td>DHL, Posta Uganda</td></tr>
          <tr><td><span class="flag">🇺🇬</span> Uganda (Other towns)</td><td>25,000 TZS</td><td>45,000 TZS</td><td>DHL, local courier</td></tr>
          <tr><td><span class="flag">🇷🇼</span> Rwanda (Kigali)</td><td>20,000 TZS</td><td>40,000 TZS</td><td>DHL, Rwanda Post</td></tr>
          <tr><td><span class="flag">🇷🇼</span> Rwanda (Other towns)</td><td>28,000 TZS</td><td>50,000 TZS</td><td>DHL</td></tr>
          <tr><td><span class="flag">🇧🇮</span> Burundi (Bujumbura)</td><td>25,000 TZS</td><td>50,000 TZS</td><td>DHL, Posta Burundi</td></tr>
          <tr><td><span class="flag">🇸🇸</span> South Sudan (Juba)</td><td>35,000 TZS</td><td>70,000 TZS</td><td>DHL, local freight</td></tr>
          <tr><td><span class="flag">🇨🇩</span> DRC (Goma, Bukavu)</td><td>30,000 TZS</td><td>60,000 TZS</td><td>DHL, cross-border</td></tr>
        </table>
      </div>
      <div class="delivery-note">
        <p><strong>🛃 Customs:</strong> EAC orders may be subject to customs duties and VAT in the destination country. These charges are the responsibility of the buyer. BabyBliss provides all necessary commercial invoices for smooth customs clearance.</p>
      </div>
    </div>

    <!-- International -->
    <div class="region-content" id="intl">
      <h3 style="margin-bottom:16px; color:var(--deep-rose);">🌐 International Shipping</h3>
      <div class="table-wrap">
        <table class="data-table">
          <tr><th>Region</th><th>Standard (10-20 days)</th><th>Express (5-10 days)</th><th>Courier</th></tr>
          <tr><td><span class="flag">🇿🇦</span> South Africa</td><td>45,000 TZS</td><td>85,000 TZS</td><td>DHL, FedEx</td></tr>
          <tr><td><span class="flag">🇳🇬</span> Nigeria / West Africa</td><td>55,000 TZS</td><td>110,000 TZS</td><td>DHL</td></tr>
          <tr><td><span class="flag">🇦🇪</span> UAE (Dubai)</td><td>50,000 TZS</td><td>95,000 TZS</td><td>DHL, Emirates Post</td></tr>
          <tr><td><span class="flag">🇬🇧</span> United Kingdom</td><td>65,000 TZS</td><td>120,000 TZS</td><td>DHL, Royal Mail</td></tr>
          <tr><td><span class="flag">🇺🇸</span> United States</td><td>75,000 TZS</td><td>140,000 TZS</td><td>DHL, FedEx</td></tr>
          <tr><td><span class="flag">🇪🇺</span> Europe (EU)</td><td>70,000 TZS</td><td>130,000 TZS</td><td>DHL, national post</td></tr>
          <tr><td><span class="flag">🇮🇳</span> India</td><td>50,000 TZS</td><td>90,000 TZS</td><td>DHL, India Post</td></tr>
          <tr><td><span class="flag">🇨🇳</span> China</td><td>45,000 TZS</td><td>80,000 TZS</td><td>DHL, SF Express</td></tr>
          <tr><td><span class="flag">🌏</span> Rest of World</td><td colspan="3" style="text-align:center; color:var(--text-light);">Calculated at checkout based on weight & destination</td></tr>
        </table>
      </div>
      <div class="delivery-note">
        <p><strong>📋 Important:</strong> International orders require a copy of the buyer's ID for customs. Delivery times exclude customs clearance delays (typically 2-5 days). BabyBliss is not responsible for customs seizures or import restrictions in destination countries.</p>
      </div>
    </div>
  </div>

  <div class="section">
    <h2>Order Processing</h2>
    <p>All orders are processed within 1-2 business days (Monday–Friday, excluding public holidays). Orders placed after 3PM EAT will be processed the next business day. During peak seasons (Ramadan, Christmas, school opening), processing may take an additional 1-2 days.</p>
    <div class="highlight-box">
      <p><strong>📦 Packaging:</strong> We use eco-friendly, recyclable packaging materials. Your items are carefully wrapped to ensure they arrive in perfect condition. Gift wrapping is available at checkout for 5,000 TZS per item.</p>
    </div>
  </div>

  <div class="section">
    <h2>Tracking Your Order</h2>
    <p>Once your order ships, you will receive an SMS and email with a tracking number. You can also track your order by:</p>
    <ul>
      <li>Logging into your BabyBliss account and visiting "My Orders"</li>
      <li>Clicking the tracking link in your shipping confirmation SMS/email</li>
      <li>Contacting our support team with your order number</li>
    </ul>
    <p>Please allow 24-48 hours for tracking information to update after receiving your shipping confirmation.</p>
  </div>

  <div class="section">
    <h2>Delivery Information</h2>
    <p><strong>Signature Required:</strong> Orders over 200,000 TZS require a signature upon delivery. If you are not home, the courier will call you to arrange redelivery or pickup from their local office.</p>
    <p><strong>Address Changes:</strong> We cannot change the delivery address once an order has shipped. Please ensure your shipping address is correct before completing your purchase.</p>
    <p><strong>PO Boxes:</strong> We do not deliver to PO Boxes. Please provide a physical street address with a contact phone number.</p>
    <p><strong>Remote Areas:</strong> Deliveries to remote villages may take 1-3 extra days. Our courier partners cover all district headquarters and major towns across Tanzania.</p>
  </div>

  <div class="section">
    <h2>Our Courier Partners</h2>
    <p>We work with trusted local and international couriers to ensure reliable delivery:</p>
    <ul>
      <li><strong>🇹🇿 Tanzania Bara:</strong> DHL Tanzania, UPS Tanzania, Fast Fast, Mwananchi Courier, ZanCouriers</li>
      <li><strong>🌴 Zanzibar:</strong> ZanCouriers, Zanzibar Express, local dhow + courier network</li>
      <li><strong>🌍 EAC:</strong> DHL East Africa, G4S, Fargo Courier, national postal services</li>
      <li><strong>🌐 International:</strong> DHL Express, FedEx, UPS, EMS (Tanzania Post)</li>
    </ul>
  </div>

  <div class="section">
    <h2>Lost or Damaged Packages</h2>
    <p>If your package is lost or arrives damaged, please contact us within 7 days of the estimated delivery date. We will work with the courier to locate your package or file a claim. For damaged items, please keep all packaging and take photos of the damage for our records.</p>
    <div class="highlight-box">
      <p><strong>📞 Contact:</strong> Call us at <strong>+255 743 123 456</strong> or WhatsApp <strong>+255 743 123 456</strong> for urgent delivery issues.</p>
    </div>
  </div>

</div>

<footer class="page-footer">
  <p>© 2026 BabyBliss. All rights reserved. | <a href="privacy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
</footer>

<script>
  function showRegion(region) {
    document.querySelectorAll('.region-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.region-tab').forEach(t => t.classList.remove('active'));
    document.getElementById(region).classList.add('active');
    event.target.classList.add('active');
  }
</script>

</body>
</html>