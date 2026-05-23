<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";

$upload_dir = "uploads/products/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// Add Product
if (isset($_POST['add_product'])) {
    $name        = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category    = mysqli_real_escape_string($conn, $_POST['category']);
    $price       = floatval($_POST['price']);
    $old_price   = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : 0;
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $badge       = mysqli_real_escape_string($conn, $_POST['badge']);
    $age_range   = mysqli_real_escape_string($conn, $_POST['age_range']);
    $image_name  = "";

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed  = ['jpg','jpeg','png','gif','webp'];
        $filename = $_FILES['product_image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($filetype, $allowed)) {
            $image_name  = uniqid() . '_' . basename($filename);
            $target_path = $upload_dir . $image_name;
            if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $target_path))
                $error = "Failed to upload image.";
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF, WEBP allowed.";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO products (name, category, price, old_price, description, badge, image, age_range, created_at)
                VALUES ('$name','$category',$price,$old_price,'$description','$badge','$image_name','$age_range',NOW())";
        if (mysqli_query($conn, $sql)) $success = "Product added successfully! 🎉";
        else $error = "Error: " . mysqli_error($conn);
    }
}

// Delete Product
if (isset($_GET['delete'])) {
    $id         = intval($_GET['delete']);
    $img_result = mysqli_query($conn, "SELECT image FROM products WHERE id = $id");
    $img_data   = mysqli_fetch_assoc($img_result);
    if ($img_data && !empty($img_data['image']) && file_exists($upload_dir . $img_data['image']))
        unlink($upload_dir . $img_data['image']);
    if (mysqli_query($conn, "DELETE FROM products WHERE id = $id")) $success = "Product deleted.";
    else $error = "Error deleting product!";
    header("Location: admin.php"); exit();
}

$products       = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
$total_products = mysqli_num_rows($products);
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin — BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Cabinet+Grotesk:wght@400;500;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    /* ═══════════════════════════════════════════
       TOKENS
    ═══════════════════════════════════════════ */
    :root {
      --rose:      #E8738A;
      --rose-dark: #C44D65;
      --rose-glow: rgba(232,115,138,.18);
      --mint:      #5FB8A0;
      --mint-soft: rgba(95,184,160,.12);
      --gold:      #F0C060;
      --gold-soft: rgba(240,192,96,.14);
      --ink:       #1A0A0E;
      --ink-2:     #2E1219;
      --ink-3:     #4A2030;
      --parchment: #FFF6EE;
      --parchment-2:#FFF0E6;
      --silk:      #FFFFFF;
      --mist:      rgba(255,255,255,.06);
      --border:    rgba(232,115,138,.15);
      --border-light: #F5E6DF;
      --text:      #1A0A0E;
      --text-mid:  #6B3A4A;
      --text-soft: #A07080;
      --r:         16px;
      --shadow:    0 4px 32px rgba(30,8,16,.08);
      --shadow-lg: 0 12px 48px rgba(30,8,16,.12);
    }

    /* ═══════════════════════════════════════════
       BASE
    ═══════════════════════════════════════════ */
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
    html { font-size:16px; }
    body {
      font-family: 'Cabinet Grotesk', sans-serif;
      background: var(--parchment);
      color: var(--text);
      min-height: 100vh;
      display: flex;
    }

    /* ═══════════════════════════════════════════
       SIDEBAR
    ═══════════════════════════════════════════ */
    .sidebar {
      width: 256px;
      min-height: 100vh;
      background: var(--ink);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0; left: 0;
      z-index: 50;
      overflow: hidden;
    }

    /* Decorative gradient blob inside sidebar */
    .sidebar::before {
      content: '';
      position: absolute;
      width: 280px; height: 280px;
      background: radial-gradient(circle, rgba(232,115,138,.25) 0%, transparent 70%);
      top: -80px; left: -80px;
      pointer-events: none;
    }
    .sidebar::after {
      content: '';
      position: absolute;
      width: 200px; height: 200px;
      background: radial-gradient(circle, rgba(95,184,160,.15) 0%, transparent 70%);
      bottom: 60px; right: -60px;
      pointer-events: none;
    }

    .sidebar-logo {
      padding: 32px 28px 24px;
      border-bottom: 1px solid var(--mist);
      position: relative; z-index: 1;
    }
    .sidebar-logo .wordmark {
      font-family: 'Cormorant Garamond', serif;
      font-size: 26px; font-weight: 700;
      color: #fff;
      display: flex; align-items: center; gap: 10px;
      letter-spacing: .5px;
    }
    .logo-dot {
      width: 32px; height: 32px;
      background: linear-gradient(135deg, var(--rose), var(--rose-dark));
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px;
      box-shadow: 0 4px 14px var(--rose-glow);
    }
    .sidebar-label {
      font-size: 10px; font-weight: 700;
      letter-spacing: 3px; text-transform: uppercase;
      color: rgba(255,255,255,.35);
      padding: 20px 28px 8px;
      position: relative; z-index: 1;
    }
    .nav-item {
      display: flex; align-items: center; gap: 12px;
      padding: 12px 28px;
      color: rgba(255,255,255,.55);
      font-size: 14px; font-weight: 500;
      text-decoration: none;
      transition: all .2s;
      position: relative; z-index: 1;
      border-left: 3px solid transparent;
      cursor: pointer;
    }
    .nav-item i { width: 18px; font-size: 15px; }
    .nav-item:hover { color: #fff; background: var(--mist); border-left-color: var(--rose); }
    .nav-item.active { color: #fff; background: rgba(232,115,138,.12); border-left-color: var(--rose); }

    .sidebar-footer {
      margin-top: auto;
      padding: 20px 28px;
      border-top: 1px solid var(--mist);
      position: relative; z-index: 1;
    }
    .admin-chip {
      display: flex; align-items: center; gap: 10px;
      background: var(--mist); border-radius: 12px; padding: 12px 14px;
    }
    .admin-avatar {
      width: 34px; height: 34px; border-radius: 10px;
      background: linear-gradient(135deg, var(--rose), var(--rose-dark));
      display: flex; align-items: center; justify-content: center; font-size: 16px;
    }
    .admin-name  { font-size: 13px; font-weight: 700; color: #fff; }
    .admin-role  { font-size: 11px; color: rgba(255,255,255,.4); }

    /* ═══════════════════════════════════════════
       MAIN CONTENT
    ═══════════════════════════════════════════ */
    .main {
      margin-left: 256px;
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Top header bar */
    .topbar {
      background: var(--silk);
      border-bottom: 1px solid var(--border-light);
      padding: 0 40px;
      height: 68px;
      display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 40;
    }
    .topbar-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 24px; font-weight: 700; color: var(--text);
    }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .btn-primary {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 10px 22px;
      background: linear-gradient(135deg, var(--rose), var(--rose-dark));
      color: #fff; border: none; border-radius: 10px;
      font-family: 'Cabinet Grotesk', sans-serif;
      font-size: 14px; font-weight: 700; cursor: pointer;
      transition: all .2s; text-decoration: none;
      box-shadow: 0 4px 14px var(--rose-glow);
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--rose-glow); }

    .page-body { padding: 36px 40px; flex: 1; }

    /* ═══════════════════════════════════════════
       ALERTS
    ═══════════════════════════════════════════ */
    .alert {
      display: flex; align-items: center; gap: 12px;
      padding: 14px 20px; border-radius: var(--r);
      font-size: 14px; font-weight: 600; margin-bottom: 24px;
      animation: slideDown .3s ease;
    }
    @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    .alert-success { background: #EAF8F4; border: 1px solid #B0E0D0; color: #2E7D62; }
    .alert-error   { background: #FFF0F3; border: 1px solid #F5B8C8; color: var(--rose-dark); }

    /* ═══════════════════════════════════════════
       STAT CARDS
    ═══════════════════════════════════════════ */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 32px;
    }
    .stat-card {
      background: var(--silk);
      border-radius: 20px;
      padding: 26px 28px;
      box-shadow: var(--shadow);
      display: flex; align-items: center; gap: 18px;
      position: relative; overflow: hidden;
      transition: transform .25s, box-shadow .25s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
    .stat-card::after {
      content: '';
      position: absolute;
      top: -30px; right: -30px;
      width: 100px; height: 100px;
      border-radius: 50%;
      opacity: .06;
    }
    .stat-card:nth-child(1)::after { background: var(--rose); }
    .stat-card:nth-child(2)::after { background: var(--mint); }
    .stat-card:nth-child(3)::after { background: var(--gold); }

    .stat-icon {
      width: 52px; height: 52px; border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; flex-shrink: 0;
    }
    .stat-icon.pink { background: linear-gradient(135deg, #FFE4EC, #FFB8CC); }
    .stat-icon.teal { background: linear-gradient(135deg, #D4F5EC, #A8E0D0); }
    .stat-icon.gold { background: linear-gradient(135deg, #FFF0C0, #FFE080); }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-label { font-size: 13px; color: var(--text-soft); margin-top: 4px; font-weight: 500; }

    /* ═══════════════════════════════════════════
       CARDS / PANELS
    ═══════════════════════════════════════════ */
    .card {
      background: var(--silk);
      border-radius: 20px;
      box-shadow: var(--shadow);
      overflow: hidden;
      margin-bottom: 28px;
    }
    .card-header {
      padding: 22px 28px;
      border-bottom: 1px solid var(--border-light);
      display: flex; align-items: center; justify-content: space-between;
    }
    .card-title {
      font-family: 'Cormorant Garamond', serif;
      font-size: 20px; font-weight: 700; color: var(--text);
      display: flex; align-items: center; gap: 10px;
    }
    .card-title .title-dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: linear-gradient(135deg, var(--rose), var(--rose-dark));
    }
    .card-body { padding: 28px; }

    /* ═══════════════════════════════════════════
       ADD PRODUCT FORM
    ═══════════════════════════════════════════ */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }
    .form-group { display: flex; flex-direction: column; gap: 7px; }
    .form-group.full { grid-column: 1 / -1; }
    label {
      font-size: 12px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 1px;
      color: var(--text-mid);
    }
    .form-control {
      padding: 13px 16px;
      border: 2px solid var(--border-light);
      border-radius: 12px;
      font-family: 'Cabinet Grotesk', sans-serif;
      font-size: 14px; color: var(--text);
      background: var(--parchment);
      outline: none;
      transition: border .2s, box-shadow .2s;
    }
    .form-control:focus {
      border-color: var(--rose);
      box-shadow: 0 0 0 4px rgba(232,115,138,.1);
      background: var(--silk);
    }
    select.form-control { appearance: none; cursor: pointer; }
    textarea.form-control { resize: vertical; min-height: 90px; }

    /* File upload */
    .file-zone {
      border: 2px dashed var(--border-light);
      border-radius: 14px;
      padding: 28px 20px;
      text-align: center;
      cursor: pointer;
      transition: all .2s;
      background: var(--parchment);
      position: relative;
    }
    .file-zone:hover { border-color: var(--rose); background: #FFF5F7; }
    .file-zone input[type=file] {
      position: absolute; inset: 0; opacity: 0; cursor: pointer;
    }
    .file-zone-icon { font-size: 32px; margin-bottom: 8px; }
    .file-zone-text { font-size: 14px; font-weight: 600; color: var(--text-mid); }
    .file-zone-sub  { font-size: 12px; color: var(--text-soft); margin-top: 4px; }
    .preview-box { margin-top: 14px; display: none; }
    .preview-box img { max-width: 160px; border-radius: 12px; border: 2px solid var(--border-light); }

    .form-actions { display: flex; gap: 12px; margin-top: 4px; align-items: center; }
    .btn-submit {
      padding: 13px 32px;
      background: linear-gradient(135deg, var(--rose), var(--rose-dark));
      color: #fff; border: none; border-radius: 12px;
      font-family: 'Cabinet Grotesk', sans-serif;
      font-size: 15px; font-weight: 700; cursor: pointer;
      transition: all .2s;
      box-shadow: 0 4px 14px var(--rose-glow);
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--rose-glow); }
    .btn-reset {
      padding: 13px 24px;
      background: var(--parchment-2);
      color: var(--text-mid); border: 2px solid var(--border-light);
      border-radius: 12px;
      font-family: 'Cabinet Grotesk', sans-serif;
      font-size: 15px; font-weight: 700; cursor: pointer;
      transition: all .2s;
    }
    .btn-reset:hover { border-color: var(--rose); color: var(--rose-dark); }

    /* ═══════════════════════════════════════════
       PRODUCTS TABLE
    ═══════════════════════════════════════════ */
    .search-wrap { position: relative; max-width: 280px; }
    .search-wrap i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-soft); font-size: 14px; }
    .search-input {
      width: 100%; padding: 10px 14px 10px 40px;
      border: 2px solid var(--border-light); border-radius: 10px;
      font-family: 'Cabinet Grotesk', sans-serif; font-size: 14px;
      outline: none; background: var(--parchment); color: var(--text);
      transition: border .2s;
    }
    .search-input:focus { border-color: var(--rose); background: var(--silk); }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: var(--parchment); }
    th {
      padding: 12px 18px;
      text-align: left;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 1.5px;
      color: var(--text-soft);
      border-bottom: 2px solid var(--border-light);
      white-space: nowrap;
    }
    td {
      padding: 16px 18px;
      border-bottom: 1px solid var(--border-light);
      font-size: 14px;
      vertical-align: middle;
    }
    tbody tr { transition: background .15s; }
    tbody tr:hover { background: #FFF8F5; }
    tbody tr:last-child td { border-bottom: none; }

    .prod-cell { display: flex; align-items: center; gap: 14px; }
    .prod-thumb {
      width: 52px; height: 52px; border-radius: 12px;
      background: linear-gradient(135deg, #FFE4EC, #FFD0DC);
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; flex-shrink: 0; overflow: hidden;
    }
    .prod-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .prod-name  { font-weight: 700; font-size: 14px; }
    .prod-cat   { font-size: 12px; color: var(--text-soft); margin-top: 2px; }

    .badge-pill {
      display: inline-block;
      padding: 4px 12px; border-radius: 20px;
      font-size: 11px; font-weight: 700;
    }
    .pill-hot  { background: #FFF0F3; color: var(--rose-dark); }
    .pill-new  { background: #E8F8F4; color: #2E7D62; }
    .pill-sale { background: #FFF8E0; color: #916800; }
    .pill-none { background: var(--parchment-2); color: var(--text-soft); }

    .price-now { font-size: 16px; font-weight: 800; color: var(--rose-dark); }
    .price-was { font-size: 12px; color: var(--text-soft); text-decoration: line-through; margin-top: 2px; }

    .stock-ok  { color: var(--mint); font-weight: 700; }
    .stock-low { color: var(--gold); font-weight: 700; }

    .action-wrap { display: flex; gap: 8px; }
    .act-btn {
      width: 34px; height: 34px; border-radius: 9px;
      border: none; cursor: pointer; font-size: 14px;
      display: flex; align-items: center; justify-content: center;
      transition: all .2s;
    }
    .act-edit   { background: #E8F8F4; color: var(--mint); }
    .act-edit:hover   { background: var(--mint); color: #fff; }
    .act-delete { background: #FFF0F3; color: var(--rose-dark); }
    .act-delete:hover { background: var(--rose-dark); color: #fff; }

    /* Empty state */
    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-state .empty-icon { font-size: 56px; margin-bottom: 14px; opacity: .5; }
    .empty-state p { color: var(--text-soft); font-size: 15px; }

    /* ═══════════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════════ */
    @media (max-width: 1024px) {
      .sidebar { display: none; }
      .main    { margin-left: 0; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 640px) {
      .page-body   { padding: 20px; }
      .topbar      { padding: 0 20px; }
      .stats-grid  { grid-template-columns: 1fr; }
      .form-grid   { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-dot">🍼</div>
    <div class="wordmark">BabyBliss</div>
  </div>

  <div class="sidebar-label">Main</div>
  <a href="admin.php" class="nav-item active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="#add-product-section" class="nav-item"
     onclick="document.getElementById('add-product-section').scrollIntoView({behavior:'smooth'});return false;">
    <i class="fas fa-plus-circle"></i> Add Product
  </a>

  <div class="sidebar-label">Store</div>
  <a href="index.php"  class="nav-item"><i class="fas fa-store"></i> View Shop</a>
  <a href="#"          class="nav-item"><i class="fas fa-shopping-bag"></i> Orders</a>
  <a href="#"          class="nav-item"><i class="fas fa-users"></i> Customers</a>

  <div class="sidebar-label">Account</div>
  <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>

  <div class="sidebar-footer">
    <div class="admin-chip">
      <div class="admin-avatar">👑</div>
      <div>
        <div class="admin-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></div>
        <div class="admin-role">Administrator</div>
      </div>
    </div>
  </div>
</aside>

<!-- ═══════════════════ MAIN ═══════════════════ -->
<div class="main">

  <!-- Top bar -->
  <header class="topbar">
    <div class="topbar-title">Dashboard &nbsp;👋</div>
    <div class="topbar-right">
      <a href="index.php" style="font-size:13px;font-weight:700;color:var(--text-soft);text-decoration:none;padding:8px 16px;border:2px solid var(--border-light);border-radius:10px;">
        <i class="fas fa-store"></i> View Store
      </a>
      <button class="btn-primary"
        onclick="document.getElementById('add-product-section').scrollIntoView({behavior:'smooth'})">
        <i class="fas fa-plus"></i> Add Product
      </button>
    </div>
  </header>

  <div class="page-body">

    <!-- Alerts -->
    <?php if ($success): ?>
      <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ── STAT CARDS ── -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon pink">📦</div>
        <div>
          <div class="stat-value"><?= $total_products ?></div>
          <div class="stat-label">Total Products</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon teal">👥</div>
        <div>
          <div class="stat-value"><?= $total_users ?></div>
          <div class="stat-label">Customers</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon gold">🛒</div>
        <div>
          <div class="stat-value"><?= $total_orders ?></div>
          <div class="stat-label">Orders</div>
        </div>
      </div>
    </div>

    <!-- ── ADD PRODUCT FORM ── -->
    <div class="card" id="add-product-section">
      <div class="card-header">
        <div class="card-title"><div class="title-dot"></div> Add New Product</div>
      </div>
      <div class="card-body">
        <form method="POST" action="admin.php" enctype="multipart/form-data">
          <div class="form-grid">

            <div class="form-group">
              <label>Product Name</label>
              <input type="text" name="product_name" class="form-control" placeholder="e.g. Cuddle Bear Plush" required/>
            </div>

            <div class="form-group">
              <label>Category</label>
              <select name="category" class="form-control" required>
                <option value="">Select category…</option>
                <option>Plush Toys</option><option>Puzzles</option><option>Musical</option>
                <option>Vehicles</option><option>Art &amp; Craft</option><option>Outdoor Play</option>
                <option>Nursery</option><option>Pretend Play</option>
              </select>
            </div>

            <div class="form-group">
              <label>Price ($)</label>
              <input type="number" name="price" class="form-control" step="0.01" placeholder="24.99" required/>
            </div>

            <div class="form-group">
              <label>Old Price ($) <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
              <input type="number" name="old_price" class="form-control" step="0.01" placeholder="34.99"/>
            </div>

            <div class="form-group">
              <label>Badge</label>
              <select name="badge" class="form-control">
                <option value="">No badge</option>
                <option value="HOT">🔥 HOT</option>
                <option value="NEW">✨ NEW</option>
                <option value="SALE">🏷️ SALE</option>
              </select>
            </div>

            <div class="form-group">
              <label>Age Range</label>
              <select name="age_range" class="form-control">
                <option value="0-12 Months">0 – 12 Months</option>
                <option value="1-3 Years">1 – 3 Years</option>
                <option value="3-5 Years">3 – 5 Years</option>
                <option value="5+ Years">5+ Years</option>
              </select>
            </div>

            <div class="form-group full">
              <label>Description</label>
              <textarea name="description" class="form-control" placeholder="Describe the product…"></textarea>
            </div>

            <!-- File upload zone -->
            <div class="form-group full">
              <label>Product Image</label>
              <div class="file-zone" onclick="document.getElementById('productImg').click()">
                <input type="file" name="product_image" id="productImg" accept="image/*" onchange="previewImg(this)"/>
                <div class="file-zone-icon">📸</div>
                <div class="file-zone-text">Click to upload image</div>
                <div class="file-zone-sub">JPG, PNG, WEBP, GIF — max 5 MB</div>
              </div>
              <div class="preview-box" id="previewBox">
                <img id="previewImg" src="" alt="Preview"/>
                <p id="previewName"></p>
              </div>
            </div>

          </div><!-- /form-grid -->

          <div class="form-actions" style="margin-top:20px;">
            <button type="submit" name="add_product" class="btn-submit">
              <i class="fas fa-plus"></i> Add Product
            </button>
            <button type="reset" class="btn-reset" onclick="document.getElementById('previewBox').style.display='none'">
              Clear Form
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- ── PRODUCTS TABLE ── -->
    <div class="card">
      <div class="card-header">
        <div class="card-title"><div class="title-dot"></div> All Products (<?= $total_products ?>)</div>
        <div class="search-wrap">
          <i class="fas fa-search"></i>
          <input type="text" class="search-input" id="searchInput" placeholder="Search products…" oninput="filterTable(this.value)"/>
        </div>
      </div>

      <div class="table-wrap">
        <?php
        // Reset pointer (mysql_query consumed it above for counting)
        mysqli_data_seek($products, 0);
        ?>
        <?php if ($total_products === 0): ?>
          <div class="empty-state">
            <div class="empty-icon">📦</div>
            <p>No products yet. Add your first one above!</p>
          </div>
        <?php else: ?>
        <table id="prodTable">
          <thead>
            <tr>
              <th>Product</th>
              <th>Category</th>
              <th>Price</th>
              <th>Badge</th>
              <th>Age</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($p = mysqli_fetch_assoc($products)): ?>
            <tr>
              <td>
                <div class="prod-cell">
                  <div class="prod-thumb">
                    <?php if (!empty($p['image']) && file_exists($upload_dir . $p['image'])): ?>
                      <img src="<?= $upload_dir . htmlspecialchars($p['image']) ?>" alt=""/>
                    <?php else: ?>
                      🧸
                    <?php endif; ?>
                  </div>
                  <div>
                    <div class="prod-name"><?= htmlspecialchars($p['name']) ?></div>
                    <div class="prod-cat">#<?= $p['id'] ?></div>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($p['category']) ?></td>
              <td>
                <div class="price-now">$<?= number_format($p['price'], 2) ?></div>
                <?php if (!empty($p['old_price']) && $p['old_price'] > 0): ?>
                  <div class="price-was">$<?= number_format($p['old_price'], 2) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php
                $badge = strtolower($p['badge'] ?? '');
                $pillClass = match($badge) {
                  'hot'  => 'pill-hot',
                  'new'  => 'pill-new',
                  'sale' => 'pill-sale',
                  default => 'pill-none'
                };
                echo '<span class="badge-pill ' . $pillClass . '">' . ($p['badge'] ?: '—') . '</span>';
                ?>
              </td>
              <td><?= htmlspecialchars($p['age_range'] ?? '—') ?></td>
              <td>
                <div class="action-wrap">
                  <button class="act-btn act-edit" title="Edit" onclick="alert('Edit feature coming soon!')">
                    <i class="fas fa-edit"></i>
                  </button>
                  <a href="admin.php?delete=<?= $p['id'] ?>"
                     onclick="return confirm('Delete this product permanently?')"
                     class="act-btn act-delete" title="Delete">
                    <i class="fas fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>

  </div><!-- /page-body -->
</div><!-- /main -->

<script>
  // Image preview
  function previewImg(input) {
    const box  = document.getElementById('previewBox');
    const img  = document.getElementById('previewImg');
    const name = document.getElementById('previewName');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        img.src = e.target.result;
        name.textContent = input.files[0].name;
        box.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  // Live table search
  function filterTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#prodTable tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  // Highlight active nav on scroll
  const addSection = document.getElementById('add-product-section');
  window.addEventListener('scroll', () => {
    const navItems = document.querySelectorAll('.nav-item');
    if (window.scrollY + 200 > addSection.offsetTop) {
      navItems.forEach(n => n.classList.remove('active'));
      navItems[1].classList.add('active');
    } else {
      navItems.forEach(n => n.classList.remove('active'));
      navItems[0].classList.add('active');
    }
  });
</script>
</body>
</html>
