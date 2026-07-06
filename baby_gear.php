HP
<?php
session_start();
require_once "config.php";
 
// ── Filters ──────────────────────────────────────────────────
$category_filter     = isset($_GET['category']) ? array_map('intval', (array)$_GET['category']) : [];
$manufacturer_filter = isset($_GET['manufacturer']) ? array_map('intval', (array)$_GET['manufacturer']) : [];
$rating_filter       = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
$price_min           = (isset($_GET['price_min']) && $_GET['price_min'] !== '') ? floatval($_GET['price_min']) : 0;
$price_max           = (isset($_GET['price_max']) && $_GET['price_max'] !== '') ? floatval($_GET['price_max']) : 999999;
$condition_filter    = isset($_GET['condition']) ? mysqli_real_escape_string($conn, $_GET['condition']) : '';
$sort                = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'featured';
$search               = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
 
// Builds a link that keeps every current filter except the ones overridden (null = remove key)
function buildFilterUrl($page, $overrides = []) {
    $params = $_GET;
    foreach ($overrides as $k => $v) { if ($v === null) unset($params[$k]); else $params[$k] = $v; }
    $qs = http_build_query($params);
    return $page . ($qs ? '?' . $qs : '');
}
 
// This page is locked to the Baby Gear pillar
$where = "p.status='active' AND p.is_active=1 AND c.pillar='baby_gear'";
if ($category_filter)      $where .= " AND p.category_id IN (" . implode(',', $category_filter) . ")";
if ($price_min > 0)        $where .= " AND p.price >= $price_min";
if ($price_max < 999999)   $where .= " AND p.price <= $price_max";
if ($condition_filter)     $where .= " AND p.condition_type = '$condition_filter'";
if ($manufacturer_filter)  $where .= " AND p.manufacturer_id IN (" . implode(',', $manufacturer_filter) . ")";
if ($rating_filter)        $where .= " AND p.rating >= $rating_filter";
if ($search)                $where .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
 
$order_by = "p.created_at DESC";
if ($sort == 'price_low')  $order_by = "p.price ASC";
if ($sort == 'price_high') $order_by = "p.price DESC";
if ($sort == 'popular')    $order_by = "p.orders_count DESC";
if ($sort == 'rating')     $order_by = "p.rating DESC";
 
$products_query = "SELECT p.*, m.company_name, m.country as manufacturer_country, m.logo as manufacturer_logo,
                           c.name as category_name, c.icon as category_icon,
                           EXISTS(SELECT 1 FROM product_variant_groups vg WHERE vg.product_id = p.id) as has_variants
                    FROM products p
                    LEFT JOIN manufacturers m ON p.manufacturer_id = m.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE $where
                    ORDER BY $order_by";
$products = mysqli_query($conn, $products_query);
$total_products = $products ? mysqli_num_rows($products) : 0;
 
// Categories that belong only to the Baby Gear pillar
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE is_active=1 AND pillar='baby_gear' ORDER BY name ASC");
 
// Manufacturers who sell at least one Baby Gear product
$manufacturers = mysqli_query($conn, "
  SELECT DISTINCT m.id, m.company_name, m.country, m.logo, m.total_sales
  FROM manufacturers m
  JOIN products p ON p.manufacturer_id = m.id
  JOIN categories c ON p.category_id = c.id AND c.pillar='baby_gear'
  WHERE m.verification_status='verified' AND m.is_active=1
  ORDER BY m.company_name ASC
");
 
// ── Counts for the FirstCry-style checkbox filters (independent of the
//    filters currently applied, so the sidebar always shows the full picture) ──
$cat_counts = [];
$cc = mysqli_query($conn, "SELECT p.category_id, COUNT(*) cnt FROM products p JOIN categories c ON p.category_id=c.id WHERE c.pillar='baby_gear' AND p.status='active' AND p.is_active=1 GROUP BY p.category_id");
while ($cc && $r = mysqli_fetch_assoc($cc)) $cat_counts[$r['category_id']] = (int)$r['cnt'];
 
$brand_counts = [];
$bc = mysqli_query($conn, "SELECT p.manufacturer_id, COUNT(*) cnt FROM products p JOIN categories c ON p.category_id=c.id WHERE c.pillar='baby_gear' AND p.status='active' AND p.is_active=1 GROUP BY p.manufacturer_id");
while ($bc && $r = mysqli_fetch_assoc($bc)) $brand_counts[$r['manufacturer_id']] = (int)$r['cnt'];
 
$rating_counts = [];
foreach ([5,4,3,2,1] as $star) {
    $rc = mysqli_query($conn, "SELECT COUNT(*) cnt FROM products p JOIN categories c ON p.category_id=c.id WHERE c.pillar='baby_gear' AND p.status='active' AND p.is_active=1 AND p.rating >= $star");
    $rr = $rc ? mysqli_fetch_assoc($rc) : ['cnt'=>0];
    $rating_counts[$star] = (int)$rr['cnt'];
}
 
$has_active_filters = $category_filter || $manufacturer_filter || $rating_filter || $price_min > 0 || $price_max < 999999;
 
// Real, royalty-free stock photo fallback (LoremFlickr) used only when a manufacturer
// has not yet uploaded an actual product photo. "lock" keeps the same photo for the
// same product on every page load instead of changing randomly.
function placeholderPhoto($product_id, $keyword = 'baby,toy') {
    return "https://loremflickr.com/500/500/" . $keyword . "?lock=" . intval($product_id);
}
 
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$is_manufacturer = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'manufacturer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Baby Gear – BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root { --cream:#FFF8F0; --blush:#F2A7B3; --rose:#E8738A; --deep-rose:#C44D65; --mint:#A8D8C8; --mint-dark:#5FB8A0; --blue:#6678CC; --gold:#F5C842; --white:#FFFFFF; --text-dark:#2D1B14; --text-mid:#6B4C3B; --text-light:#A07D6A; --shadow:rgba(196,77,101,0.12); --radius:20px; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DM Sans', sans-serif; background:var(--cream); color:var(--text-dark); }
 
    header { background:var(--white); position:sticky; top:0; z-index:999; box-shadow:0 2px 20px var(--shadow); }
    .header-top { background:var(--deep-rose); text-align:center; padding:6px; font-size:13px; color:var(--white); }
    .header-main { display:flex; align-items:center; justify-content:space-between; padding:0 48px; height:72px; }
    .logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .logo-icon { width:44px; height:44px; background:linear-gradient(135deg,var(--blush),var(--deep-rose)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; }
    .logo-text { font-family:'Playfair Display', serif; font-size:26px; font-weight:700; color:var(--deep-rose); line-height:1; }
    .search-bar { flex:1; max-width:420px; margin:0 24px; position:relative; }
    .search-bar input { width:100%; padding:12px 20px 12px 44px; border:2px solid #F0E4DC; border-radius:12px; font-size:14px; outline:none; }
    .search-bar input:focus { border-color:var(--rose); box-shadow:0 0 0 4px rgba(232,115,138,.1); }
    .search-bar i { position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--text-light); }
    .pillar-nav { display:flex; gap:8px; }
    .pillar-link { display:flex; align-items:center; gap:7px; padding:9px 16px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; transition:all .2s; }
    .pillar-link.home { color:var(--text-mid); }
    .pillar-link.home:hover { background:var(--cream); }
    .pillar-link.gear { background:var(--deep-rose); color:#fff; }
    .pillar-link.nutrition { background:#EAF8F4; color:var(--mint-dark); }
    .pillar-link.nutrition:hover { background:var(--mint-dark); color:#fff; }
    .pillar-link.parenting { background:#EEF0FB; color:var(--blue); }
    .pillar-link.parenting:hover { background:var(--blue); color:#fff; }
    .header-actions { display:flex; align-items:center; gap:10px; }
    .icon-btn { position:relative; background:none; border:none; font-size:20px; color:var(--text-mid); cursor:pointer; padding:8px; border-radius:10px; text-decoration:none; }
    .icon-btn:hover { background:var(--cream); color:var(--deep-rose); }
    .badge { position:absolute; top:2px; right:2px; background:var(--deep-rose); color:var(--white); font-size:10px; font-weight:600; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
    .btn-login { padding:9px 20px; border-radius:10px; font-size:14px; font-weight:600; border:2px solid var(--rose); color:var(--rose); background:transparent; text-decoration:none; }
    .btn-login:hover { background:var(--rose); color:var(--white); }
    .btn-sell { padding:9px 20px; border-radius:10px; font-size:14px; font-weight:600; border:none; background:linear-gradient(135deg,var(--mint-dark),#3A9E88); color:var(--white); text-decoration:none; }
 
    .pillar-hero { background:linear-gradient(135deg,#1a0a0e 0%,#3a1a24 50%,#5a2a34 100%); padding:50px 48px; display:flex; align-items:center; justify-content:space-between; position:relative; overflow:hidden; }
    .hero-content { position:relative; z-index:1; max-width:600px; }
    .hero-tag { display:inline-block; background:var(--rose); color:white; padding:4px 14px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:2px; text-transform:uppercase; margin-bottom:16px; }
    .hero-content h1 { font-family:'Playfair Display', serif; font-size:38px; color:var(--white); margin-bottom:12px; line-height:1.2; }
    .hero-content p { color:rgba(255,255,255,.8); font-size:16px; line-height:1.6; }
    .hero-emoji { font-size:120px; opacity:.15; position:absolute; right:80px; top:50%; transform:translateY(-50%); }
 
    .main-container { max-width:1400px; margin:0 auto; padding:40px 48px; display:grid; grid-template-columns:260px 1fr; gap:32px; }
 
    /* PILLAR SWITCHER — sidebar block requested by the user */
    .pillar-switcher { background:var(--white); border-radius:var(--radius); padding:14px; box-shadow:0 4px 20px var(--shadow); margin-bottom:20px; }
    .switcher-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-light); padding:4px 8px 10px; }
    .switch-item { display:flex; align-items:center; gap:10px; padding:11px 12px; border-radius:10px; text-decoration:none; font-size:14px; font-weight:600; margin-bottom:4px; transition:all .2s; }
    .switch-item.gear { background:linear-gradient(135deg,#FFF0F3,#FFE0E8); color:var(--deep-rose); }
    .switch-item.nutrition { color:var(--text-mid); }
    .switch-item.nutrition:hover { background:#EAF8F4; color:var(--mint-dark); }
    .switch-item.parenting { color:var(--text-mid); }
    .switch-item.parenting:hover { background:#EEF0FB; color:var(--blue); }
 
    .sidebar-filters { background:var(--white); border-radius:var(--radius); padding:0; box-shadow:0 4px 20px var(--shadow); height:fit-content; position:sticky; top:100px; overflow:hidden; }
    .filter-panel-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; background:#F5F5F5; font-size:13px; font-weight:700; letter-spacing:.5px; color:var(--text-dark); }
    .filter-panel-header a { font-size:12px; font-weight:600; color:var(--rose); text-decoration:none; }
    .filter-panel-header a:hover { text-decoration:underline; }
    .filter-group { padding:16px 20px; border-bottom:1px solid #EFEFEF; }
    .filter-group:last-child { border-bottom:none; }
    .filter-group-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
    .filter-group-header span.label { font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:6px; }
    .filter-group-header span.label .tick { color:var(--deep-rose); }
    .filter-group-header a.clear-link { font-size:11px; color:var(--text-light); text-decoration:underline; }
    .filter-group-header a.clear-link:hover { color:var(--deep-rose); }
    .filter-checkbox-list { display:flex; flex-direction:column; gap:2px; max-height:260px; overflow-y:auto; }
    .filter-checkbox-list.short { max-height:none; }
    .filter-checkbox-item { display:flex; align-items:center; gap:9px; padding:6px 2px; font-size:13.5px; color:var(--text-mid); cursor:pointer; }
    .filter-checkbox-item:hover { color:var(--deep-rose); }
    .filter-checkbox-item input[type=checkbox], .filter-checkbox-item input[type=radio] { width:16px; height:16px; accent-color:var(--rose); cursor:pointer; flex-shrink:0; }
    .filter-checkbox-item .fc-count { color:var(--text-light); font-size:12px; margin-left:auto; padding-left:6px; }
    .filter-checkbox-item .fc-stars { color:var(--gold); letter-spacing:1px; }
    .filter-checkbox-item .fc-more { color:var(--text-mid); font-size:12.5px; }
    .see-toggle { font-size:12px; font-weight:600; color:var(--rose); text-decoration:underline; cursor:pointer; margin-top:8px; display:inline-block; background:none; border:none; padding:0; }
    .price-range { display:flex; gap:8px; }
    .price-range input { width:100%; padding:8px 12px; border:2px solid #F0E4DC; border-radius:8px; font-size:13px; }
    .price-range button { padding:8px 16px; background:var(--rose); color:white; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
 
 
    .results-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:16px; }
    .results-count { font-size:14px; color:var(--text-light); }
    .results-count strong { color:var(--text-dark); }
    .sort-bar select { padding:8px 16px; border:2px solid #F0E4DC; border-radius:10px; font-size:14px; background:var(--white); cursor:pointer; outline:none; }
 
    .products-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
    .product-card { background:var(--white); border-radius:16px; overflow:hidden; transition:all .3s; position:relative; cursor:pointer; border:1px solid transparent; }
    .product-card:hover { transform:translateY(-4px); box-shadow:0 16px 40px rgba(196,77,101,.15); border-color:var(--blush); }
    .product-badge { position:absolute; top:12px; left:12px; background:var(--deep-rose); color:var(--white); font-size:10px; font-weight:700; padding:4px 10px; border-radius:6px; z-index:2; text-transform:uppercase; }
    .product-img { width:100%; height:200px; background:linear-gradient(135deg,#FFF0F5,#FFE4EA); display:flex; align-items:center; justify-content:center; font-size:80px; overflow:hidden; }
    .product-img img { width:100%; height:100%; object-fit:cover; }
    .product-body { padding:16px; }
    .manufacturer-row { display:flex; align-items:center; gap:8px; margin-bottom:8px; font-size:12px; color:var(--text-light); }
    .verified { color:var(--mint-dark); }
    .product-name { font-size:15px; font-weight:600; margin-bottom:6px; height:42px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
    .stars { color:var(--gold); font-size:12px; }
    .review-count { font-size:11px; color:var(--text-light); }
    .product-price { display:flex; align-items:center; gap:8px; margin:8px 0 10px; }
    .price-current { font-size:20px; font-weight:700; color:var(--deep-rose); }
    .price-old { font-size:13px; color:var(--text-light); text-decoration:line-through; }
    .btn-cart { width:100%; padding:10px; background:linear-gradient(135deg,var(--rose),var(--deep-rose)); color:var(--white); border:none; border-radius:10px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; margin:0 16px 16px; }
    .no-products { text-align:center; padding:80px 20px; grid-column:1/-1; }
    .no-products-icon { font-size:80px; margin-bottom:20px; opacity:.5; }
 
    footer { background:#1E0F0A; color:rgba(255,255,255,.8); margin-top:40px; }
    .footer-main { display:grid; grid-template-columns:2fr 1fr 1fr 1.2fr; gap:48px; padding:56px 48px; }
    .footer-logo { font-family:'Playfair Display', serif; font-size:28px; color:var(--white); margin-bottom:14px; }
    .footer-col h4 { font-size:14px; font-weight:700; text-transform:uppercase; color:var(--white); margin-bottom:18px; padding-bottom:10px; border-bottom:1px solid rgba(255,255,255,.12); }
    .footer-col ul { list-style:none; display:flex; flex-direction:column; gap:10px; }
    .footer-col ul li a { font-size:14px; color:rgba(255,255,255,.65); text-decoration:none; }
    .footer-col ul li a:hover { color:var(--blush); }
    .footer-bottom { border-top:1px solid rgba(255,255,255,.08); padding:20px 48px; }
    .footer-bottom p { font-size:13px; color:rgba(255,255,255,.4); }
 
    @media (max-width:1100px) { .products-grid { grid-template-columns:repeat(2,1fr); } .main-container { grid-template-columns:1fr; } .sidebar-filters { position:static; } }
    @media (max-width:768px) { .products-grid { grid-template-columns:1fr; } .header-main { flex-wrap:wrap; height:auto; padding:12px 20px; } .search-bar { order:3; width:100%; margin:10px 0 0; max-width:none; } .pillar-hero { padding:30px 20px; } .hero-emoji { display:none; } .main-container { padding:20px; } }
  </style>
</head>
<body>
 
<header>
  <div class="header-top">🌸 Free shipping on orders over Tsh 50,000 · Buyer Protection with Escrow 🍼</div>
  <div class="header-main">
    <a href="index.php" class="logo">
      <div class="logo-icon">🍼</div>
      <div class="logo-text">BabyBliss</div>
    </a>
    <form class="search-bar" action="baby_gear.php" method="GET">
      <i class="fas fa-search"></i>
      <input type="text" name="search" placeholder="Search baby gear..." value="<?= htmlspecialchars($search) ?>"/>
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
    <div class="hero-tag">🧸 Baby Gear Pillar</div>
    <h1>Everyday Essentials, Toys & Nursery Gear</h1>
    <p>Shop direct from verified manufacturers worldwide. Filter by price, condition, and category — every purchase protected by secure escrow.</p>
  </div>
  <div class="hero-emoji">🧸</div>
</div>
 
<div class="main-container">
  <aside>
    <!-- PILLAR SWITCHER -->
    <div class="pillar-switcher">
      <div class="switcher-title">Browse by Pillar</div>
      <a href="baby_gear.php" class="switch-item gear">🧸 Baby Gear</a>
      <a href="nutrition.php" class="switch-item nutrition">🍎 Nutrition</a>
      <a href="parenting.php" class="switch-item parenting">👶 Parenting</a>
    </div>
 
    <div class="sidebar-filters">
      <div class="filter-panel-header">
        <span>FILTER BY</span>
        <?php if ($has_active_filters): ?><a href="baby_gear.php">Clear All</a><?php endif; ?>
      </div>
 
      <form method="GET" action="baby_gear.php" id="filterForm">
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"/>
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>"/>
 
        <div class="filter-group">
          <div class="filter-group-header">
            <span class="label"><?php if ($category_filter): ?><span class="tick">✓</span><?php endif; ?> CATEGORIES</span>
            <?php if ($category_filter): ?><a class="clear-link" href="<?= buildFilterUrl('baby_gear.php', ['category' => null]) ?>">Clear</a><?php endif; ?>
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
            <?php if ($manufacturer_filter): ?><a class="clear-link" href="<?= buildFilterUrl('baby_gear.php', ['manufacturer' => null]) ?>">Clear</a><?php endif; ?>
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
            <?php if ($rating_filter): ?><a class="clear-link" href="<?= buildFilterUrl('baby_gear.php', ['rating' => null]) ?>">Clear</a><?php endif; ?>
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
  </aside>
 
  <div class="products-area">
    <div class="results-header">
      <div class="results-count">Showing <strong><?= $total_products ?></strong> baby gear products</div>
      <div class="sort-bar">
        <select onchange="window.location.href=this.value">
          <option value="?<?= http_build_query(array_merge($_GET, ['sort' => 'featured'])) ?>" <?= $sort=='featured'?'selected':'' ?>>Featured</option>
          <option value="?<?= http_build_query(array_merge($_GET, ['sort' => 'price_low'])) ?>" <?= $sort=='price_low'?'selected':'' ?>>Price: Low to High</option>
          <option value="?<?= http_build_query(array_merge($_GET, ['sort' => 'price_high'])) ?>" <?= $sort=='price_high'?'selected':'' ?>>Price: High to Low</option>
          <option value="?<?= http_build_query(array_merge($_GET, ['sort' => 'popular'])) ?>" <?= $sort=='popular'?'selected':'' ?>>Most Popular</option>
          <option value="?<?= http_build_query(array_merge($_GET, ['sort' => 'rating'])) ?>" <?= $sort=='rating'?'selected':'' ?>>Top Rated</option>
        </select>
      </div>
    </div>
 
    <?php if ($total_products === 0): ?>
      <div class="no-products">
        <div class="no-products-icon">📦</div>
        <h3 style="font-family:'Playfair Display',serif;font-size:24px;margin-bottom:10px;">No Products Found</h3>
        <p style="color:var(--text-light);">Try adjusting your filters.</p>
        <a href="baby_gear.php" class="btn-login" style="display:inline-block;margin-top:16px;">Clear Filters</a>
      </div>
    <?php else: ?>
      <div class="products-grid">
        <?php mysqli_data_seek($products, 0); while ($p = mysqli_fetch_assoc($products)):
          $img_path = !empty($p['image_url']) ? 'uploads/products/' . $p['image_url'] : '';
          $cart_img = !empty($img_path) && file_exists($img_path) ? $img_path : placeholderPhoto($p['id'], 'babytoy');
        ?>
        <div class="product-card">
          <?php if (!empty($p['badge'])): ?><div class="product-badge"><?= htmlspecialchars($p['badge']) ?></div><?php endif; ?>
          <a href="product.php?id=<?= $p['id'] ?>" style="text-decoration:none;color:inherit;display:block;">
            <div class="product-img">
              <?php if (!empty($p['image_url']) && file_exists('uploads/products/' . $p['image_url'])): ?>
                <img src="uploads/products/<?= htmlspecialchars($p['image_url']) ?>" alt=""/>
              <?php else: ?>
                <img src="<?= placeholderPhoto($p['id'], 'babytoy') ?>" alt="" loading="lazy"/>
              <?php endif; ?>
            </div>
            <div class="product-body">
              <div class="manufacturer-row">🏭 <?= htmlspecialchars($p['company_name'] ?? 'Unknown') ?> <span class="verified"><i class="fas fa-check-circle"></i></span></div>
              <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
              <div><span class="stars"><?= str_repeat('★', round($p['rating'] ?? 5)) ?></span> <span class="review-count">(<?= $p['review_count'] ?? rand(10,200) ?>)</span></div>
              <div class="product-price">
                <span class="price-current">Tsh<?= number_format($p['price'],0) ?></span>
                <?php if (!empty($p['old_price']) && $p['old_price']>0): ?><span class="price-old">Tsh<?= number_format($p['old_price'],0) ?></span><?php endif; ?>
              </div>
            </div>
          </a>
          <?php if (!empty($p['has_variants'])): ?>
            <a href="product.php?id=<?= $p['id'] ?>" class="btn-cart"><i class="fas fa-sliders-h"></i> View Options</a>
          <?php else: ?>
            <a href="cart.php?add=<?= $p['id'] ?>&name=<?= urlencode($p['name']) ?>&price=<?= $p['price'] ?>&image=<?= urlencode($cart_img) ?>" class="btn-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</a>
          <?php endif; ?>
        </div>
        <?php endwhile; ?>
      </div>
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