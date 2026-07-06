<?php
session_start();
require_once "config.php";

// Check if manufacturer
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manufacturer' || !isset($_SESSION['manufacturer_id'])) {
    header("Location: login.php");
    exit();
}

$manufacturer_id = intval($_SESSION['manufacturer_id']);
$user_id = intval($_SESSION['user_id']);
$success = "";
$error = "";

$upload_dir = "uploads/products/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// Add Product
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $old_price = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : 0;
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $stock = intval($_POST['stock'] ?? 0);
    $badge = mysqli_real_escape_string($conn, $_POST['badge'] ?? '');
    $age_range = mysqli_real_escape_string($conn, $_POST['age_range'] ?? '0-12 Months');
    $condition_type = mysqli_real_escape_string($conn, $_POST['condition_type'] ?? 'new');
    $shipping_cost = floatval($_POST['shipping_cost'] ?? 0);
    $estimated_delivery = mysqli_real_escape_string($conn, $_POST['estimated_delivery'] ?? '7-14 days');
    $image_name = "";

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $filename = $_FILES['product_image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($filetype, $allowed)) {
            $image_name = 'prod_' . uniqid() . '_' . basename($filename);
            if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_dir . $image_name))
                $error = "Failed to upload image.";
        } else {
            $error = "Invalid file type.";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO products (manufacturer_id, category_id, name, description, price, old_price, stock, badge, age_range, condition_type, shipping_cost, estimated_delivery, image_url, status, created_at)
                VALUES ($manufacturer_id, $category_id, '$name', '$description', $price, $old_price, $stock, '$badge', '$age_range', '$condition_type', $shipping_cost, '$estimated_delivery', '$image_name', 'pending_review', NOW())";
        if (mysqli_query($conn, $sql)) $success = "Product submitted for review! It will be live once approved.";
        else $error = "Error: " . mysqli_error($conn);
    }
}

// Delete Product
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $check = mysqli_query($conn, "SELECT image_url FROM products WHERE id = $id AND manufacturer_id = $manufacturer_id");
    if ($img = mysqli_fetch_assoc($check)) {
        if (!empty($img['image_url']) && file_exists($upload_dir . $img['image_url'])) unlink($upload_dir . $img['image_url']);
    }
    mysqli_query($conn, "DELETE FROM products WHERE id = $id AND manufacturer_id = $manufacturer_id");
    $success = "Product deleted.";
    header("Location: manufacturer_dashboard.php?tab=products"); exit();
}

// Get manufacturer stats
$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM manufacturers WHERE id = $manufacturer_id"));
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE manufacturer_id = $manufacturer_id"))['count'];
$active_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE manufacturer_id = $manufacturer_id AND status = 'active'"))['count'];
$total_sales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(oi.quantity), 0) as total FROM order_items oi WHERE oi.manufacturer_id = $manufacturer_id"))['total'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(oi.unit_price * oi.quantity), 0) as total FROM order_items oi WHERE oi.manufacturer_id = $manufacturer_id"))['total'];

// Get products
$products = mysqli_query($conn, "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.manufacturer_id = $manufacturer_id ORDER BY p.created_at DESC");

// Get orders
$orders = mysqli_query($conn, "SELECT o.*, oi.quantity, oi.unit_price, p.name as product_name, p.image_url, u.first_name, u.last_name, u.email 
                                FROM orders o 
                                JOIN order_items oi ON o.id = oi.order_id 
                                JOIN products p ON oi.product_id = p.id 
                                JOIN users u ON o.user_id = u.id
                                WHERE oi.manufacturer_id = $manufacturer_id 
                                ORDER BY o.ordered_at DESC");

$categories = mysqli_query($conn, "SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
$active_tab = $_GET['tab'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manufacturer Dashboard - BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Cabinet+Grotesk:wght@400;500;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root { --rose: #E8738A; --rose-dark: #C44D65; --rose-glow: rgba(232,115,138,.18); --mint: #5FB8A0; --mint-soft: rgba(95,184,160,.12); --gold: #F0C060; --ink: #1A0A0E; --parchment: #FFF6EE; --silk: #FFFFFF; --mist: rgba(255,255,255,.06); --border: rgba(232,115,138,.15); --border-light: #F5E6DF; --text: #1A0A0E; --text-mid: #6B3A4A; --text-soft: #A07080; --r: 16px; --shadow: 0 4px 32px rgba(30,8,16,.08); --shadow-lg: 0 12px 48px rgba(30,8,16,.12); }
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'Cabinet Grotesk', sans-serif; background: var(--parchment); color: var(--text); min-height: 100vh; display: flex; }
    .sidebar { width: 256px; min-height: 100vh; background: var(--ink); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 50; }
    .sidebar-logo { padding: 32px 28px 24px; border-bottom: 1px solid var(--mist); }
    .sidebar-logo .wordmark { font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 10px; }
    .logo-dot { width: 32px; height: 32px; background: linear-gradient(135deg, var(--rose), var(--rose-dark)); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
    .sidebar-label { font-size: 10px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,.35); padding: 20px 28px 8px; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 28px; color: rgba(255,255,255,.55); font-size: 14px; font-weight: 500; text-decoration: none; transition: all .2s; border-left: 3px solid transparent; cursor: pointer; }
    .nav-item i { width: 18px; font-size: 15px; }
    .nav-item:hover { color: #fff; background: var(--mist); border-left-color: var(--rose); }
    .nav-item.active { color: #fff; background: rgba(232,115,138,.12); border-left-color: var(--rose); }
    .sidebar-footer { margin-top: auto; padding: 20px 28px; border-top: 1px solid var(--mist); }
    .admin-chip { display: flex; align-items: center; gap: 10px; background: var(--mist); border-radius: 12px; padding: 12px 14px; }
    .admin-avatar { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, var(--mint), #3A9E88); display: flex; align-items: center; justify-content: center; font-size: 16px; }
    .admin-name { font-size: 13px; font-weight: 700; color: #fff; }
    .admin-role { font-size: 11px; color: rgba(255,255,255,.4); }
    .main { margin-left: 256px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
    .topbar { background: var(--silk); border-bottom: 1px solid var(--border-light); padding: 0 40px; height: 68px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 40; }
    .topbar-title { font-family: 'Cormorant Garamond', serif; font-size: 24px; font-weight: 700; color: var(--text); }
    .topbar-right { display: flex; align-items: center; gap: 12px; }
    .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 10px 22px; background: linear-gradient(135deg, var(--rose), var(--rose-dark)); color: #fff; border: none; border-radius: 10px; font-family: 'Cabinet Grotesk', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .2s; text-decoration: none; box-shadow: 0 4px 14px var(--rose-glow); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--rose-glow); }
    .page-body { padding: 36px 40px; flex: 1; }
    .alert { display: flex; align-items: center; gap: 12px; padding: 14px 20px; border-radius: var(--r); font-size: 14px; font-weight: 600; margin-bottom: 24px; }
    .alert-success { background: #EAF8F4; border: 1px solid #B0E0D0; color: #2E7D62; }
    .alert-error { background: #FFF0F3; border: 1px solid #F5B8C8; color: var(--rose-dark); }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 32px; }
    .stat-card { background: var(--silk); border-radius: 20px; padding: 26px 28px; box-shadow: var(--shadow); display: flex; align-items: center; gap: 18px; transition: transform .25s, box-shadow .25s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }
    .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
    .stat-icon.pink { background: linear-gradient(135deg, #FFE4EC, #FFB8CC); }
    .stat-icon.teal { background: linear-gradient(135deg, #D4F5EC, #A8E0D0); }
    .stat-icon.gold { background: linear-gradient(135deg, #FFF0C0, #FFE080); }
    .stat-icon.purple { background: linear-gradient(135deg, #E8E0FF, #D0C4FF); }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-label { font-size: 13px; color: var(--text-soft); margin-top: 4px; font-weight: 500; }
    .card { background: var(--silk); border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; margin-bottom: 28px; }
    .card-header { padding: 22px 28px; border-bottom: 1px solid var(--border-light); display: flex; align-items: center; justify-content: space-between; }
    .card-title { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px; }
    .card-title .title-dot { width: 8px; height: 8px; border-radius: 50%; background: linear-gradient(135deg, var(--rose), var(--rose-dark)); }
    .card-body { padding: 28px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .form-group { display: flex; flex-direction: column; gap: 7px; }
    .form-group.full { grid-column: 1 / -1; }
    label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-mid); }
    .form-control { padding: 13px 16px; border: 2px solid var(--border-light); border-radius: 12px; font-family: 'Cabinet Grotesk', sans-serif; font-size: 14px; color: var(--text); background: var(--parchment); outline: none; transition: border .2s, box-shadow .2s; }
    .form-control:focus { border-color: var(--rose); box-shadow: 0 0 0 4px rgba(232,115,138,.1); background: var(--silk); }
    select.form-control { appearance: none; cursor: pointer; }
    textarea.form-control { resize: vertical; min-height: 90px; }
    .file-zone { border: 2px dashed var(--border-light); border-radius: 14px; padding: 28px 20px; text-align: center; cursor: pointer; transition: all .2s; background: var(--parchment); position: relative; }
    .file-zone:hover { border-color: var(--rose); background: #FFF5F7; }
    .file-zone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
    .btn-submit { padding: 13px 32px; background: linear-gradient(135deg, var(--rose), var(--rose-dark)); color: #fff; border: none; border-radius: 12px; font-family: 'Cabinet Grotesk', sans-serif; font-size: 15px; font-weight: 700; cursor: pointer; transition: all .2s; box-shadow: 0 4px 14px var(--rose-glow); }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--rose-glow); }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 18px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-soft); border-bottom: 2px solid var(--border-light); white-space: nowrap; }
    td { padding: 16px 18px; border-bottom: 1px solid var(--border-light); font-size: 14px; vertical-align: middle; }
    tbody tr:hover { background: #FFF8F5; }
    .status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status-active { background: #EAF8F4; color: #2E7D62; }
    .status-pending { background: #FFF8E0; color: #B8860B; }
    .status-inactive { background: #FFF0F3; color: var(--rose-dark); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .verification-banner { background: linear-gradient(135deg, #FFF8E0, #FFE4A0); border: 2px solid #F0D060; border-radius: 16px; padding: 20px 28px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; }
    .verification-banner i { font-size: 24px; color: #B8860B; }
    .verification-banner h4 { font-size: 16px; font-weight: 700; color: #8B6914; margin-bottom: 4px; }
    .verification-banner p { font-size: 13px; color: #A08020; }
    @media (max-width: 1024px) { .sidebar { display: none; } .main { margin-left: 0; } .stats-grid { grid-template-columns: 1fr 1fr; } }
  </style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-dot">🏭</div>
    <div class="wordmark">Seller Center</div>
  </div>
  <div class="sidebar-label">Main</div>
  <a href="manufacturer_dashboard.php?tab=dashboard" class="nav-item <?= $active_tab == 'dashboard' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="manufacturer_dashboard.php?tab=products" class="nav-item <?= $active_tab == 'products' ? 'active' : '' ?>"><i class="fas fa-box"></i> My Products</a>
  <a href="manufacturer_dashboard.php?tab=orders" class="nav-item <?= $active_tab == 'orders' ? 'active' : '' ?>"><i class="fas fa-shopping-bag"></i> Orders</a>
  <div class="sidebar-label">Account</div>
  <a href="index.php" class="nav-item"><i class="fas fa-store"></i> View Shop</a>
  <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
  <div class="sidebar-footer">
    <div class="admin-chip">
      <div class="admin-avatar">🏭</div>
      <div>
        <div class="admin-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Seller') ?></div>
        <div class="admin-role">Verified Manufacturer</div>
      </div>
    </div>
  </div>
</aside>

<div class="main">
  <header class="topbar">
    <div class="topbar-title"><?= $active_tab == 'dashboard' ? 'Dashboard' : ($active_tab == 'products' ? 'My Products' : 'Orders') ?> 🏭</div>
    <div class="topbar-right">
      <a href="index.php" style="font-size:13px;font-weight:700;color:var(--text-soft);text-decoration:none;padding:8px 16px;border:2px solid var(--border-light);border-radius:10px;"><i class="fas fa-store"></i> View Store</a>
      <?php if ($active_tab == 'products'): ?>
      <button class="btn-primary" onclick="document.getElementById('add-product-section').scrollIntoView({behavior:'smooth'})"><i class="fas fa-plus"></i> Add Product</button>
      <?php endif; ?>
    </div>
  </header>

  <div class="page-body">
    <?php if ($stats['verification_status'] !== 'verified'): ?>
    <div class="verification-banner">
      <i class="fas fa-clock"></i>
      <div>
        <h4>Account Under Review</h4>
        <p>Your manufacturer account is being verified by our team. You'll be able to list products once approved. This usually takes 1-2 business days.</p>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- DASHBOARD -->
    <div class="tab-content <?= $active_tab == 'dashboard' ? 'active' : '' ?>">
      <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon pink">📦</div><div><div class="stat-value"><?= $total_products ?></div><div class="stat-label">Total Products</div></div></div>
        <div class="stat-card"><div class="stat-icon teal">✅</div><div><div class="stat-value"><?= $active_products ?></div><div class="stat-label">Active Products</div></div></div>
        <div class="stat-card"><div class="stat-icon gold">🛒</div><div><div class="stat-value"><?= $total_sales ?></div><div class="stat-label">Total Sales</div></div></div>
        <div class="stat-card"><div class="stat-icon purple">💰</div><div><div class="stat-value">Tsh<?= number_format($total_revenue, 0) ?></div><div class="stat-label">Total Revenue</div></div></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> Quick Actions</div></div>
        <div class="card-body">
          <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
            <a href="manufacturer_dashboard.php?tab=products" class="btn-primary" style="text-align:center; justify-content:center;"><i class="fas fa-plus"></i> Add Product</a>
            <a href="manufacturer_dashboard.php?tab=orders" class="btn-primary" style="text-align:center; justify-content:center; background: linear-gradient(135deg, var(--gold), #D4A030);"><i class="fas fa-shopping-bag"></i> View Orders</a>
          </div>
        </div>
      </div>
    </div>

    <!-- PRODUCTS -->
    <div class="tab-content <?= $active_tab == 'products' ? 'active' : '' ?>">
      <div class="card" id="add-product-section">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> Add New Product</div></div>
        <div class="card-body">
          <form method="POST" action="manufacturer_dashboard.php?tab=products" enctype="multipart/form-data">
            <div class="form-grid">
              <div class="form-group"><label>Product Name</label><input type="text" name="product_name" class="form-control" placeholder="e.g. Organic Cotton Baby Blanket" required/></div>
              <div class="form-group"><label>Category</label><select name="category_id" class="form-control" required><option value="">Select category...</option><?php mysqli_data_seek($categories, 0); while ($cat = mysqli_fetch_assoc($categories)): ?><option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option><?php endwhile; ?></select></div>
              <div class="form-group"><label>Price (Tsh)</label><input type="number" name="price" class="form-control" step="0.01" placeholder="24999" required/></div>
              <div class="form-group"><label>Old Price (Tsh) <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label><input type="number" name="old_price" class="form-control" step="0.01" placeholder="34999"/></div>
              <div class="form-group"><label>Stock Quantity</label><input type="number" name="stock" class="form-control" placeholder="10" value="10"/></div>
              <div class="form-group"><label>Badge</label><select name="badge" class="form-control"><option value="">No badge</option><option value="HOT">🔥 HOT</option><option value="NEW">✨ NEW</option><option value="SALE">🏷️ SALE</option></select></div>
              <div class="form-group"><label>Condition</label><select name="condition_type" class="form-control"><option value="new">Brand New</option><option value="used">Pre-owned</option><option value="refurbished">Refurbished</option></select></div>
              <div class="form-group"><label>Age Range</label><select name="age_range" class="form-control"><option value="0-12 Months">0 – 12 Months</option><option value="1-3 Years">1 – 3 Years</option><option value="3-5 Years">3 – 5 Years</option><option value="5+ Years">5+ Years</option></select></div>
              <div class="form-group"><label>Shipping Cost (Tsh)</label><input type="number" name="shipping_cost" class="form-control" step="0.01" placeholder="0" value="0"/></div>
              <div class="form-group"><label>Estimated Delivery</label><input type="text" name="estimated_delivery" class="form-control" placeholder="7-14 days" value="7-14 days"/></div>
              <div class="form-group full"><label>Description</label><textarea name="description" class="form-control" placeholder="Describe your product in detail..."></textarea></div>
              <div class="form-group full">
                <label>Product Image</label>
                <div class="file-zone" onclick="document.getElementById('productImg').click()">
                  <input type="file" name="product_image" id="productImg" accept="image/*" onchange="previewImg(this)"/>
                  <div style="font-size:32px;margin-bottom:8px;">📸</div>
                  <div style="font-size:14px;font-weight:600;color:var(--text-mid);">Click to upload image</div>
                  <div style="font-size:12px;color:var(--text-soft);margin-top:4px;">JPG, PNG, WEBP — max 5 MB</div>
                </div>
                <div class="preview-box" id="previewBox" style="margin-top:14px;display:none;"><img id="previewImg" src="" alt="Preview" style="max-width:160px;border-radius:12px;border:2px solid var(--border-light);"/></div>
              </div>
            </div>
            <div style="display:flex; gap:12px; margin-top:20px; align-items:center;">
              <button type="submit" name="add_product" class="btn-submit"><i class="fas fa-plus"></i> Submit for Review</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> My Products</div></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Sales</th><th>Actions</th></tr></thead>
            <tbody>
              <?php while ($p = mysqli_fetch_assoc($products)): ?>
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:52px;height:52px;border-radius:12px;background:linear-gradient(135deg, #FFE4EC, #FFD0DC);display:flex;align-items:center;justify-content:center;font-size:24px;overflow:hidden;">
                      <?php if (!empty($p['image_url']) && file_exists($upload_dir . $p['image_url'])): ?><img src="<?= $upload_dir . htmlspecialchars($p['image_url']) ?>" style="width:100%;height:100%;object-fit:cover;"/><?php else: ?>🧸<?php endif; ?>
                    </div>
                    <div><div style="font-weight:700;font-size:14px;"><?= htmlspecialchars($p['name']) ?></div><div style="font-size:12px;color:var(--text-soft);">#<?= $p['id'] ?></div></div>
                  </div>
                </td>
                <td><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
                <td><div style="font-weight:800;color:var(--rose-dark);">Tsh<?= number_format($p['price'], 0) ?></div></td>
                <td><?= $p['stock'] ?></td>
                <td><span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst(str_replace('_', ' ', $p['status'])) ?></span></td>
                <td><?= $p['orders_count'] ?></td>
                <td><a href="manufacturer_dashboard.php?tab=products&delete=<?= $p['id'] ?>" onclick="return confirm('Delete this product?')" style="width:34px;height:34px;border-radius:9px;border:none;cursor:pointer;font-size:14px;display:inline-flex;align-items:center;justify-content:center;background:#FFF0F3;color:var(--rose-dark);text-decoration:none;"><i class="fas fa-trash"></i></a></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ORDERS -->
    <div class="tab-content <?= $active_tab == 'orders' ? 'active' : '' ?>">
      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> Customer Orders</div></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Order #</th><th>Customer</th><th>Product</th><th>Qty</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
              <?php while ($o = mysqli_fetch_assoc($orders)): 
                $status_class = match(strtolower($o['status'])) { 'pending' => 'status-pending', 'processing' => 'status-pending', 'shipped' => 'status-active', 'completed' => 'status-active', 'cancelled' => 'status-inactive', default => 'status-pending' };
              ?>
              <tr>
                <td><span style="font-weight:800;color:var(--rose-dark);">#<?= $o['id'] ?></span></td>
                <td><div style="font-weight:600;"><?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?></div><div style="font-size:12px;color:var(--text-soft);"><?= htmlspecialchars($o['email']) ?></div></td>
                <td><?= htmlspecialchars($o['product_name']) ?></td>
                <td><?= $o['quantity'] ?></td>
                <td><span style="font-weight:700;color:var(--rose-dark);">Tsh<?= number_format($o['unit_price'] * $o['quantity'], 0) ?></span></td>
                <td><span class="status-badge <?= $status_class ?>"><?= ucfirst($o['status']) ?></span></td>
                <td><?= date('M j, Y', strtotime($o['ordered_at'])) ?></td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function previewImg(input) {
    const box = document.getElementById('previewBox'), img = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => { img.src = e.target.result; box.style.display = 'block'; };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
</body>
</html>