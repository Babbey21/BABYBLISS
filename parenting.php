<?php
session_start();
require_once "config.php";
 
$category_filter     = isset($_GET['category']) ? array_map('intval', (array)$_GET['category']) : [];
$manufacturer_filter = isset($_GET['manufacturer']) ? array_map('intval', (array)$_GET['manufacturer']) : [];
$rating_filter       = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
$price_min           = (isset($_GET['price_min']) && $_GET['price_min'] !== '') ? floatval($_GET['price_min']) : 0;
$price_max           = (isset($_GET['price_max']) && $_GET['price_max'] !== '') ? floatval($_GET['price_max']) : 999999;
$sort            = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'featured';
$search          = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$tab             = isset($_GET['tab']) && $_GET['tab'] === 'articles' ? 'articles' : 'products';
 
// Builds a link that keeps every current filter except the ones overridden (null = remove key)
function buildFilterUrl($page, $overrides = []) {
    $params = $_GET;
    foreach ($overrides as $k => $v) { if ($v === null) unset($params[$k]); else $params[$k] = $v; }
    $qs = http_build_query($params);
    return $page . ($qs ? '?' . $qs : '');
}
 
// ── Products (Parenting pillar only) ─────────────────────────
$where = "p.status='active' AND p.is_active=1 AND c.pillar='parenting'";
if ($category_filter)     $where .= " AND p.category_id IN (" . implode(',', $category_filter) . ")";
if ($manufacturer_filter) $where .= " AND p.manufacturer_id IN (" . implode(',', $manufacturer_filter) . ")";
if ($rating_filter)       $where .= " AND p.rating >= $rating_filter";
if ($price_min > 0)       $where .= " AND p.price >= $price_min";
if ($price_max < 999999)  $where .= " AND p.price <= $price_max";
if ($search)            $where .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
 
$order_by = "p.created_at DESC";
if ($sort == 'price_low')  $order_by = "p.price ASC";
if ($sort == 'price_high') $order_by = "p.price DESC";
if ($sort == 'popular')    $order_by = "p.orders_count DESC";
if ($sort == 'rating')     $order_by = "p.rating DESC";
 
$products_query = "SELECT p.*, m.company_name, c.name as category_name, c.icon as category_icon
                    FROM products p
                    LEFT JOIN manufacturers m ON p.manufacturer_id = m.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE $where ORDER BY $order_by";
$products = mysqli_query($conn, $products_query);
$total_products = $products ? mysqli_num_rows($products) : 0;
 
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE is_active=1 AND pillar='parenting' ORDER BY name ASC");
 
$manufacturers = mysqli_query($conn, "
  SELECT DISTINCT m.id, m.company_name
  FROM manufacturers m
  JOIN products p ON p.manufacturer_id = m.id
  JOIN categories c ON p.category_id = c.id AND c.pillar='parenting'
  WHERE m.verification_status='verified' AND m.is_active=1
  ORDER BY m.company_name ASC
");
 
$cat_counts = [];
$cc = mysqli_query($conn, "SELECT p.category_id, COUNT(*) cnt FROM products p JOIN categories c ON p.category_id=c.id WHERE c.pillar='parenting' AND p.status='active' AND p.is_active=1 GROUP BY p.category_id");
while ($cc && $r = mysqli_fetch_assoc($cc)) $cat_counts[$r['category_id']] = (int)$r['cnt'];
 
$brand_counts = [];
$bc = mysqli_query($conn, "SELECT p.manufacturer_id, COUNT(*) cnt FROM products p JOIN categories c ON p.category_id=c.id WHERE c.pillar='parenting' AND p.status='active' AND p.is_active=1 GROUP BY p.manufacturer_id");
while ($bc && $r = mysqli_fetch_assoc($bc)) $brand_counts[$r['manufacturer_id']] = (int)$r['cnt'];
 
$rating_counts = [];
foreach ([5,4,3,2,1] as $star) {
    $rc = mysqli_query($conn, "SELECT COUNT(*) cnt FROM products p JOIN categories c ON p.category_id=c.id WHERE c.pillar='parenting' AND p.status='active' AND p.is_active=1 AND p.rating >= $star");
    $rr = $rc ? mysqli_fetch_assoc($rc) : ['cnt'=>0];
    $rating_counts[$star] = (int)$rr['cnt'];
}
$has_active_filters = $category_filter || $manufacturer_filter || $rating_filter || $price_min > 0 || $price_max < 999999;
 
function placeholderPhoto($product_id, $keyword = 'newborn') {
    return "https://loremflickr.com/500/500/" . $keyword . "?lock=" . intval($product_id);
}
 
// ── Articles (Parenting guidance content) ─────────────────────
$articles = mysqli_query($conn, "SELECT * FROM articles WHERE pillar='parenting' AND is_published=1 ORDER BY created_at DESC");
$total_articles = $articles ? mysqli_num_rows($articles) : 0;
 
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$is_manufacturer = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manufacturer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Parenting – BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root { --cream:#FFF8F0; --rose:#E8738A; --deep-rose:#C44D65; --mint-dark:#5FB8A0; --blue:#6678CC; --blue-dark:#4455AA; --blue-deep:#2D3870; --gold:#F5C842; --white:#FFFFFF; --text-dark:#2D1B14; --text-mid:#6B4C3B; --text-light:#A07D6A; --shadow:rgba(102,120,204,0.15); --radius:20px; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DM Sans', sans-serif; background:var(--cream); color:var(--text-dark); }
 
    header { background:var(--white); position:sticky; top:0; z-index:999; box-shadow:0 2px 20px var(--shadow); }
    .header-top { background:var(--blue-dark); text-align:center; padding:6px; font-size:13px; color:var(--white); }
    .header-main { display:flex; align-items:center; justify-content:space-between; padding:0 48px; height:72px; }
    .logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .logo-icon { width:44px; height:44px; background:linear-gradient(135deg,var(--blue),var(--blue-dark)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; }
    .logo-text { font-family:'Playfair Display', serif; font-size:26px; font-weight:700; color:var(--blue-dark); line-height:1; }
    .search-bar { flex:1; max-width:420px; margin:0 24px; position:relative; }
    .search-bar input { width:100%; padding:12px 20px 12px 44px; border:2px solid #E6E8F8; border-radius:12px; font-size:14px; outline:none; }
    .search-bar input:focus { border-color:var(--blue); box-shadow:0 0 0 4px rgba(102,120,204,.12); }
    .search-bar i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--text-light); }
    .pillar-nav { display:flex; gap:8px; }
    .pillar-link { display:flex; align-items:center; gap:7px; padding:9px 16px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; transition:all .2s; }
    .pillar-link.home { color:var(--text-mid); }
    .pillar-link.home:hover { background:var(--cream); }
    .pillar-link.gear { background:#FFF0F3; color:var(--deep-rose); }
    .pillar-link.gear:hover { background:var(--rose); color:#fff; }
    .pillar-link.nutrition { background:#EAF8F4; color:var(--mint-dark); }
    .pillar-link.nutrition:hover { background:var(--mint-dark); color:#fff; }
    .pillar-link.parenting { background:var(--blue); color:#fff; }
    .header-actions { display:flex; align-items:center; gap:10px; }
    .icon-btn { position:relative; background:none; border:none; font-size:20px; color:var(--text-mid); cursor:pointer; padding:8px; border-radius:10px; text-decoration:none; }
    .icon-btn:hover { background:var(--cream); color:var(--blue); }
    .badge { position:absolute; top:2px; right:2px; background:var(--blue); color:var(--white); font-size:10px; font-weight:600; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .btn-login { padding:9px 20px; border-radius:10px; font-size:14px; font-weight:600; border:2px solid var(--blue); color:var(--blue); background:transparent; text-decoration:none; }
    .btn-login:hover { background:var(--blue); color:var(--white); }
    .btn-sell { padding:9px 20px; border-radius:10px; font-size:14px; font-weight:600; border:none; background:linear-gradient(135deg,var(--mint-dark),#3A9E88); color:var(--white); text-decoration:none; }
 
    .pillar-hero { background:linear-gradient(135deg,#1A1F45 0%,#2D3870 50%,#4455AA 100%); padding:50px 48px; display:flex; align-items:center; justify-content:space-between; position:relative; overflow:hidden; }
    .hero-content { position:relative; z-index:1; max-width:600px; }
    .hero-tag { display:inline-block; background:var(--blue); color:white; padding:4px 14px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:2px; text-transform:uppercase; margin-bottom:16px; }
    .hero-content h1 { font-family:'Playfair Display', serif; font-size:38px; color:var(--white); margin-bottom:12px; line-height:1.2; }
    .hero-content p { color:rgba(255,255,255,.8); font-size:16px; line-height:1.6; }
    .hero-emoji { font-size:120px; opacity:.15; position:absolute; right:80px; top:50%; transform:translateY(-50%); }
 
    .tabs-wrap { background:var(--white); border-bottom:1px solid #E6E8F8; padding:0 48px; }
    .tabs { max-width:1400px; margin:0 auto; display:flex; gap:8px; }
    .tab { padding:18px 24px; font-size:15px; font-weight:600; color:var(--text-light); text-decoration:none; border-bottom:3px solid transparent; transition:all .2s; }
    .tab:hover { color:var(--blue); }
    .tab.active { color:var(--blue); border-bottom-color:var(--blue); }
 
    .main-container { max-width:1400px; margin:0 auto; padding:40px 48px; display:grid; grid-template-columns:260px 1fr; gap:32px; }
 
    .pillar-switcher { background:var(--white); border-radius:var(--radius); padding:14px; box-shadow:0 4px 20px var(--shadow); margin-bottom:20px; }
    .switcher-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-light); padding:4px 8px 10px; }
    .switch-item { display:flex; align-items:center; gap:10px; padding:11px 12px; border-radius:10px; text-decoration:none; font-size:14px; font-weight:600; margin-bottom:4px; transition:all .2s; }
    .switch-item.gear { color:var(--text-mid); } .switch-item.gear:hover { background:#FFF0F3; color:var(--deep-rose); }
    .switch-item.nutrition { color:var(--text-mid); } .switch-item.nutrition:hover { background:#EAF8F4; color:var(--mint-dark); }
    .switch-item.parenting { background:linear-gradient(135deg,#EEF0FB,#DDE2F6); color:var(--blue-dark); }
 
    .sidebar-filters { background:var(--white); border-radius:var(--radius); padding:0; box-shadow:0 4px 20px var(--shadow); height:fit-content; position:sticky; top:160px; overflow:hidden; }
    .filter-panel-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; background:#F5F5F5; font-size:13px; font-weight:700; letter-spacing:.5px; color:var(--text-dark); }
    .filter-panel-header a { font-size:12px; font-weight:600; color:var(--blue-dark); text-decoration:none; }
    .filter-panel-header a:hover { text-decoration:underline; }
    .filter-group { padding:16px 20px; border-bottom:1px solid #EFEFEF; }
    .filter-group:last-child { border-bottom:none; }
    .filter-group-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
    .filter-group-header span.label { font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:6px; }
    .filter-group-header span.label .tick { color:var(--blue-dark); }
    .filter-group-header a.clear-link { font-size:11px; color:var(--text-light); text-decoration:underline; }
    .filter-group-header a.clear-link:hover { color:var(--blue-dark); }
    .filter-checkbox-list { display:flex; flex-direction:column; gap:2px; max-height:260px; overflow-y:auto; }
    .filter-checkbox-list.short { max-height:none; }
    .filter-checkbox-item { display:flex; align-items:center; gap:9px; padding:6px 2px; font-size:13.5px; color:var(--text-mid); cursor:pointer; }
    .filter-checkbox-item:hover { color:var(--blue-dark); }
    .filter-checkbox-item input[type=checkbox], .filter-checkbox-item input[type=radio] { width:16px; height:16px; accent-color:var(--blue); cursor:pointer; flex-shrink:0; }
    .filter-checkbox-item .fc-count { color:var(--text-light); font-size:12px; margin-left:auto; padding-left:6px; }
    .filter-checkbox-item .fc-stars { color:var(--gold); letter-spacing:1px; }
    .filter-checkbox-item .fc-more { color:var(--text-mid); font-size:12.5px; }
    .price-range { display:flex; gap:8px; }
    .price-range input { width:100%; padding:8px 12px; border:2px solid #E6E8F8; border-radius:8px; font-size:13px; }
    .price-range button { padding:8px 16px; background:var(--blue); color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
    .filter-item { display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:8px; cursor:pointer; transition:all .2s; font-size:14px; color:var(--text-mid); text-decoration:none; }
    .filter-item:hover { background:var(--cream); color:var(--blue); }
    .filter-item.active { background:linear-gradient(135deg,#EEF0FB,#DDE2F6); color:var(--blue-dark); font-weight:600; }
 
    .results-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:16px; }
    .results-count { font-size:14px; color:var(--text-light); }
    .results-count strong { color:var(--text-dark); }
    .sort-bar select { padding:8px 16px; border:2px solid #E6E8F8; border-radius:10px; font-size:14px; background:var(--white); cursor:pointer; outline:none; }
 
    .products-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
    .product-card { background:var(--white); border-radius:16px; overflow:hidden; transition:all .3s; position:relative; cursor:pointer; border:1px solid transparent; }
    .product-card:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(102,120,204,.18); border-color:#C8CCF0; }
    .product-badge { position:absolute; top:12px; left:12px; background:var(--blue); color:var(--white); font-size:10px; font-weight:700; padding:4px 10px; border-radius:6px; z-index:2; text-transform:uppercase; }
    .product-img { width:100%; height:200px; background:linear-gradient(135deg,#EEF0FB,#DDE2F6); display:flex; align-items:center; justify-content:center; font-size:80px; overflow:hidden; }
    .product-img img { width:100%; height:100%; object-fit:cover; }
    .product-body { padding:16px; }
    .product-cat { font-size:11px; font-weight:700; color:var(--blue); text-transform:uppercase; letter-spacing:1px; }
    .product-name { font-size:15px; font-weight:600; margin:6px 0; height:42px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
    .product-price { display:flex; align-items:center; gap:8px; margin:8px 0 10px; }
    .price-current { font-size:20px; font-weight:700; color:var(--blue-dark); }
    .price-old { font-size:13px; color:var(--text-light); text-decoration:line-through; }
    .btn-cart { width:100%; padding:10px; background:linear-gradient(135deg,var(--blue),var(--blue-dark)); color:var(--white); border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; margin:0 16px 16px; }
 
    .articles-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:20px; }
    .article-card { background:var(--white); border-radius:18px; padding:26px; text-decoration:none; color:inherit; transition:all .3s; box-shadow:0 4px 16px var(--shadow); display:flex; gap:18px; }
    .article-card:hover { transform:translateY(-4px); box-shadow:0 14px 32px rgba(102,120,204,.22); }
    .article-icon { width:60px; height:60px; border-radius:14px; background:linear-gradient(135deg,#EEF0FB,#DDE2F6); display:flex; align-items:center; justify-content:center; font-size:30px; flex-shrink:0; }
    .article-topic { font-size:11px; font-weight:700; color:var(--blue-dark); text-transform:uppercase; letter-spacing:1px; margin-bottom:6px; }
    .article-title { font-family:'Playfair Display', serif; font-size:18px; margin-bottom:8px; line-height:1.3; }
    .article-excerpt { font-size:13px; color:var(--text-mid); line-height:1.5; margin-bottom:10px; }
    .article-meta { font-size:12px; color:var(--text-light); display:flex; align-items:center; gap:14px; }
 
    .no-items { text-align:center; padding:80px 20px; grid-column:1/-1; }
    .no-items-icon { font-size:80px; margin-bottom:20px; opacity:.5; }
 
    footer { background:#1A1F45; color:rgba(255,255,255,.8); margin-top:40px; }
    .footer-main { display:grid; grid-template-columns:2fr 1fr 1fr 1.2fr; gap:48px; padding:56px 48px; }
    .footer-logo { font-family:'Playfair Display', serif; font-size:28px; color:var(--white); margin-bottom:14px; }
    .footer-col h4 { font-size:14px; font-weight:700; text-transform:uppercase; color:var(--white); margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid rgba(255,255,255,.12); }
    .footer-col ul { list-style:none; display:flex; flex-direction:column; gap:10px; }
    .footer-col ul li a { font-size:14px; color:rgba(255,255,255,.65); text-decoration:none; }
    .footer-col ul li a:hover { color:#A8B0E8; }
    .footer-bottom { border-top:1px solid rgba(255,255,255,.08); padding:20px 48px; }
    .footer-bottom p { font-size:13px; color:rgba(255,255,255,.4); }
 
    @media (max-width:1100px) { .products-grid, .articles-grid { grid-template-columns:repeat(2,1fr); } .main-container { grid-template-columns:1fr; } .sidebar-filters { position:static; } }
    @media (max-width:768px) { .products-grid, .articles-grid { grid-template-columns:1fr; } .header-main { flex-wrap:wrap; height:auto; padding:12px 20px; } .search-bar { order:3; width:100%; margin:10px 0 0; max-width:none; } .pillar-hero { padding:30px 20px; } .hero-emoji { display:none; } .main-container { padding:20px; } .tabs-wrap { padding:0 20px; } .article-card { flex-direction:column; } }
  </style>
</head>
<body>
 
<header>
  <div class="header-top">👶 Guidance for Every Stage of Parenthood · Practical · Reassuring</div>
  <div class="header-main">
    <a href="index.php" class="logo">
      <div class="logo-icon">👶</div>
      <div class="logo-text">BabyBliss</div>
    </a>
    <form class="search-bar" action="parenting.php" method="GET">
      <i class="fas fa-search"></i>
      <input type="text" name="search" placeholder="Search parenting products..." value="<?= htmlspecialchars($search) ?>"/>
    </form>
    <nav class="pillar-nav">
      <a href="index.php" class="pillar-link home">Home</a>
      <a href="baby_gear.php" class="pillar-link gear">🧸 Baby Gear</a>
      <a href="nutrition.php" class="pillar-link nutrition">🍎 Nutrition</a>
      <a href="parenting.php" class="pillar-link parenting">👶 Parenting</a>
    </nav>
    <div class="header-actions">
      <a href="cart.php" class="icon-btn"><i class="fas fa-shopping-cart"></i><?php if ($cart_count > 0): ?><span class="badge"><?= $cart_count ?></span><?php endif; ?></a>
      <?php if (isset($_SESSION['user_name'])): ?>
        <?php if ($is_manufacturer): ?><a href="manufacturer_dashboard.php" class="btn-sell"><i class="fas fa-store"></i> My Store</a>
        <?php elseif ($is_admin): ?><a href="admin.php" class="btn-login"><i class="fas fa-crown"></i> Admin</a><?php endif; ?>
        <a href="logout.php" class="btn-login">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn-login">Sign In</a>
        <a href="register.php?role=manufacturer" class="btn-sell"><i class="fas fa-store"></i> Sell</a>
      <?php endif; ?>
    </div>
  </div>
</header>
 
<div class="pillar-hero">
  <div class="hero-content">
    <div class="hero-tag">👶 Parenting Pillar</div>
    <h1>Guidance for Every Stage of Parenthood</h1>
    <p>Baby monitors, books, and safety gear from trusted sellers — alongside practical guidance on sleep, milestones, and managing toddler behavior.</p>
  </div>
  <div class="hero-emoji">👶</div>
</div>
 
<div class="tabs-wrap">
  <div class="tabs">
    <a href="?tab=products" class="tab <?= $tab=='products'?'active':'' ?>"><i class="fas fa-shopping-basket"></i> Shop Products</a>
    <a href="?tab=articles" class="tab <?= $tab=='articles'?'active':'' ?>"><i class="fas fa-book-open"></i> Articles & Guides</a>
  </div>
</div>
 
<div class="main-container">
  <aside>
    <div class="pillar-switcher">
      <div class="switcher-title">Browse by Pillar</div>
      <a href="baby_gear.php" class="switch-item gear">🧸 Baby Gear</a>
      <a href="nutrition.php" class="switch-item nutrition">🍎 Nutrition</a>
      <a href="parenting.php" class="switch-item parenting">👶 Parenting</a>
    </div>
 
    <?php if ($tab === 'products'): ?>
    <div class="sidebar-filters">
      <div class="filter-panel-header">
        <span>FILTER BY</span>
        <?php if ($has_active_filters): ?><a href="parenting.php">Clear All</a><?php endif; ?>
      </div>
 
      <form method="GET" action="parenting.php" id="filterForm">
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"/>
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>"/>
        <input type="hidden" name="tab" value="products"/>
 
        <div class="filter-group">
          <div class="filter-group-header">
            <span class="label"><?php if ($category_filter): ?><span class="tick">✓</span><?php endif; ?> CATEGORIES</span>
            <?php if ($category_filter): ?><a class="clear-link" href="<?= buildFilterUrl('parenting.php', ['category' => null]) ?>">Clear</a><?php endif; ?>
          </div>
          <div class="filter-checkbox-list">
            <?php mysqli_data_seek($categories, 0); while ($cat = mysqli_fetch_assoc($categories)):
              $checked = in_array($cat['id'], $category_filter);
            ?>
            <label class="filter-checkbox-item">
              <input type="checkbox" name="category[]" value="<?= $cat['id'] ?>" <?= $checked ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"/>
              <span><?= htmlspecialchars($cat['icon'] ?? '📦') ?> <?= htmlspecialchars($cat['name']) ?></span>
              <span class="fc-count">(<?= $cat_counts[$cat['id']] ?? 0 ?>)</span>
            </label>
            <?php endwhile; ?>
          </div>
        </div>
 
        <div class="filter-group">
          <div class="filter-group-header"><span class="label">PRICE (Tsh)</span></div>
          <div class="price-range">
            <input type="number" name="price_min" placeholder="Min" value="<?= $price_min > 0 ? $price_min : '' ?>"/>
            <input type="number" name="price_max" placeholder="Max" value="<?= $price_max < 999999 ? $price_max : '' ?>"/>
            <button type="submit">Go</button>
          </div>
        </div>
 
        <div class="filter-group">
          <div class="filter-group-header">
            <span class="label"><?php if ($manufacturer_filter): ?><span class="tick">✓</span><?php endif; ?> BRAND</span>
            <?php if ($manufacturer_filter): ?><a class="clear-link" href="<?= buildFilterUrl('parenting.php', ['manufacturer' => null]) ?>">Clear</a><?php endif; ?>
          </div>
          <div class="filter-checkbox-list">
            <?php if ($manufacturers && mysqli_num_rows($manufacturers) > 0): mysqli_data_seek($manufacturers, 0); while ($m = mysqli_fetch_assoc($manufacturers)):
              $checked = in_array($m['id'], $manufacturer_filter);
            ?>
            <label class="filter-checkbox-item">
              <input type="checkbox" name="manufacturer[]" value="<?= $m['id'] ?>" <?= $checked ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"/>
              <span><?= htmlspecialchars($m['company_name']) ?></span>
              <span class="fc-count">(<?= $brand_counts[$m['id']] ?? 0 ?>)</span>
            </label>
            <?php endwhile; else: ?><p style="font-size:13px;color:var(--text-light);">No sellers yet</p><?php endif; ?>
          </div>
        </div>
 
        <div class="filter-group">
          <div class="filter-group-header">
            <span class="label"><?php if ($rating_filter): ?><span class="tick">✓</span><?php endif; ?> CUSTOMER RATINGS</span>
            <?php if ($rating_filter): ?><a class="clear-link" href="<?= buildFilterUrl('parenting.php', ['rating' => null]) ?>">Clear</a><?php endif; ?>
          </div>
          <div class="filter-checkbox-list short">
            <?php foreach ([5,4,3,2,1] as $star): ?>
            <label class="filter-checkbox-item">
              <input type="radio" name="rating" value="<?= $star ?>" <?= $rating_filter == $star ? 'checked' : '' ?> onchange="document.getElementById('filterForm').submit()"/>
              <span class="fc-stars"><?= str_repeat('★', $star) . str_repeat('☆', 5 - $star) ?></span>
              <?php if ($star < 5): ?><span class="fc-more">& More</span><?php endif; ?>
              <span class="fc-count">(<?= $rating_counts[$star] ?>)</span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </form>
    </div>
    <?php else: ?>
    <div class="sidebar-filters">
      <div class="filter-panel-header"><span>TOPICS</span></div>
      <div class="filter-group">
        <div class="filter-checkbox-list short" style="gap:8px;">
          <span class="filter-item active"><i class="fas fa-bed"></i> Sleep</span>
          <span class="filter-item"><i class="fas fa-child"></i> Behavior</span>
          <span class="filter-item"><i class="fas fa-shield-alt"></i> Safety</span>
          <span class="filter-item"><i class="fas fa-seedling"></i> Development</span>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </aside>
 
  <div class="products-area">
    <?php if ($tab === 'products'): ?>
      <div class="results-header">
        <div class="results-count">Showing <strong><?= $total_products ?></strong> parenting products</div>
        <div class="sort-bar">
          <select onchange="window.location.href=this.value">
            <option value="?tab=products&sort=featured" <?= $sort=='featured'?'selected':'' ?>>Featured</option>
            <option value="?tab=products&sort=price_low" <?= $sort=='price_low'?'selected':'' ?>>Price: Low to High</option>
            <option value="?tab=products&sort=price_high" <?= $sort=='price_high'?'selected':'' ?>>Price: High to Low</option>
            <option value="?tab=products&sort=popular" <?= $sort=='popular'?'selected':'' ?>>Most Popular</option>
          </select>
        </div>
      </div>
 
      <?php if ($total_products === 0): ?>
        <div class="no-items"><div class="no-items-icon">👶</div><h3 style="font-family:'Playfair Display',serif;">No products yet</h3><p style="color:var(--text-light);">Parenting products will appear here once sellers add them.</p></div>
      <?php else: ?>
        <div class="products-grid">
          <?php mysqli_data_seek($products, 0); while ($p = mysqli_fetch_assoc($products)):
            $img_path = !empty($p['image_url']) ? 'uploads/products/' . $p['image_url'] : '';
            $cart_img = !empty($img_path) && file_exists($img_path) ? $img_path : placeholderPhoto($p['id']);
          ?>
          <div class="product-card">
            <?php if (!empty($p['badge'])): ?><div class="product-badge"><?= htmlspecialchars($p['badge']) ?></div><?php endif; ?>
            <a href="product.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;display:block;">
              <div class="product-img">
                <?php if (!empty($p['image_url']) && file_exists('uploads/products/' . $p['image_url'])): ?>
                  <img src="uploads/products/<?= htmlspecialchars($p['image_url']) ?>" alt=""/>
                <?php else: ?><img src="<?= placeholderPhoto($p['id']) ?>" alt="" loading="lazy"/><?php endif; ?>
              </div>
              <div class="product-body">
                <div class="product-cat"><?= htmlspecialchars($p['category_name'] ?? 'Parenting') ?></div>
                <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
                <div class="product-price">
                  <span class="price-current">Tsh<?= number_format($p['price'],0) ?></span>
                  <?php if (!empty($p['old_price']) && $p['old_price']>0): ?><span class="price-old">Tsh<?= number_format($p['old_price'],0) ?></span><?php endif; ?>
                </div>
              </div>
            </a>
            <a href="cart.php?add=<?= $p['id'] ?>&name=<?= urlencode($p['name']) ?>&price=<?= $p['price'] ?>&image=<?= urlencode($cart_img) ?>" class="btn-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</a>
          </div>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
 
    <?php else: ?>
      <div class="results-header">
        <div class="results-count">Showing <strong><?= $total_articles ?></strong> parenting guides</div>
      </div>
 
      <?php if ($total_articles === 0): ?>
        <div class="no-items"><div class="no-items-icon">📖</div><p style="color:var(--text-light);">No articles published yet.</p></div>
      <?php else: ?>
        <div class="articles-grid">
          <?php mysqli_data_seek($articles, 0); while ($a = mysqli_fetch_assoc($articles)): ?>
          <a href="article.php?id=<?= $a['id'] ?>" class="article-card">
            <div class="article-icon"><?= htmlspecialchars($a['icon'] ?: '📖') ?></div>
            <div>
              <div class="article-topic"><?= htmlspecialchars($a['topic'] ?: 'Parenting') ?></div>
              <div class="article-title"><?= htmlspecialchars($a['title']) ?></div>
              <div class="article-excerpt"><?= htmlspecialchars($a['excerpt']) ?></div>
              <div class="article-meta"><i class="fas fa-clock"></i> <?= (int)$a['read_time'] ?> min read &nbsp;·&nbsp; <?= htmlspecialchars($a['author']) ?></div>
            </div>
          </a>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
 
<footer>
  <div class="footer-main">
    <div><div class="footer-logo">🍼 BabyBliss</div></div>
    <div class="footer-col"><h4>Shop</h4><ul><li><a href="baby_gear.php">Baby Gear</a></li><li><a href="nutrition.php">Nutrition</a></li><li><a href="parenting.php">Parenting</a></li></ul></div>
    <div class="footer-col"><h4>Buyers</h4><ul><li><a href="how_to_buy_.php">How to Buy</a></li><li><a href="buyer_protection.php">Buyer Protection</a></li></ul></div>
    <div class="footer-col"><h4>Support</h4><ul><li><a href="help_center.php">Help Center</a></li><li><a href="contact.php">Contact</a></li></ul></div>
  </div>
  <div class="footer-bottom"><p>© 2026 BabyBliss. All rights reserved.</p></div>
</footer>
</body>
</html>