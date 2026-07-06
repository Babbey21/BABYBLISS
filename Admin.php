<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = "";
$error = "";
$active_tab = $_GET['tab'] ?? 'dashboard';

// ─── APPROVE/REJECT MANUFACTURER ───
if (isset($_GET['approve_manufacturer'])) {
    $mid = intval($_GET['approve_manufacturer']);
    mysqli_query($conn, "UPDATE manufacturers SET verification_status = 'verified' WHERE id = $mid");
    $success = "Manufacturer approved successfully!";
    header("Location: admin.php?tab=manufacturers"); exit();
}
if (isset($_GET['reject_manufacturer'])) {
    $mid = intval($_GET['reject_manufacturer']);
    mysqli_query($conn, "UPDATE manufacturers SET verification_status = 'rejected' WHERE id = $mid");
    $success = "Manufacturer rejected.";
    header("Location: admin.php?tab=manufacturers"); exit();
}

// ─── APPROVE/REJECT PRODUCT ───
if (isset($_GET['approve_product'])) {
    $pid = intval($_GET['approve_product']);
    mysqli_query($conn, "UPDATE products SET status = 'active' WHERE id = $pid");
    $success = "Product approved and is now live!";
    header("Location: admin.php?tab=products"); exit();
}
if (isset($_GET['reject_product'])) {
    $pid = intval($_GET['reject_product']);
    mysqli_query($conn, "UPDATE products SET status = 'inactive' WHERE id = $pid");
    $success = "Product rejected.";
    header("Location: admin.php?tab=products"); exit();
}

// ─── UPDATE ORDER STATUS ───
if (isset($_POST['update_order_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $order_id");

    // If delivered, release escrow payment
    if ($new_status == 'delivered') {
        mysqli_query($conn, "UPDATE payments SET status = 'released', released_at = NOW() WHERE order_id = $order_id AND status = 'held_in_escrow'");
    }

    $success = "Order #$order_id status updated to $new_status!";
    header("Location: admin.php?tab=orders"); exit();
}

// ─── RESOLVE DISPUTE ───
if (isset($_POST['resolve_dispute'])) {
    $did = intval($_POST['dispute_id']);
    $resolution = mysqli_real_escape_string($conn, $_POST['resolution']);
    $refund_amount = floatval($_POST['refund_amount'] ?? 0);
    $status = mysqli_real_escape_string($conn, $_POST['dispute_status']);

    mysqli_query($conn, "UPDATE disputes SET status = '$status', resolution = '$resolution', refund_amount = $refund_amount, resolved_at = NOW() WHERE id = $did");

    if ($refund_amount > 0) {
        $order_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT order_id FROM disputes WHERE id = $did"))['order_id'];
        mysqli_query($conn, "UPDATE payments SET status = 'refunded' WHERE order_id = $order_id");
    }

    $success = "Dispute #$did resolved!";
    header("Location: admin.php?tab=disputes"); exit();
}

// ─── UPDATE PLATFORM SETTINGS ───
if (isset($_POST['update_settings'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $val = mysqli_real_escape_string($conn, $value);
        mysqli_query($conn, "UPDATE platform_settings SET setting_value = '$val' WHERE setting_key = '$key'");
    }
    $success = "Settings updated!";
    header("Location: admin.php?tab=settings"); exit();
}

// ─── STATISTICS ───
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'customer'"))['count'];
$total_manufacturers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM manufacturers"))['count'];
$verified_manufacturers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM manufacturers WHERE verification_status = 'verified'"))['count'];
$pending_manufacturers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM manufacturers WHERE verification_status = 'pending'"))['count'];
$total_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"))['count'];
$pending_products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE status = 'pending_review'"))['count'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status != 'cancelled'"))['total'];
$total_disputes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM disputes WHERE status = 'open' OR status = 'under_review'"))['count'];

// ─── FETCH DATA ───
$manufacturers = mysqli_query($conn, "SELECT m.*, u.email, u.first_name, u.last_name, u.phone FROM manufacturers m JOIN users u ON m.user_id = u.id ORDER BY m.created_at DESC");
$products = mysqli_query($conn, "SELECT p.*, m.company_name, c.name as category_name FROM products p LEFT JOIN manufacturers m ON p.manufacturer_id = m.id LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$orders = mysqli_query($conn, "SELECT o.*, u.first_name, u.last_name, u.email, a.street, a.city, a.country, p.method as payment_method, p.status as payment_status FROM orders o LEFT JOIN users u ON o.user_id = u.id LEFT JOIN addresses a ON o.address_id = a.id LEFT JOIN payments p ON o.id = p.order_id ORDER BY o.ordered_at DESC");
$disputes = mysqli_query($conn, "SELECT d.*, o.total_amount, u.first_name, u.last_name, m.company_name FROM disputes d JOIN orders o ON d.order_id = o.id JOIN users u ON d.user_id = u.id JOIN manufacturers m ON d.manufacturer_id = m.id ORDER BY d.created_at DESC");
$settings = mysqli_query($conn, "SELECT * FROM platform_settings ORDER BY setting_key ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - BabyBliss Marketplace</title>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Cabinet+Grotesk:wght@400;500;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root { --rose: #E8738A; --rose-dark: #C44D65; --rose-glow: rgba(232,115,138,.18); --mint: #5FB8A0; --mint-soft: rgba(95,184,160,.12); --gold: #F0C060; --gold-soft: rgba(240,192,96,.14); --ink: #1A0A0E; --parchment: #FFF6EE; --parchment-2:#FFF0E6; --silk: #FFFFFF; --mist: rgba(255,255,255,.06); --border: rgba(232,115,138,.15); --border-light: #F5E6DF; --text: #1A0A0E; --text-mid: #6B3A4A; --text-soft: #A07080; --r: 16px; --shadow: 0 4px 32px rgba(30,8,16,.08); --shadow-lg: 0 12px 48px rgba(30,8,16,.12); }
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
    .admin-avatar { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, var(--rose), var(--rose-dark)); display: flex; align-items: center; justify-content: center; font-size: 16px; }
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
    .stat-icon.red { background: linear-gradient(135deg, #FFE0E0, #FFB0B0); }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-label { font-size: 13px; color: var(--text-soft); margin-top: 4px; font-weight: 500; }
    .card { background: var(--silk); border-radius: 20px; box-shadow: var(--shadow); overflow: hidden; margin-bottom: 28px; }
    .card-header { padding: 22px 28px; border-bottom: 1px solid var(--border-light); display: flex; align-items: center; justify-content: space-between; }
    .card-title { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 700; color: var(--text); display: flex; align-items: center; gap: 10px; }
    .card-title .title-dot { width: 8px; height: 8px; border-radius: 50%; background: linear-gradient(135deg, var(--rose), var(--rose-dark)); }
    .card-body { padding: 28px; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 18px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-soft); border-bottom: 2px solid var(--border-light); white-space: nowrap; }
    td { padding: 16px 18px; border-bottom: 1px solid var(--border-light); font-size: 14px; vertical-align: middle; }
    tbody tr:hover { background: #FFF8F5; }
    .status-badge { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status-verified { background: #EAF8F4; color: #2E7D62; }
    .status-pending { background: #FFF8E0; color: #B8860B; }
    .status-rejected { background: #FFF0F3; color: var(--rose-dark); }
    .status-active { background: #EAF8F4; color: #2E7D62; }
    .status-inactive { background: #FFF0F3; color: var(--rose-dark); }
    .action-wrap { display: flex; gap: 8px; }
    .act-btn { width: 34px; height: 34px; border-radius: 9px; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all .2s; }
    .act-approve { background: #EAF8F4; color: var(--mint); }
    .act-approve:hover { background: var(--mint); color: #fff; }
    .act-reject { background: #FFF0F3; color: var(--rose-dark); }
    .act-reject:hover { background: var(--rose-dark); color: #fff; }
    .act-view { background: #E0E0FF; color: #6678CC; }
    .act-view:hover { background: #6678CC; color: #fff; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .dispute-card { background: var(--parchment); border-radius: 12px; padding: 20px; margin-bottom: 16px; border: 2px solid var(--border-light); }
    .dispute-card h4 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
    .dispute-meta { display: flex; gap: 16px; font-size: 13px; color: var(--text-soft); margin-bottom: 12px; }
    .form-control { padding: 12px 16px; border: 2px solid var(--border-light); border-radius: 12px; font-family: 'Cabinet Grotesk', sans-serif; font-size: 14px; color: var(--text); background: var(--parchment); outline: none; transition: border .2s; }
    .form-control:focus { border-color: var(--rose); box-shadow: 0 0 0 4px rgba(232,115,138,.1); background: var(--silk); }
    select.form-control { appearance: none; cursor: pointer; }
    textarea.form-control { resize: vertical; min-height: 80px; }
    .btn-submit { padding: 12px 28px; background: linear-gradient(135deg, var(--rose), var(--rose-dark)); color: #fff; border: none; border-radius: 12px; font-family: 'Cabinet Grotesk', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .2s; }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px var(--rose-glow); }
    .settings-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .settings-group { display: flex; flex-direction: column; gap: 8px; }
    .settings-group label { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--text-mid); }
    .settings-group small { font-size: 12px; color: var(--text-soft); }
    @media (max-width: 1024px) { .sidebar { display: none; } .main { margin-left: 0; } .stats-grid { grid-template-columns: 1fr 1fr; } .settings-row { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-dot">👑</div>
    <div class="wordmark">Admin Panel</div>
  </div>
  <div class="sidebar-label">Main</div>
  <a href="admin.php?tab=dashboard" class="nav-item <?= $active_tab == 'dashboard' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="admin.php?tab=manufacturers" class="nav-item <?= $active_tab == 'manufacturers' ? 'active' : '' ?>"><i class="fas fa-industry"></i> Manufacturers <?= $pending_manufacturers > 0 ? '<span style="background:var(--rose);color:white;padding:2px 8px;border-radius:10px;font-size:10px;margin-left:auto;">' . $pending_manufacturers . '</span>' : '' ?></a>
  <a href="admin.php?tab=products" class="nav-item <?= $active_tab == 'products' ? 'active' : '' ?>"><i class="fas fa-box"></i> Products <?= $pending_products > 0 ? '<span style="background:var(--rose);color:white;padding:2px 8px;border-radius:10px;font-size:10px;margin-left:auto;">' . $pending_products . '</span>' : '' ?></a>
  <a href="admin.php?tab=orders" class="nav-item <?= $active_tab == 'orders' ? 'active' : '' ?>"><i class="fas fa-shopping-bag"></i> Orders</a>
  <a href="admin.php?tab=disputes" class="nav-item <?= $active_tab == 'disputes' ? 'active' : '' ?>"><i class="fas fa-gavel"></i> Disputes <?= $total_disputes > 0 ? '<span style="background:var(--rose);color:white;padding:2px 8px;border-radius:10px;font-size:10px;margin-left:auto;">' . $total_disputes . '</span>' : '' ?></a>
  <a href="admin.php?tab=settings" class="nav-item <?= $active_tab == 'settings' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Settings</a>
  <div class="sidebar-label">Account</div>
  <a href="index.php" class="nav-item"><i class="fas fa-store"></i> View Shop</a>
  <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
  <div class="sidebar-footer">
    <div class="admin-chip">
      <div class="admin-avatar">👑</div>
      <div><div class="admin-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></div><div class="admin-role">Platform Administrator</div></div>
    </div>
  </div>
</aside>

<div class="main">
  <header class="topbar">
    <div class="topbar-title"><?= ucfirst($active_tab) ?> 👑</div>
    <div class="topbar-right">
      <a href="index.php" style="font-size:13px;font-weight:700;color:var(--text-soft);text-decoration:none;padding:8px 16px;border:2px solid var(--border-light);border-radius:10px;"><i class="fas fa-store"></i> View Store</a>
    </div>
  </header>

  <div class="page-body">
    <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- DASHBOARD -->
    <div class="tab-content <?= $active_tab == 'dashboard' ? 'active' : '' ?>">
      <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon pink">👥</div><div><div class="stat-value"><?= $total_users ?></div><div class="stat-label">Customers</div></div></div>
        <div class="stat-card"><div class="stat-icon teal">🏭</div><div><div class="stat-value"><?= $verified_manufacturers ?>/<?= $total_manufacturers ?></div><div class="stat-label">Verified Manufacturers</div></div></div>
        <div class="stat-card"><div class="stat-icon gold">📦</div><div><div class="stat-value"><?= $total_products ?></div><div class="stat-label">Total Products</div></div></div>
        <div class="stat-card"><div class="stat-icon purple">💰</div><div><div class="stat-value">Tsh<?= number_format($total_revenue, 0) ?></div><div class="stat-label">Total Revenue</div></div></div>
      </div>
      <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon red">⏳</div><div><div class="stat-value"><?= $pending_manufacturers ?></div><div class="stat-label">Pending Manufacturers</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg, #FFE4C0, #FFD0A0);">🔍</div><div><div class="stat-value"><?= $pending_products ?></div><div class="stat-label">Pending Review</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg, #E0E0FF, #D0D0FF);">🛒</div><div><div class="stat-value"><?= $total_orders ?></div><div class="stat-label">Total Orders</div></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:linear-gradient(135deg, #FFC0CB, #FFB0C0);">⚖️</div><div><div class="stat-value"><?= $total_disputes ?></div><div class="stat-label">Open Disputes</div></div></div>
      </div>
    </div>

    <!-- MANUFACTURERS -->
    <div class="tab-content <?= $active_tab == 'manufacturers' ? 'active' : '' ?>">
      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> All Manufacturers</div></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Company</th><th>Contact</th><th>Country</th><th>Status</th><th>Sales</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
              <?php while ($m = mysqli_fetch_assoc($manufacturers)): ?>
              <tr>
                <td><div style="display:flex;align-items:center;gap:12px;"><div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg, #E8E0FF, #D0C4FF);display:flex;align-items:center;justify-content:center;font-size:18px;"><?= !empty($m['logo']) ? '<img src="uploads/products/' . htmlspecialchars($m['logo']) . '" style="width:100%;height:100%;object-fit:cover;border-radius:10px;"/>' : '🏭' ?></div><div><div style="font-weight:700;"><?= htmlspecialchars($m['company_name']) ?></div><div style="font-size:12px;color:var(--text-soft);"><?= htmlspecialchars($m['company_description'] ?? 'No description') ?></div></div></div></td>
                <td><div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name']) ?></div><div style="font-size:12px;color:var(--text-soft);"><?= htmlspecialchars($m['email']) ?></div></td>
                <td><?= htmlspecialchars($m['country'] ?? 'N/A') ?></td>
                <td><span class="status-badge status-<?= $m['verification_status'] ?>"><?= ucfirst($m['verification_status']) ?></span></td>
                <td><?= number_format($m['total_sales']) ?></td>
                <td><?= date('M Y', strtotime($m['created_at'])) ?></td>
                <td>
                  <div class="action-wrap">
                    <?php if ($m['verification_status'] == 'pending'): ?>
                    <a href="admin.php?tab=manufacturers&approve_manufacturer=<?= $m['id'] ?>" class="act-btn act-approve" title="Approve"><i class="fas fa-check"></i></a>
                    <a href="admin.php?tab=manufacturers&reject_manufacturer=<?= $m['id'] ?>" class="act-btn act-reject" title="Reject" onclick="return confirm('Reject this manufacturer?')"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- PRODUCTS -->
    <div class="tab-content <?= $active_tab == 'products' ? 'active' : '' ?>">
      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> All Products</div></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Product</th><th>Manufacturer</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
              <?php while ($p = mysqli_fetch_assoc($products)): ?>
              <tr>
                <td><div style="display:flex;align-items:center;gap:12px;"><div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg, #FFE4EC, #FFD0DC);display:flex;align-items:center;justify-content:center;font-size:18px;overflow:hidden;"><?= !empty($p['image_url']) && file_exists('uploads/products/' . $p['image_url']) ? '<img src="uploads/products/' . htmlspecialchars($p['image_url']) . '" style="width:100%;height:100%;object-fit:cover;"/>' : '🧸' ?></div><div><div style="font-weight:700;"><?= htmlspecialchars($p['name']) ?></div><div style="font-size:12px;color:var(--text-soft);">Stock: <?= $p['stock'] ?></div></div></div></td>
                <td><?= htmlspecialchars($p['company_name'] ?? 'Unknown') ?></td>
                <td><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
                <td><span style="font-weight:700;color:var(--rose-dark);">Tsh<?= number_format($p['price'], 0) ?></span></td>
                <td><span class="status-badge status-<?= str_replace('_', '-', $p['status']) ?>"><?= ucfirst(str_replace('_', ' ', $p['status'])) ?></span></td>
                <td>
                  <div class="action-wrap">
                    <?php if ($p['status'] == 'pending_review'): ?>
                    <a href="admin.php?tab=products&approve_product=<?= $p['id'] ?>" class="act-btn act-approve" title="Approve"><i class="fas fa-check"></i></a>
                    <a href="admin.php?tab=products&reject_product=<?= $p['id'] ?>" class="act-btn act-reject" title="Reject" onclick="return confirm('Reject this product?')"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                  </div>
                </td>
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
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> All Orders</div></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
              <?php while ($o = mysqli_fetch_assoc($orders)): 
                $status_class = match(strtolower($o['status'])) { 'pending' => 'status-pending', 'processing' => 'status-pending', 'shipped' => 'status-active', 'delivered' => 'status-active', 'completed' => 'status-verified', 'cancelled' => 'status-rejected', 'disputed' => 'status-rejected', default => 'status-pending' };
              ?>
              <tr>
                <td><span style="font-weight:800;color:var(--rose-dark);">#<?= $o['id'] ?></span></td>
                <td><div style="font-weight:600;"><?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?></div><div style="font-size:12px;color:var(--text-soft);"><?= htmlspecialchars($o['email']) ?></div></td>
                <td><span style="font-weight:700;">Tsh<?= number_format($o['total_amount'], 0) ?></span></td>
                <td><span class="status-badge <?= $status_class ?>"><?= ucfirst($o['status']) ?></span></td>
                <td><span class="status-badge status-<?= str_replace('_', '-', $o['payment_status'] ?? 'pending') ?>"><?= ucfirst(str_replace('_', ' ', $o['payment_status'] ?? 'Pending')) ?></span></td>
                <td><?= date('M j, Y', strtotime($o['ordered_at'])) ?></td>
                <td>
                  <form method="POST" action="admin.php?tab=orders" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>"/>
                    <select name="status" class="form-control" style="width:130px;font-size:12px;padding:4px 8px;" onchange="this.form.submit()">
                      <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                      <option value="processing" <?= $o['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                      <option value="shipped" <?= $o['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                      <option value="delivered" <?= $o['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                      <option value="completed" <?= $o['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                      <option value="cancelled" <?= $o['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                    <input type="hidden" name="update_order_status" value="1"/>
                  </form>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- DISPUTES -->
    <div class="tab-content <?= $active_tab == 'disputes' ? 'active' : '' ?>">
      <?php while ($d = mysqli_fetch_assoc($disputes)): ?>
      <div class="dispute-card">
        <h4><i class="fas fa-gavel" style="color:var(--rose);margin-right:8px;"></i>Dispute #<?= $d['id'] ?> — <?= ucfirst(str_replace('_', ' ', $d['type'])) ?></h4>
        <div class="dispute-meta">
          <span><i class="fas fa-user"></i> Buyer: <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?></span>
          <span><i class="fas fa-industry"></i> Seller: <?= htmlspecialchars($d['company_name']) ?></span>
          <span><i class="fas fa-shopping-bag"></i> Order #<?= $d['order_id'] ?></span>
          <span><i class="fas fa-money-bill"></i> Tsh<?= number_format($d['total_amount'], 0) ?></span>
          <span class="status-badge status-<?= str_replace('_', '-', $d['status']) ?>"><?= ucfirst(str_replace('_', ' ', $d['status'])) ?></span>
        </div>
        <p style="font-size:14px;color:var(--text-mid);margin-bottom:16px;line-height:1.6;"><?= htmlspecialchars($d['description']) ?></p>

        <?php if ($d['status'] == 'open' || $d['status'] == 'under_review'): ?>
        <form method="POST" action="admin.php?tab=disputes" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;align-items:end;">
          <input type="hidden" name="dispute_id" value="<?= $d['id'] ?>"/>
          <div>
            <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-mid);margin-bottom:6px;display:block;">Resolution</label>
            <select name="dispute_status" class="form-control" required>
              <option value="resolved_buyer">Resolved in Buyer's Favor</option>
              <option value="resolved_seller">Resolved in Seller's Favor</option>
              <option value="closed">Closed (No Action)</option>
            </select>
          </div>
          <div>
            <label style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-mid);margin-bottom:6px;display:block;">Refund Amount (Tsh)</label>
            <input type="number" name="refund_amount" class="form-control" step="0.01" placeholder="0.00" value="0"/>
          </div>
          <button type="submit" name="resolve_dispute" class="btn-submit"><i class="fas fa-gavel"></i> Resolve</button>
          <div class="full" style="grid-column:1/-1;">
            <textarea name="resolution" class="form-control" placeholder="Enter resolution details..." required></textarea>
          </div>
        </form>
        <?php else: ?>
        <div style="background:var(--silk);padding:16px;border-radius:10px;">
          <strong style="font-size:13px;color:var(--text-mid);">Resolution:</strong>
          <p style="font-size:14px;color:var(--text-soft);margin-top:4px;"><?= htmlspecialchars($d['resolution'] ?? 'No details provided') ?></p>
          <?php if ($d['refund_amount'] > 0): ?><p style="font-size:13px;color:var(--rose-dark);margin-top:8px;"><i class="fas fa-undo"></i> Refund issued: Tsh<?= number_format($d['refund_amount'], 0) ?></p><?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endwhile; ?>
    </div>

    <!-- SETTINGS -->
    <div class="tab-content <?= $active_tab == 'settings' ? 'active' : '' ?>">
      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> Platform Settings</div></div>
        <div class="card-body">
          <form method="POST" action="admin.php?tab=settings">
            <div class="settings-row">
              <?php while ($s = mysqli_fetch_assoc($settings)): ?>
              <div class="settings-group">
                <label><?= ucfirst(str_replace('_', ' ', $s['setting_key'])) ?></label>
                <input type="text" name="settings[<?= $s['setting_key'] ?>]" class="form-control" value="<?= htmlspecialchars($s['setting_value']) ?>" required/>
                <small><?= htmlspecialchars($s['description']) ?></small>
              </div>
              <?php endwhile; ?>
            </div>
            <button type="submit" name="update_settings" class="btn-submit"><i class="fas fa-save"></i> Save Settings</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>