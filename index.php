<?php
session_start();
require_once "config.php";
 
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$is_manufacturer = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manufacturer';
 
// Pull a few trending products across all pillars for the homepage strip
$trending_query = "SELECT p.*, c.name AS category_name, c.pillar
                    FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.status='active' AND p.is_active=1
                    ORDER BY p.created_at DESC LIMIT 8";
$trending = mysqli_query($conn, $trending_query);
 
// Real, royalty-free stock photo fallback (LoremFlickr), used only when a manufacturer
// has not uploaded a real product photo yet. "lock" keeps the same photo per product.
function placeholderPhoto($product_id, $keyword = 'baby') {
    return "https://loremflickr.com/500/500/" . $keyword . "?lock=" . intval($product_id);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BabyBliss – Baby Gear, Nutrition & Parenting in One Place</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --cream:#FFF8F0; --blush:#F2A7B3; --rose:#E8738A; --deep-rose:#C44D65;
      --mint:#A8D8C8; --mint-dark:#5FB8A0; --blue:#6678CC; --blue-dark:#4455AA;
      --gold:#F5C842; --white:#FFFFFF; --text-dark:#2D1B14; --text-mid:#6B4C3B; --text-light:#A07D6A;
      --shadow: rgba(196,77,101,0.12); --radius:20px;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    html { scroll-behavior: smooth; }
    body { font-family:'DM Sans', sans-serif; background:var(--cream); color:var(--text-dark); overflow-x:hidden; }
 
    /* HEADER */
    header { background:var(--white); position:sticky; top:0; z-index:999; box-shadow:0 2px 20px var(--shadow); }
    .header-top { background:var(--deep-rose); text-align:center; padding:6px; font-size:13px; color:var(--white); letter-spacing:.5px; }
    .header-main { display:flex; align-items:center; justify-content:space-between; padding:0 48px; height:72px; }
    .logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .logo-icon { width:44px; height:44px; background:linear-gradient(135deg,var(--blush),var(--deep-rose)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; }
    .logo-text { font-family:'Playfair Display', serif; font-size:26px; font-weight:700; color:var(--deep-rose); line-height:1; }
    .logo-sub { font-size:10px; font-weight:300; color:var(--text-light); letter-spacing:2px; text-transform:uppercase; }
    .search-bar { flex:1; max-width:420px; margin:0 24px; position:relative; }
    .search-bar input { width:100%; padding:12px 20px 12px 44px; border:2px solid #F0E4DC; border-radius:12px; font-family:'DM Sans',sans-serif; font-size:14px; outline:none; transition:all .2s; }
    .search-bar input:focus { border-color:var(--rose); box-shadow:0 0 0 4px rgba(232,115,138,.1); }
    .search-bar i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--text-light); }
 
    /* PILLAR NAV — the 3 main links requested */
    .pillar-nav { display:flex; gap:8px; align-items:center; }
    .pillar-link {
      display:flex; align-items:center; gap:7px;
      padding:9px 16px; border-radius:10px;
      font-size:14px; font-weight:600; text-decoration:none;
      transition:all .2s; white-space:nowrap;
    }
    .pillar-link.home   { color:var(--text-mid); }
    .pillar-link.home:hover { background:var(--cream); color:var(--deep-rose); }
    .pillar-link.gear      { background:#FFF0F3; color:var(--deep-rose); }
    .pillar-link.gear:hover      { background:var(--rose); color:#fff; }
    .pillar-link.nutrition { background:#EAF8F4; color:var(--mint-dark); }
    .pillar-link.nutrition:hover { background:var(--mint-dark); color:#fff; }
    .pillar-link.parenting { background:#EEF0FB; color:var(--blue); }
    .pillar-link.parenting:hover { background:var(--blue); color:#fff; }
 
    .header-actions { display:flex; align-items:center; gap:10px; }
    .icon-btn { position:relative; background:none; border:none; font-size:20px; color:var(--text-mid); cursor:pointer; padding:8px; border-radius:10px; transition:all .2s; text-decoration:none; }
    .icon-btn:hover { background:var(--cream); color:var(--deep-rose); }
    .badge { position:absolute; top:2px; right:2px; background:var(--deep-rose); color:var(--white); font-size:10px; font-weight:600; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .btn-login { padding:9px 20px; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; border:2px solid var(--rose); color:var(--rose); background:transparent; transition:all .2s; text-decoration:none; }
    .btn-login:hover { background:var(--rose); color:var(--white); }
    .btn-sell { padding:9px 20px; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; border:none; background:linear-gradient(135deg,var(--mint-dark),#3A9E88); color:var(--white); transition:all .2s; text-decoration:none; }
    .btn-sell:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(95,184,160,.35); }
 
    /* CAROUSEL */
    .carousel-wrap { position:relative; overflow:hidden; }
    .carousel { display:flex; transition:transform .6s cubic-bezier(.4,0,.2,1); }
    .slide { min-width:100%; height:440px; display:flex; align-items:center; position:relative; overflow:hidden; }
    .slide-1 { background:linear-gradient(135deg,#FDE8F0 0%,#FFDCEA 50%,#FFB8CE 100%); }
    .slide-2 { background:linear-gradient(135deg,#E8F8F5 0%,#C8EDE6 50%,#A8D8C8 100%); }
    .slide-3 { background:linear-gradient(135deg,#E8F0FF 0%,#C8D8FF 50%,#A8B8FF 100%); }
    .slide-content { position:relative; z-index:2; padding:0 80px; max-width:560px; }
    .slide-tag { display:inline-block; background:var(--deep-rose); color:var(--white); font-size:11px; font-weight:600; letter-spacing:2px; text-transform:uppercase; padding:5px 14px; border-radius:20px; margin-bottom:16px; }
    .slide h1 { font-family:'Playfair Display', serif; font-size:44px; line-height:1.15; color:var(--text-dark); margin-bottom:14px; }
    .slide p { font-size:16px; color:var(--text-mid); margin-bottom:24px; line-height:1.6; }
    .slide-cta { display:inline-flex; align-items:center; gap:10px; padding:13px 28px; border-radius:14px; background:linear-gradient(135deg,var(--rose),var(--deep-rose)); color:var(--white); font-size:15px; font-weight:600; text-decoration:none; transition:all .25s; }
    .slide-cta:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(196,77,101,.4); }
    .slide-emoji { position:absolute; right:80px; bottom:0; font-size:160px; animation:float 3s ease-in-out infinite; }
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-14px)} }
    .carousel-dots { position:absolute; bottom:16px; left:50%; transform:translateX(-50%); display:flex; gap:8px; }
    .dot { width:8px; height:8px; border-radius:4px; background:rgba(196,77,101,.3); cursor:pointer; transition:all .3s; }
    .dot.active { width:24px; background:var(--deep-rose); }
 
    /* PILLARS INTRO */
    .pillars-section { max-width:1300px; margin:0 auto; padding:64px 48px; }
    .pillars-intro { text-align:center; max-width:680px; margin:0 auto 44px; }
    .pillars-intro span.eyebrow { font-size:12px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--rose); }
    .pillars-intro h2 { font-family:'Playfair Display', serif; font-size:34px; color:var(--text-dark); margin:10px 0 14px; }
    .pillars-intro p { font-size:15px; color:var(--text-mid); line-height:1.7; }
 
    .pillars-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:28px; }
    .pillar-card {
      border-radius:24px; padding:36px 32px; position:relative; overflow:hidden;
      text-decoration:none; color:inherit; transition:all .3s; display:block;
      box-shadow:0 8px 30px var(--shadow);
    }
    .pillar-card:hover { transform:translateY(-8px); box-shadow:0 24px 50px rgba(0,0,0,.12); }
    .pillar-card.card-gear      { background:linear-gradient(160deg,#FFF0F3,#FFE0E8); }
    .pillar-card.card-nutrition { background:linear-gradient(160deg,#EAF8F4,#D4F0E6); }
    .pillar-card.card-parenting { background:linear-gradient(160deg,#EEF0FB,#DDE2F6); }
    .pillar-card .big-icon { font-size:52px; margin-bottom:18px; display:block; }
    .pillar-card h3 { font-family:'Playfair Display', serif; font-size:24px; margin-bottom:10px; color:var(--text-dark); }
    .pillar-card p { font-size:14px; color:var(--text-mid); line-height:1.65; margin-bottom:22px; min-height:84px; }
    .pillar-card .cta {
      display:inline-flex; align-items:center; gap:8px;
      font-size:14px; font-weight:700; padding:11px 22px; border-radius:11px;
      transition:all .2s;
    }
    .card-gear .cta      { background:var(--deep-rose); color:#fff; }
    .card-nutrition .cta { background:var(--mint-dark); color:#fff; }
    .card-parenting .cta { background:var(--blue); color:#fff; }
    .pillar-card:hover .cta { transform:translateX(4px); }
    .pillar-card .decor { position:absolute; font-size:140px; opacity:.07; right:-20px; bottom:-30px; }
 
    /* TRENDING STRIP */
    .trending-section { background:var(--white); padding:60px 48px; }
    .section-head { text-align:center; margin-bottom:36px; }
    .section-head span.eyebrow { font-size:12px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:var(--rose); }
    .section-head h2 { font-family:'Playfair Display', serif; font-size:30px; margin:8px 0; }
    .products-grid { max-width:1300px; margin:0 auto; display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
    .product-card { background:var(--cream); border-radius:16px; overflow:hidden; transition:all .3s; position:relative; cursor:pointer; }
    .product-card:hover { transform:translateY(-4px); box-shadow:0 16px 36px var(--shadow); }
    .product-badge { position:absolute; top:12px; left:12px; background:var(--deep-rose); color:var(--white); font-size:10px; font-weight:700; padding:4px 10px; border-radius:6px; z-index:2; text-transform:uppercase; }
    .pillar-tag { position:absolute; top:12px; right:12px; font-size:10px; font-weight:700; padding:4px 9px; border-radius:6px; z-index:2; background:rgba(255,255,255,.85); }
    .product-img { width:100%; height:160px; background:linear-gradient(135deg,#FFF0F5,#FFE4EA); display:flex; align-items:center; justify-content:center; font-size:64px; overflow:hidden; }
    .product-img img { width:100%; height:100%; object-fit:cover; }
    .product-body { padding:14px 16px; }
    .product-name { font-size:14px; font-weight:600; margin-bottom:6px; height:38px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
    .product-price { font-size:18px; font-weight:700; color:var(--deep-rose); }
 
    /* FOOTER */
    footer { background:#1E0F0A; color:rgba(255,255,255,.8); margin-top:0; }
    .footer-main { display:grid; grid-template-columns:2fr 1fr 1fr 1.2fr; gap:48px; padding:56px 48px; max-width:1300px; margin:0 auto; }
    .footer-logo { font-family:'Playfair Display', serif; font-size:28px; color:var(--white); margin-bottom:14px; }
    .footer-about { font-size:14px; line-height:1.7; margin-bottom:20px; }
    .footer-col h4 { font-size:14px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--white); margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid rgba(255,255,255,.12); }
    .footer-col ul { list-style:none; display:flex; flex-direction:column; gap:10px; }
    .footer-col ul li a { font-size:14px; color:rgba(255,255,255,.65); text-decoration:none; transition:color .2s; }
    .footer-col ul li a:hover { color:var(--blush); }
    .footer-bottom { border-top:1px solid rgba(255,255,255,.08); padding:20px 48px; display:flex; align-items:center; justify-content:space-between; max-width:1300px; margin:0 auto; }
    .footer-bottom p { font-size:13px; color:rgba(255,255,255,.4); }
 
    @media (max-width:1100px) {
      .pillars-grid { grid-template-columns:1fr; }
      .products-grid { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width:768px) {
      .header-main { flex-wrap:wrap; height:auto; padding:12px 20px; }
      .search-bar { order:3; width:100%; margin:10px 0 0; max-width:none; }
      .pillar-nav { order:2; }
      .pillars-section, .trending-section { padding:40px 20px; }
      .footer-main { grid-template-columns:1fr 1fr; padding:40px 20px; }
    }
  </style>
</head>
<body>
 
<header>
  <div class="header-top">🌸 Free shipping on orders over Tsh 50,000 · Buyer Protection with Escrow · Verified Sellers Only 🍼</div>
  <div class="header-main">
    <a href="index.php" class="logo">
      <div class="logo-icon">🍼</div>
      <div><div class="logo-text">BabyBliss</div><div class="logo-sub">Gear · Nutrition · Parenting</div></div>
    </a>
 
    <form class="search-bar" action="baby_gear.php" method="GET">
      <i class="fas fa-search"></i>
      <input type="text" name="search" placeholder="Search the whole store..."/>
    </form>
 
    <!-- ═══ THE 3 MAIN PILLAR LINKS (top nav) ═══ -->
    <nav class="pillar-nav">
      <a href="index.php" class="pillar-link home">Home</a>
      <a href="baby_gear.php" class="pillar-link gear"><span>🧸</span> Baby Gear</a>
      <a href="nutrition.php" class="pillar-link nutrition"><span>🍎</span> Nutrition</a>
      <a href="parenting.php" class="pillar-link parenting"><span>👶</span> Parenting</a>
    </nav>
 
    <div class="header-actions">
      <a href="cart.php" class="icon-btn">
        <i class="fas fa-shopping-cart"></i>
        <?php if ($cart_count > 0): ?><span class="badge"><?= $cart_count ?></span><?php endif; ?>
      </a>
      <?php if (isset($_SESSION['user_name'])): ?>
        <?php if ($is_manufacturer): ?>
          <a href="manufacturer_dashboard.php" class="btn-sell"><i class="fas fa-store"></i> My Store</a>
        <?php elseif ($is_admin): ?>
          <a href="admin.php" class="btn-login"><i class="fas fa-crown"></i> Admin</a>
        <?php endif; ?>
        <a href="logout.php" class="btn-login">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn-login">Sign In</a>
        <a href="register.php?role=manufacturer" class="btn-sell"><i class="fas fa-store"></i> Sell</a>
      <?php endif; ?>
    </div>
  </div>
</header>
 
<!-- CAROUSEL -->
<div class="carousel-wrap">
  <div class="carousel" id="carousel">
    <div class="slide slide-1">
      <div class="slide-content">
        <div class="slide-tag">✨ Everything Under One Roof</div>
        <h1>One Store for Your Whole Parenting Journey</h1>
        <p>Baby gear, nutrition, and parenting guidance — no more shopping across five different stores.</p>
        <a href="baby_gear.php" class="slide-cta"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
      </div>
      <div class="slide-emoji"><img src="img/download (15).jpg " height="400px"></div>
    </div>
    <div class="slide slide-2">
      <div class="slide-content">
        <div class="slide-tag" style="background:var(--mint-dark)">🍎 Nutrition</div>
        <h1>Feed Your Baby with Confidence</h1>
        <p>Trusted formula, supplements, and feeding guides — backed by expert-reviewed articles.</p>
        <a href="nutrition.php" class="slide-cta" style="background:linear-gradient(135deg,var(--mint-dark),#3A9E88)"><i class="fas fa-apple-alt"></i> Explore Nutrition</a>
      </div>
      <div class="slide-emoji"><img src="uploads/products/prod_6a1d2598c74ed_📚 Toddler Learning Activities That Turn Play Into Progress.jpg" height="400px"></div>
    </div>
    <div class="slide slide-3">
      <div class="slide-content">
        <div class="slide-tag" style="background:var(--blue)">👶 Parenting</div>
        <h1>Guidance for Every Stage of Parenthood</h1>
        <p>From sleep schedules to safety monitors — practical help for first-time parents.</p>
        <a href="parenting.php" class="slide-cta" style="background:linear-gradient(135deg,var(--blue),var(--blue-dark))"><i class="fas fa-heart"></i> Explore Parenting</a>
      </div>
      <div class="slide-emoji">👶</div>
    </div>
  </div>
  <div class="carousel-dots" id="dots">
    <div class="dot active" onclick="goSlide(0)"></div>
    <div class="dot" onclick="goSlide(1)"></div>
    <div class="dot" onclick="goSlide(2)"></div>
  </div>
</div>
 
<!-- THE 3 PILLARS -->
<section class="pillars-section">
  <div class="pillars-intro">
    <span class="eyebrow">Why BabyBliss</span>
    <h2>Three Pillars, One Trusted Platform</h2>
    <p>New parents face information overload and scattered shopping across multiple stores. BabyBliss consolidates baby gear, nutritional products, and parenting guidance — all backed by verified sellers and secure escrow payments.</p>
  </div>
 
  <div class="pillars-grid">
    <a href="baby_gear.php" class="pillar-card card-gear">
      <span class="big-icon">🧸</span>
      <h3>Baby Gear</h3>
      <p>Toys, strollers, clothing, nursery furniture, and everyday essentials from verified manufacturers worldwide — at fair, transparent prices.</p>
      <span class="cta">Shop Baby Gear <i class="fas fa-arrow-right"></i></span>
      <div class="decor">🧸</div>
    </a>
 
    <a href="nutrition.php" class="pillar-card card-nutrition">
      <span class="big-icon">🍎</span>
      <h3>Nutrition</h3>
      <p>Formula, vitamins, baby food, and feeding accessories — paired with expert-reviewed articles on weaning, hydration, and healthy growth.</p>
      <span class="cta">Shop Nutrition <i class="fas fa-arrow-right"></i></span>
      <div class="decor">🍎</div>
    </a>
 
    <a href="parenting.php" class="pillar-card card-parenting">
      <span class="big-icon">👶</span>
      <h3>Parenting</h3>
      <p>Baby monitors, books, maternity care, and safety gear — alongside practical guidance on sleep, milestones, and managing toddler behavior.</p>
      <span class="cta">Explore Parenting <i class="fas fa-arrow-right"></i></span>
      <div class="decor">👶</div>
    </a>
  </div>
</section>
 
<!-- TRENDING ACROSS BABYBLISS -->
<section class="trending-section">
  <div class="section-head">
    <span class="eyebrow">Most Popular</span>
    <h2>Trending Across BabyBliss</h2>
  </div>
  <div class="products-grid">
    <?php if ($trending && mysqli_num_rows($trending) > 0): ?>
      <?php while ($p = mysqli_fetch_assoc($trending)): ?>
        <?php
          $pillar = $p['pillar'] ?? 'baby_gear';
          $pillar_page = $pillar === 'nutrition' ? 'nutrition.php' : ($pillar === 'parenting' ? 'parenting.php' : 'baby_gear.php');
          $pillar_color = $pillar === 'nutrition' ? '#5FB8A0' : ($pillar === 'parenting' ? '#6678CC' : '#C44D65');
          $pillar_label = $pillar === 'nutrition' ? ' Nutrition' : ($pillar === 'parenting' ? ' Parenting' : ' Baby Gear');
          $pillar_keywords = ['baby_gear' => 'babytoy', 'nutrition' => 'babyfood', 'parenting' => 'newborn'];
          $ph_keyword = $pillar_keywords[$pillar] ?? 'baby';
        ?>
        <a href="product.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;">
          <div class="product-card">
            <?php if (!empty($p['badge'])): ?><div class="product-badge"><?= htmlspecialchars($p['badge']) ?></div><?php endif; ?>
            <div class="pillar-tag" style="color:<?= $pillar_color ?>;"><?= $pillar_label ?></div>
            <div class="product-img">
              <?php if (!empty($p['image_url']) && file_exists('uploads/products/' . $p['image_url'])): ?>
                <img src="uploads/products/<?= htmlspecialchars($p['image_url']) ?>" alt=""/>
              <?php else: ?><img src="<?= placeholderPhoto($p['id'], $ph_keyword) ?>" alt="" loading="lazy"/><?php endif; ?>
            </div>
            <div class="product-body">
              <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
              <div class="product-price">Tsh<?= number_format($p['price'], 0) ?></div>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center;color:var(--text-light);grid-column:1/-1;">No products yet — be the first to add one!</p>
    <?php endif; ?>
  </div>
</section>
 
<footer>
  <div class="footer-main">
    <div>
      <div class="footer-logo">🍼 BabyBliss</div>
      <p class="footer-about">The trusted platform for baby gear, nutrition, and parenting guidance — all in one place, with verified sellers and secure escrow payments.</p>
    </div>
    <div class="footer-col">
      <h4>Shop</h4>
      <ul>
        <li><a href="baby_gear.php"> Baby Gear</a></li>
        <li><a href="nutrition.php"> Nutrition</a></li>
        <li><a href="parenting.php"> Parenting</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Buyers</h4>
      <ul>
        <li><a href="how_to_buy .php">How to Buy</a></li>
        <li><a href="buyer_protection.php">Buyer Protection</a></li>
        <li><a href="returns_refunds.php">Returns & Refunds</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Support</h4>
      <ul>
        <li><a href="help_center.php">Help Center</a></li>
        <li><a href="dispute.php">Dispute Resolution</a></li>
        <li><a href="contact.php">Contact Us</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2026 BabyBliss. All rights reserved. Secure escrow payments · Verified sellers · Buyer protection</p>
  </div>
</footer>
 
<script>
  let cur = 0; const total = 3;
  function goSlide(i) { cur = i; update(); }
  function update() {
    document.getElementById('carousel').style.transform = `translateX(-${cur*100}%)`;
    document.querySelectorAll('.dot').forEach((d,i) => d.classList.toggle('active', i===cur));
  }
  setInterval(() => { cur = (cur+1)%total; update(); }, 5000);
</script>
</body>
</html>