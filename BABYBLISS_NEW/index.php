<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BabyBliss – Nurture Every Moment</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --cream: #FFF8F0;
      --blush: #F2A7B3;
      --rose: #E8738A;
      --deep-rose: #C44D65;
      --mint: #A8D8C8;
      --mint-dark: #5FB8A0;
      --sky: #B8D8E8;
      --gold: #F5C842;
      --warm-brown: #7C5C4A;
      --soft-brown: #C4997A;
      --white: #FFFFFF;
      --text-dark: #2D1B14;
      --text-mid: #6B4C3B;
      --text-light: #A07D6A;
      --shadow: rgba(196,77,101,0.12);
      --radius: 20px;
      --radius-sm: 12px;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--cream);
      color: var(--text-dark);
      overflow-x: hidden;
    }

    /* ── HEADER ── */
    header {
      background: var(--white);
      position: sticky; top: 0; z-index: 999;
      box-shadow: 0 2px 20px var(--shadow);
    }
    .header-top {
      background: var(--deep-rose);
      text-align: center;
      padding: 6px;
      font-size: 13px;
      color: var(--white);
      letter-spacing: 0.5px;
    }
    .header-main {
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 48px;
      height: 72px;
    }
    .logo {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none;
    }
    .logo-icon {
      width: 44px; height: 44px;
      background: linear-gradient(135deg, var(--blush), var(--deep-rose));
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px;
    }
    .logo-text {
      font-family: 'Playfair Display', serif;
      font-size: 26px; font-weight: 700;
      color: var(--deep-rose);
      line-height: 1;
    }
    .logo-sub {
      font-size: 10px; font-weight: 300;
      color: var(--text-light); letter-spacing: 2px;
      text-transform: uppercase;
    }
    nav { display: flex; gap: 28px; align-items: center; }
    nav a {
      font-size: 15px; font-weight: 500;
      color: var(--text-mid); text-decoration: none;
      position: relative; padding: 4px 0;
      transition: color 0.2s;
    }
    nav a::after {
      content: ''; position: absolute;
      bottom: -2px; left: 0; width: 0; height: 2px;
      background: var(--rose); border-radius: 2px;
      transition: width 0.3s;
    }
    nav a:hover { color: var(--deep-rose); }
    nav a:hover::after { width: 100%; }
    .header-actions { display: flex; align-items: center; gap: 12px; }
    .icon-btn {
      position: relative; background: none; border: none;
      font-size: 20px; color: var(--text-mid);
      cursor: pointer; padding: 8px;
      border-radius: 10px; transition: all 0.2s;
    }
    .icon-btn:hover { background: var(--cream); color: var(--deep-rose); }
    .badge {
      position: absolute; top: 2px; right: 2px;
      background: var(--deep-rose); color: var(--white);
      font-size: 10px; font-weight: 600;
      width: 18px; height: 18px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
    }
    .btn-login {
      padding: 9px 22px; border-radius: 10px;
      font-size: 14px; font-weight: 600; cursor: pointer;
      border: 2px solid var(--rose); color: var(--rose);
      background: transparent; transition: all 0.2s;
      text-decoration: none;
    }
    .btn-login:hover { background: var(--rose); color: var(--white); }
    .btn-register {
      padding: 9px 22px; border-radius: 10px;
      font-size: 14px; font-weight: 600; cursor: pointer;
      border: none; background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); transition: all 0.2s;
      text-decoration: none;
    }
    .btn-register:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(196,77,101,0.35); }

    /* ── CAROUSEL ── */
    .carousel-wrap { position: relative; overflow: hidden; }
    .carousel {
      display: flex; transition: transform 0.6s cubic-bezier(.4,0,.2,1);
    }
    .slide {
      min-width: 100%; height: 520px;
      display: flex; align-items: center;
      position: relative; overflow: hidden;
    }
    .slide-1 { background: linear-gradient(135deg, #FDE8F0 0%, #FFDCEA 50%, #FFB8CE 100%); }
    .slide-2 { background: linear-gradient(135deg, #E8F8F5 0%, #C8EDE6 50%, #A8D8C8 100%); }
    .slide-3 { background: linear-gradient(135deg, #FFF3D4 0%, #FFE8A8 50%, #FFD870 100%); }
    .slide-4 { background: linear-gradient(135deg, #E8F0FF 0%, #C8D8FF 50%, #A8B8FF 100%); }
    .slide-content {
      position: relative; z-index: 2;
      padding: 0 80px; max-width: 580px;
      animation: fadeInLeft 0.7s ease both;
    }
    @keyframes fadeInLeft {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }
    .slide-tag {
      display: inline-block;
      background: var(--deep-rose); color: var(--white);
      font-size: 11px; font-weight: 600; letter-spacing: 2px;
      text-transform: uppercase; padding: 5px 14px;
      border-radius: 20px; margin-bottom: 16px;
    }
    .slide h1 {
      font-family: 'Playfair Display', serif;
      font-size: 52px; line-height: 1.1;
      color: var(--text-dark); margin-bottom: 14px;
    }
    .slide p { font-size: 17px; color: var(--text-mid); margin-bottom: 28px; line-height: 1.6; }
    .slide-cta {
      display: inline-flex; align-items: center; gap: 10px;
      padding: 14px 30px; border-radius: 14px;
      background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); font-size: 15px; font-weight: 600;
      text-decoration: none; transition: all 0.25s;
    }
    .slide-cta:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(196,77,101,0.4); }
    .slide-img {
      position: absolute; right: 80px; bottom: 0;
      font-size: 220px; line-height: 1; opacity: 0.18;
      filter: drop-shadow(0 20px 40px rgba(0,0,0,0.1));
    }
    .slide-img-main {
      position: absolute; right: 60px; bottom: 0;
      font-size: 160px; animation: float 3s ease-in-out infinite;
    }
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-16px)} }
    .carousel-btn {
      position: absolute; top: 50%; transform: translateY(-50%);
      z-index: 10; background: var(--white);
      border: none; width: 48px; height: 48px;
      border-radius: 50%; cursor: pointer; font-size: 18px;
      color: var(--deep-rose); box-shadow: 0 4px 16px var(--shadow);
      display: flex; align-items: center; justify-content: center;
      transition: all 0.2s;
    }
    .carousel-btn:hover { background: var(--deep-rose); color: var(--white); }
    .carousel-btn.prev { left: 24px; }
    .carousel-btn.next { right: 24px; }
    .carousel-dots {
      position: absolute; bottom: 18px; left: 50%; transform: translateX(-50%);
      display: flex; gap: 8px;
    }
    .dot {
      width: 8px; height: 8px; border-radius: 4px;
      background: rgba(196,77,101,0.3); cursor: pointer;
      transition: all 0.3s;
    }
    .dot.active { width: 24px; background: var(--deep-rose); }

    /* ── SECTION TITLE ── */
    .section { padding: 64px 48px; }
    .section-header { text-align: center; margin-bottom: 40px; }
    .section-label {
      font-size: 12px; font-weight: 600; letter-spacing: 3px;
      text-transform: uppercase; color: var(--rose); margin-bottom: 10px;
    }
    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: 38px; font-weight: 700; color: var(--text-dark);
      margin-bottom: 12px;
    }
    .section-sub { font-size: 16px; color: var(--text-light); max-width: 480px; margin: 0 auto; }
    .divider {
      width: 56px; height: 3px; border-radius: 2px;
      background: linear-gradient(90deg, var(--rose), var(--blush));
      margin: 14px auto 0;
    }

    /* ── CATEGORIES ── */
    .categories { background: var(--white); }
    .category-grid {
      display: grid; grid-template-columns: repeat(6,1fr); gap: 18px;
    }
    .cat-card {
      background: var(--cream); border-radius: var(--radius);
      padding: 24px 16px; text-align: center;
      cursor: pointer; transition: all 0.3s;
      border: 2px solid transparent;
    }
    .cat-card:hover, .cat-card.active {
      border-color: var(--rose);
      background: linear-gradient(135deg, #FFF0F3, #FFE4EA);
      transform: translateY(-4px);
      box-shadow: 0 12px 30px var(--shadow);
    }
    .cat-icon { font-size: 42px; margin-bottom: 10px; }
    .cat-name { font-size: 13px; font-weight: 600; color: var(--text-mid); }
    .cat-count { font-size: 11px; color: var(--text-light); margin-top: 3px; }

    /* ── TRENDING BANNER ── */
    .trending-banner {
      background: linear-gradient(135deg, var(--deep-rose) 0%, #A03050 100%);
      padding: 36px 48px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .trending-banner h2 {
      font-family: 'Playfair Display', serif;
      font-size: 34px; color: var(--white);
    }
    .trending-banner p { color: rgba(255,255,255,0.8); margin-top: 6px; font-size: 15px; }
    .trend-badges { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
    .trend-badge {
      background: rgba(255,255,255,0.15); color: var(--white);
      border: 1px solid rgba(255,255,255,0.3); border-radius: 20px;
      padding: 6px 16px; font-size: 13px; font-weight: 500;
      backdrop-filter: blur(6px);
    }
    .trending-btn {
      padding: 14px 32px; background: var(--white);
      color: var(--deep-rose); border-radius: 12px;
      font-weight: 700; font-size: 15px; cursor: pointer;
      border: none; white-space: nowrap;
      transition: all 0.2s;
    }
    .trending-btn:hover { transform: scale(1.04); }

    /* ── TRENDING PRODUCTS ── */
    .trending-section { background: #FFF2F5; }
    .products-grid {
      display: grid; grid-template-columns: repeat(4,1fr); gap: 24px;
    }
    .product-card {
      background: var(--white); border-radius: var(--radius);
      overflow: hidden; transition: all 0.3s;
      position: relative; cursor: pointer;
    }
    .product-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 50px rgba(196,77,101,0.15);
    }
    .product-badge {
      position: absolute; top: 14px; left: 14px;
      background: var(--deep-rose); color: var(--white);
      font-size: 11px; font-weight: 700; padding: 4px 10px;
      border-radius: 8px; z-index: 2;
    }
    .product-badge.new { background: var(--mint-dark); }
    .product-badge.sale { background: var(--gold); color: var(--text-dark); }
    .product-img {
      width: 100%; height: 220px;
      background: linear-gradient(135deg, #FFF0F5, #FFE4EA);
      display: flex; align-items: center; justify-content: center;
      font-size: 90px; position: relative;
      transition: all 0.3s;
    }
    .product-card:hover .product-img { background: linear-gradient(135deg, #FFE4EA, #FFD0DC); }
    .product-actions {
      position: absolute; top: 14px; right: 14px;
      display: flex; flex-direction: column; gap: 8px;
      opacity: 0; transition: all 0.3s; transform: translateX(10px);
    }
    .product-card:hover .product-actions { opacity: 1; transform: translateX(0); }
    .action-btn {
      width: 36px; height: 36px; border-radius: 50%;
      background: var(--white); border: none; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: var(--text-mid);
      box-shadow: 0 2px 10px rgba(0,0,0,0.12);
      transition: all 0.2s;
    }
    .action-btn:hover { background: var(--deep-rose); color: var(--white); }
    .product-body { padding: 18px 20px 20px; }
    .product-cat { font-size: 11px; color: var(--rose); font-weight: 600; letter-spacing: 1px; text-transform: uppercase; }
    .product-name { font-size: 16px; font-weight: 600; color: var(--text-dark); margin: 5px 0 8px; }
    .product-rating { display: flex; align-items: center; gap: 6px; margin-bottom: 12px; }
    .stars { color: var(--gold); font-size: 13px; }
    .review-count { font-size: 12px; color: var(--text-light); }
    .product-price { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .price-current { font-size: 22px; font-weight: 700; color: var(--deep-rose); }
    .price-old { font-size: 15px; color: var(--text-light); text-decoration: line-through; }
    .btn-cart {
      width: 100%; padding: 12px;
      background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); border: none; border-radius: 12px;
      font-size: 14px; font-weight: 600; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      transition: all 0.25s;
    }
    .btn-cart:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(196,77,101,0.35); }

    /* ── ALL PRODUCTS ── */
    .products-section { background: var(--cream); }
    .products-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 28px;
    }
    .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; }
    .filter-chip {
      padding: 8px 18px; border-radius: 20px;
      font-size: 13px; font-weight: 500; cursor: pointer;
      border: 2px solid #E8D5D0; color: var(--text-mid);
      background: var(--white); transition: all 0.2s;
    }
    .filter-chip:hover, .filter-chip.active {
      border-color: var(--rose); color: var(--rose);
      background: #FFF0F3;
    }
    .sort-select {
      padding: 9px 16px; border-radius: 10px;
      border: 2px solid #E8D5D0; font-family: 'DM Sans', sans-serif;
      font-size: 14px; color: var(--text-mid);
      background: var(--white); cursor: pointer; outline: none;
    }

    /* ── PROMO BANNERS ── */
    .promo-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
      padding: 0 48px 64px;
    }
    .promo-card {
      border-radius: var(--radius); padding: 40px 48px;
      display: flex; align-items: center; justify-content: space-between;
      overflow: hidden; position: relative; cursor: pointer;
      transition: all 0.3s;
    }
    .promo-card:hover { transform: translateY(-4px); }
    .promo-1 { background: linear-gradient(135deg, #FFD6E0, #FFB3C6); }
    .promo-2 { background: linear-gradient(135deg, #C8F0E8, #90D8C8); }
    .promo-content h3 {
      font-family: 'Playfair Display', serif;
      font-size: 26px; color: var(--text-dark); margin-bottom: 8px;
    }
    .promo-content p { font-size: 14px; color: var(--text-mid); margin-bottom: 18px; }
    .promo-btn {
      padding: 10px 24px; border-radius: 10px;
      background: var(--text-dark); color: var(--white);
      font-size: 14px; font-weight: 600; border: none; cursor: pointer;
      transition: all 0.2s;
    }
    .promo-btn:hover { background: var(--deep-rose); }
    .promo-emoji { font-size: 80px; }

    /* ── FOOTER ── */
    footer {
      background: #1E0F0A;
      color: rgba(255,255,255,0.8);
    }
    .footer-top {
      background: linear-gradient(135deg, var(--deep-rose), #A03050);
      padding: 40px 48px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .footer-top h3 {
      font-family: 'Playfair Display', serif;
      font-size: 24px; color: var(--white);
    }
    .footer-top p { color: rgba(255,255,255,0.8); margin-top: 4px; }
    .newsletter-form { display: flex; gap: 10px; }
    .newsletter-form input {
      padding: 13px 20px; border-radius: 12px;
      border: none; font-size: 14px; width: 280px;
      font-family: 'DM Sans', sans-serif;
    }
    .newsletter-form button {
      padding: 13px 24px; border-radius: 12px;
      background: var(--white); color: var(--deep-rose);
      font-weight: 700; font-size: 14px; border: none; cursor: pointer;
    }
    .footer-main {
      display: grid; grid-template-columns: 2fr 1fr 1fr 1.2fr;
      gap: 48px; padding: 56px 48px;
    }
    .footer-logo { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--white); margin-bottom: 14px; }
    .footer-about { font-size: 14px; line-height: 1.7; margin-bottom: 20px; }
    .footer-social { display: flex; gap: 10px; }
    .social-btn {
      width: 40px; height: 40px; border-radius: 10px;
      background: rgba(255,255,255,0.08); border: none;
      color: rgba(255,255,255,0.7); font-size: 16px;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; transition: all 0.2s;
    }
    .social-btn:hover { background: var(--rose); color: var(--white); }
    .footer-col h4 {
      font-size: 14px; font-weight: 700; letter-spacing: 1px;
      text-transform: uppercase; color: var(--white);
      margin-bottom: 18px; padding-bottom: 10px;
      border-bottom: 1px solid rgba(255,255,255,0.12);
    }
    .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .footer-col ul li a {
      font-size: 14px; color: rgba(255,255,255,0.65);
      text-decoration: none; transition: color 0.2s;
    }
    .footer-col ul li a:hover { color: var(--blush); }
    .contact-item { display: flex; gap: 12px; align-items: flex-start; margin-bottom: 16px; }
    .contact-item i { color: var(--blush); font-size: 16px; margin-top: 2px; min-width: 18px; }
    .contact-item span { font-size: 14px; color: rgba(255,255,255,0.65); line-height: 1.5; }
    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,0.08);
      padding: 20px 48px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .footer-bottom p { font-size: 13px; color: rgba(255,255,255,0.4); }
    .payment-icons { display: flex; gap: 10px; }
    .pay-icon {
      background: rgba(255,255,255,0.08); border-radius: 6px;
      padding: 6px 12px; font-size: 12px; color: rgba(255,255,255,0.6);
      font-weight: 600;
    }

    /* SCROLL ANIMATE */
    .fade-in {
      opacity: 0; transform: translateY(30px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .fade-in.visible { opacity: 1; transform: translateY(0); }

    /* CART SIDEBAR */
    .cart-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.4);
      z-index: 2000; opacity: 0; pointer-events: none; transition: opacity 0.3s;
    }
    .cart-overlay.open { opacity: 1; pointer-events: all; }
    .cart-sidebar {
      position: fixed; top: 0; right: -420px; width: 420px; height: 100vh;
      background: var(--white); z-index: 2001;
      transition: right 0.4s cubic-bezier(.4,0,.2,1);
      display: flex; flex-direction: column;
    }
    .cart-overlay.open .cart-sidebar { right: 0; }
    .cart-header {
      padding: 24px 28px; border-bottom: 1px solid #F0E8E4;
      display: flex; align-items: center; justify-content: space-between;
    }
    .cart-header h3 { font-family: 'Playfair Display', serif; font-size: 22px; }
    .close-btn {
      background: none; border: none; font-size: 20px;
      cursor: pointer; color: var(--text-mid); padding: 4px;
    }
    .cart-items { flex: 1; overflow-y: auto; padding: 20px 28px; }
    .cart-item { display: flex; gap: 16px; margin-bottom: 20px; }
    .cart-item-img {
      width: 70px; height: 70px; border-radius: 12px;
      background: var(--cream); font-size: 36px;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .cart-item-info { flex: 1; }
    .cart-item-name { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
    .cart-item-price { font-size: 15px; font-weight: 700; color: var(--deep-rose); }
    .cart-qty { display: flex; align-items: center; gap: 10px; margin-top: 8px; }
    .qty-btn {
      width: 28px; height: 28px; border-radius: 8px;
      border: 1px solid #E8D5D0; background: none;
      cursor: pointer; font-size: 14px; color: var(--text-mid);
    }
    .cart-footer { padding: 20px 28px; border-top: 1px solid #F0E8E4; }
    .cart-total { display: flex; justify-content: space-between; margin-bottom: 16px; }
    .cart-total span:first-child { font-size: 14px; color: var(--text-light); }
    .cart-total span:last-child { font-size: 20px; font-weight: 700; color: var(--text-dark); }
    .btn-checkout {
      width: 100%; padding: 15px;
      background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); border: none; border-radius: 14px;
      font-size: 16px; font-weight: 700; cursor: pointer;
    }

    @media (max-width: 900px) {
      .category-grid { grid-template-columns: repeat(3,1fr); }
      .products-grid { grid-template-columns: repeat(2,1fr); }
      .footer-main { grid-template-columns: 1fr 1fr; }
      .header-main { padding: 0 20px; }
      nav { display: none; }
    }
  </style>
</head>
<body>

<!-- HEADER -->
<header>
  <div class="header-top">🌸 Free shipping on orders over $50 · Use code BABY20 for 20% off your first order 🍼</div>
  <div class="header-main">
    <a href="index.html" class="logo">
      <div class="logo-icon">🍼</div>
      <div>
        <div class="logo-text">BabyBliss</div>
        <div class="logo-sub">Nurture Every Moment</div>
      </div>
    </a>
    <nav>
      <a href="#">Home</a>
      <a href="#">Shop</a>
      <a href="#">Toys</a>
      <a href="#">Clothing</a>
      <a href="#">Nursery</a>
      <a href="#">Sale</a>
    </nav>
    <div class="header-actions">
      <button class="icon-btn"><i class="fas fa-search"></i></button>
     
       
           <a href="cart.php" class="icon-btn" class="fas fa-shopping-cart">cart</a>
           
        </div>
        
      </button>
      <button class="icon-btn">
        <i class="fas fa-heart"></i>
        <span class="badge">5</span>
      </button>
      <a href="login.php" class="btn-login">Login</a>
      <a href="register.php" class="btn-register">Register</a>
    </div>
  </div>
</header>

<!-- CAROUSEL -->
<div class="carousel-wrap">
  <div class="carousel" id="carousel">
    <div class="slide slide-1">
      <div class="slide-content">
        <div class="slide-tag">✨ New Arrivals</div>
        <h1>Magical Toys for Little Explorers</h1>
        <p>Discover beautifully crafted toys that spark imagination and nurture development at every stage.</p>
        <a href="#" class="slide-cta"><i class="fas fa-shopping-bag"></i> Shop Now</a>
      </div>
      <div class="slide-img">🧸</div>
      <div class="slide-img-main">🧸</div>
    </div>
    <div class="slide slide-2">
      <div class="slide-content">
        <div class="slide-tag" style="background:var(--mint-dark)">🌿 Eco-Friendly</div>
        <h1>Safe & Natural Baby Essentials</h1>
        <p>Organic, non-toxic, and lovingly designed products for your precious little ones.</p>
        <a href="#" class="slide-cta" style="background:linear-gradient(135deg,var(--mint-dark),#3A9E88)"><i class="fas fa-leaf"></i> Explore</a>
      </div>
      <div class="slide-img">🌿</div>
      <div class="slide-img-main">🎀</div>
    </div>
    <div class="slide slide-3">
      <div class="slide-content">
        <div class="slide-tag" style="background:var(--gold);color:var(--text-dark)">⚡ Hot Deals</div>
        <h1>Summer Sale – Up to 40% Off!</h1>
        <p>Grab amazing deals on our most popular baby products before they're gone.</p>
        <a href="#" class="slide-cta" style="background:linear-gradient(135deg,#E8A800,#C88A00)"><i class="fas fa-tag"></i> View Deals</a>
      </div>
      <div class="slide-img">⭐</div>
      <div class="slide-img-main">🎉</div>
    </div>
    <div class="slide slide-4">
      <div class="slide-content">
        <div class="slide-tag" style="background:#6678CC">🎓 Learning</div>
        <h1>Smart Toys That Grow with Your Child</h1>
        <p>Educational toys designed by child development experts to make learning joyful.</p>
        <a href="#" class="slide-cta" style="background:linear-gradient(135deg,#6678CC,#4455AA)"><i class="fas fa-star"></i> Discover</a>
      </div>
      <div class="slide-img">📚</div>
      <div class="slide-img-main">🚀</div>
    </div>
  </div>
  <button class="carousel-btn prev" onclick="moveSlide(-1)"><i class="fas fa-chevron-left"></i></button>
  <button class="carousel-btn next" onclick="moveSlide(1)"><i class="fas fa-chevron-right"></i></button>
  <div class="carousel-dots" id="dots">
    <div class="dot active" onclick="goSlide(0)"></div>
    <div class="dot" onclick="goSlide(1)"></div>
    <div class="dot" onclick="goSlide(2)"></div>
    <div class="dot" onclick="goSlide(3)"></div>
  </div>
</div>

<!-- CATEGORIES -->
<section class="section categories">
  <div class="section-header">
    <div class="section-label">Browse by Category</div>
    <div class="section-title">Shop by Age & Type</div>
    <div class="divider"></div>
  </div>
  <div class="category-grid fade-in">
    <div class="cat-card active" onclick="filterCategory(this)">
      <div class="cat-icon">🧸</div>
      <div class="cat-name">Plush Toys</div>
      <div class="cat-count">48 items</div>
    </div>
    <div class="cat-card" onclick="filterCategory(this)">
      <div class="cat-icon">🎲</div>
      <div class="cat-name">Board Games</div>
      <div class="cat-count">32 items</div>
    </div>
    <div class="cat-card" onclick="filterCategory(this)">
      <div class="cat-icon">🎨</div>
      <div class="cat-name">Art & Craft</div>
      <div class="cat-count">27 items</div>
    </div>
    <div class="cat-card" onclick="filterCategory(this)">
      <div class="cat-icon">🚂</div>
      <div class="cat-name">Vehicles</div>
      <div class="cat-count">41 items</div>
    </div>
    <div class="cat-card" onclick="filterCategory(this)">
      <div class="cat-icon">🎪</div>
      <div class="cat-name">Outdoor Play</div>
      <div class="cat-count">19 items</div>
    </div>
    <div class="cat-card" onclick="filterCategory(this)">
      <div class="cat-icon">🧩</div>
      <div class="cat-name">Puzzles</div>
      <div class="cat-count">55 items</div>
    </div>
  </div>
</section>

<!-- TRENDING BANNER -->
<div class="trending-banner">
  <div>
    <h2>🔥 Trending This Week</h2>
    <p>What other parents are loving right now</p>
    <div class="trend-badges">
      <span class="trend-badge">#1 Soft Plush Bears</span>
      <span class="trend-badge">#2 Baby Mobiles</span>
      <span class="trend-badge">#3 Wooden Blocks</span>
      <span class="trend-badge">#4 Musical Mats</span>
    </div>
  </div>
  <button class="trending-btn">View All Trending →</button>
</div>

<!-- TRENDING PRODUCTS -->
<section class="section trending-section">
  <div class="section-header">
    <div class="section-label">Most Popular</div>
    <div class="section-title">Trending Products</div>
    <div class="section-sub">Handpicked favorites that parents and babies love</div>
    <div class="divider"></div>
  </div>
  <div class="products-grid fade-in">
    <!-- Card 1 -->
    <div class="product-card">
      <div class="product-badge">HOT</div>
      <div class="product-img">
        🐻
        <div class="product-actions">
          <button class="action-btn" title="Wishlist"><i class="fas fa-heart"></i></button>
          <button class="action-btn" title="Quick View"><i class="fas fa-eye"></i></button>
        </div>
      </div>
      <div class="product-body">
        <div class="product-cat">Plush Toys</div>
        <div class="product-name">Cuddle Bear Plush</div>
        <div class="product-rating">
          <span class="stars">★★★★★</span>
          <span class="review-count">(128)</span>
        </div>
        <div class="product-price">
          <span class="price-current">$24.99</span>
          <span class="price-old">$34.99</span>
        </div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <!-- Card 2 -->
    <div class="product-card">
      <div class="product-badge new">NEW</div>
      <div class="product-img">
        🧩
        <div class="product-actions">
          <button class="action-btn"><i class="fas fa-heart"></i></button>
          <button class="action-btn"><i class="fas fa-eye"></i></button>
        </div>
      </div>
      <div class="product-body">
        <div class="product-cat">Puzzles</div>
        <div class="product-name">Rainbow Puzzle Set</div>
        <div class="product-rating">
          <span class="stars">★★★★☆</span>
          <span class="review-count">(94)</span>
        </div>
        <div class="product-price">
          <span class="price-current">$18.99</span>
        </div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <!-- Card 3 -->
    <div class="product-card">
      <div class="product-badge sale">SALE</div>
      <div class="product-img">
        🎵
        <div class="product-actions">
          <button class="action-btn"><i class="fas fa-heart"></i></button>
          <button class="action-btn"><i class="fas fa-eye"></i></button>
        </div>
      </div>
      <div class="product-body">
        <div class="product-cat">Musical</div>
        <div class="product-name">Musical Activity Gym</div>
        <div class="product-rating">
          <span class="stars">★★★★★</span>
          <span class="review-count">(203)</span>
        </div>
        <div class="product-price">
          <span class="price-current">$39.99</span>
          <span class="price-old">$59.99</span>
        </div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <!-- Card 4 -->
    <div class="product-card">
      <div class="product-img">
        🚂
        <div class="product-actions">
          <button class="action-btn"><i class="fas fa-heart"></i></button>
          <button class="action-btn"><i class="fas fa-eye"></i></button>
        </div>
      </div>
      <div class="product-body">
        <div class="product-cat">Vehicles</div>
        <div class="product-name">Wooden Train Set</div>
        <div class="product-rating">
          <span class="stars">★★★★★</span>
          <span class="review-count">(167)</span>
        </div>
        <div class="product-price">
          <span class="price-current">$44.99</span>
          <span class="price-old">$54.99</span>
        </div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
  </div>
</section>

<!-- ALL PRODUCTS -->
<section class="section products-section">
  <div class="section-header">
    <div class="section-label">Our Collection</div>
    <div class="section-title">All Products</div>
    <div class="divider"></div>
  </div>
  <div class="products-header">
    <div class="filter-bar">
      <div class="filter-chip active">All</div>
      <div class="filter-chip">0–12 Months</div>
      <div class="filter-chip">1–3 Years</div>
      <div class="filter-chip">3–5 Years</div>
      <div class="filter-chip">Under $25</div>
      <div class="filter-chip">On Sale</div>
    </div>
    <select class="sort-select">
      <option>Sort: Featured</option>
      <option>Price: Low to High</option>
      <option>Price: High to Low</option>
      <option>Newest First</option>
      <option>Best Rated</option>
    </select>
  </div>
  <div class="products-grid fade-in">
    <div class="product-card">
      <div class="product-img">🦒<div class="product-actions"><button class="action-btn"><i class="fas fa-heart"></i></button><button class="action-btn"><i class="fas fa-eye"></i></button></div></div>
      <div class="product-body">
        <div class="product-cat">Plush Toys</div>
        <div class="product-name">Sophie the Giraffe</div>
        <div class="product-rating"><span class="stars">★★★★★</span><span class="review-count">(312)</span></div>
        <div class="product-price"><span class="price-current">$29.99</span></div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <div class="product-card">
      <div class="product-badge sale">-30%</div>
      <div class="product-img">🎨<div class="product-actions"><button class="action-btn"><i class="fas fa-heart"></i></button><button class="action-btn"><i class="fas fa-eye"></i></button></div></div>
      <div class="product-body">
        <div class="product-cat">Art & Craft</div>
        <div class="product-name">Finger Paint Kit</div>
        <div class="product-rating"><span class="stars">★★★★☆</span><span class="review-count">(87)</span></div>
        <div class="product-price"><span class="price-current">$14.99</span><span class="price-old">$21.99</span></div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <div class="product-card">
      <div class="product-img">🪀<div class="product-actions"><button class="action-btn"><i class="fas fa-heart"></i></button><button class="action-btn"><i class="fas fa-eye"></i></button></div></div>
      <div class="product-body">
        <div class="product-cat">Outdoor Play</div>
        <div class="product-name">Classic Yo-Yo Set</div>
        <div class="product-rating"><span class="stars">★★★☆☆</span><span class="review-count">(45)</span></div>
        <div class="product-price"><span class="price-current">$9.99</span></div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <div class="product-card">
      <div class="product-badge new">NEW</div>
      <div class="product-img">🌙<div class="product-actions"><button class="action-btn"><i class="fas fa-heart"></i></button><button class="action-btn"><i class="fas fa-eye"></i></button></div></div>
      <div class="product-body">
        <div class="product-cat">Nursery</div>
        <div class="product-name">Dream Night Light</div>
        <div class="product-rating"><span class="stars">★★★★★</span><span class="review-count">(176)</span></div>
        <div class="product-price"><span class="price-current">$32.99</span></div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <div class="product-card">
      <div class="product-img">🧁<div class="product-actions"><button class="action-btn"><i class="fas fa-heart"></i></button><button class="action-btn"><i class="fas fa-eye"></i></button></div></div>
      <div class="product-body">
        <div class="product-cat">Pretend Play</div>
        <div class="product-name">Play Kitchen Deluxe</div>
        <div class="product-rating"><span class="stars">★★★★★</span><span class="review-count">(234)</span></div>
        <div class="product-price"><span class="price-current">$79.99</span><span class="price-old">$99.99</span></div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <div class="product-card">
      <div class="product-img">🏰<div class="product-actions"><button class="action-btn"><i class="fas fa-heart"></i></button><button class="action-btn"><i class="fas fa-eye"></i></button></div></div>
      <div class="product-body">
        <div class="product-cat">Pretend Play</div>
        <div class="product-name">Fairy Castle Playhouse</div>
        <div class="product-rating"><span class="stars">★★★★☆</span><span class="review-count">(112)</span></div>
        <div class="product-price"><span class="price-current">$59.99</span></div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <div class="product-card">
      <div class="product-badge">HOT</div>
      <div class="product-img">🎠<div class="product-actions"><button class="action-btn"><i class="fas fa-heart"></i></button><button class="action-btn"><i class="fas fa-eye"></i></button></div></div>
      <div class="product-body">
        <div class="product-cat">Nursery</div>
        <div class="product-name">Musical Crib Mobile</div>
        <div class="product-rating"><span class="stars">★★★★★</span><span class="review-count">(289)</span></div>
        <div class="product-price"><span class="price-current">$36.99</span><span class="price-old">$49.99</span></div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
    <div class="product-card">
      <div class="product-img">🏄<div class="product-actions"><button class="action-btn"><i class="fas fa-heart"></i></button><button class="action-btn"><i class="fas fa-eye"></i></button></div></div>
      <div class="product-body">
        <div class="product-cat">Outdoor Play</div>
        <div class="product-name">Splash Water Mat</div>
        <div class="product-rating"><span class="stars">★★★★☆</span><span class="review-count">(78)</span></div>
        <div class="product-price"><span class="price-current">$22.99</span></div>
        <button class="btn-cart" onclick="addToCart(this)"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
      </div>
    </div>
  </div>
</section>

<!-- PROMO BANNERS -->
<div class="promo-grid">
  <div class="promo-card promo-1">
    <div class="promo-content">
      <h3>Bundle & Save Big</h3>
      <p>Get 3 toys and save 25% on your order. Mix and match from hundreds of options.</p>
      <button class="promo-btn">Shop Bundles</button>
    </div>
    <div class="promo-emoji">🎁</div>
  </div>
  <div class="promo-card promo-2">
    <div class="promo-content">
      <h3>Eco-Friendly Collection</h3>
      <p>Sustainable, organic toys that are safe for your baby and the planet.</p>
      <button class="promo-btn" style="background:var(--mint-dark)">Shop Eco</button>
    </div>
    <div class="promo-emoji">🌿</div>
  </div>
</div>

<!-- FOOTER -->
<footer>
  <div class="footer-top">
    <div>
      <h3>🍼 Join Our Baby Family</h3>
      <p>Get exclusive deals, parenting tips, and new arrivals right in your inbox</p>
    </div>
    <div class="newsletter-form">
      <input type="email" placeholder="Your email address..."/>
      <button>Subscribe 💌</button>
    </div>
  </div>
  <div class="footer-main">
    <div>
      <div class="footer-logo">🍼 BabyBliss</div>
      <p class="footer-about">We believe every baby deserves the best start in life. Our carefully curated collection of toys and essentials is designed to nurture, inspire, and bring joy to your little one's world.</p>
      <div class="footer-social">
        <button class="social-btn"><i class="fab fa-facebook-f"></i></button>
        <button class="social-btn"><i class="fab fa-instagram"></i></button>
        <button class="social-btn"><i class="fab fa-pinterest-p"></i></button>
        <button class="social-btn"><i class="fab fa-tiktok"></i></button>
        <button class="social-btn"><i class="fab fa-youtube"></i></button>
      </div>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Our Story</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#">Careers</a></li>
        <li><a href="#">Press</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Customer Care</h4>
      <ul>
        <li><a href="#">FAQ</a></li>
        <li><a href="#">Shipping Policy</a></li>
        <li><a href="#">Return Policy</a></li>
        <li><a href="#">Size Guide</a></li>
        <li><a href="#">Track Order</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Contact Us</h4>
      <div class="contact-item">
        <i class="fas fa-map-marker-alt"></i>
        <span>123 Blossom Lane, Suite 400<br/>San Francisco, CA 94105, USA</span>
      </div>
      <div class="contact-item">
        <i class="fas fa-phone"></i>
        <span>+1 (800) 555-BABY<br/>Mon–Fri, 9AM – 6PM PST</span>
      </div>
      <div class="contact-item">
        <i class="fas fa-envelope"></i>
        <span>hello@babybliss.com</span>
      </div>
      <div class="contact-item">
        <i class="fas fa-clock"></i>
        <span>24/7 Live Chat Available</span>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2026 - <?php echo date('Y') ?> BabyBliss. All rights reserved. Made with 💕 for little ones.</p>
    <div class="payment-icons">
      <div class="pay-icon">VISA</div>
      <div class="pay-icon">MC</div>
      <div class="pay-icon">AMEX</div>
      <div class="pay-icon">PayPal</div>
      <div class="pay-icon">Apple Pay</div>
    </div>
  </div>
</footer>

<!-- CART SIDEBAR -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart(event)">
  <div class="cart-sidebar">
    <div class="cart-header">
      <h3>🛒 Your Cart (3)</h3>
      <button class="close-btn" onclick="toggleCart()">✕</button>
    </div>
    <div class="cart-items">
      <div class="cart-item">
        <div class="cart-item-img">🐻</div>
        <div class="cart-item-info">
          <div class="cart-item-name">Cuddle Bear Plush</div>
          <div class="cart-item-price">$24.99</div>
          <div class="cart-qty">
            <button class="qty-btn">−</button>
            <span>1</span>
            <button class="qty-btn">+</button>
          </div>
        </div>
      </div>
      <div class="cart-item">
        <div class="cart-item-img">🧩</div>
        <div class="cart-item-info">
          <div class="cart-item-name">Rainbow Puzzle Set</div>
          <div class="cart-item-price">$18.99</div>
          <div class="cart-qty">
            <button class="qty-btn">−</button>
            <span>2</span>
            <button class="qty-btn">+</button>
          </div>
        </div>
      </div>
      <div class="cart-item">
        <div class="cart-item-img">🎵</div>
        <div class="cart-item-info">
          <div class="cart-item-name">Musical Activity Gym</div>
          <div class="cart-item-price">$39.99</div>
          <div class="cart-qty">
            <button class="qty-btn">−</button>
            <span>1</span>
            <button class="qty-btn">+</button>
          </div>
        </div>
      </div>
    </div>
    <div class="cart-footer">
      <div class="cart-total"><span>Subtotal</span><span>$101.96</span></div>
      <button class="btn-checkout">Proceed to Checkout →</button>
    </div>
  </div>
</div>

<script>
  // Carousel
  let cur = 0; const total = 4;
  function moveSlide(dir) { cur = (cur + dir + total) % total; updateCarousel(); }
  function goSlide(i) { cur = i; updateCarousel(); }
  function updateCarousel() {
    document.getElementById('carousel').style.transform = `translateX(-${cur*100}%)`;
    document.querySelectorAll('.dot').forEach((d,i) => d.classList.toggle('active', i===cur));
  }
  setInterval(() => moveSlide(1), 5000);

  // Cart
  function toggleCart() { document.getElementById('cartOverlay').classList.toggle('open'); }
  function closeCart(e) { if(e.target === document.getElementById('cartOverlay')) toggleCart(); }

  // Category filter
  function filterCategory(el) {
    document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
  }

  // Filter chips
  document.querySelectorAll('.filter-chip').forEach(c => {
    c.addEventListener('click', () => {
      document.querySelectorAll('.filter-chip').forEach(x => x.classList.remove('active'));
      c.classList.add('active');
    });
  });

  // Add to cart
  function addToCart(btn) {
    btn.innerHTML = '<i class="fas fa-check"></i> Added!';
    btn.style.background = 'linear-gradient(135deg,var(--mint-dark),#3A9E88)';
    setTimeout(() => {
      btn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
      btn.style.background = '';
    }, 1800);
  }

  // Scroll animations
  const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('visible'); });
  }, { threshold: 0.1 });
  document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
</script>
</body>
</html>
