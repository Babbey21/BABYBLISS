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

// ─── ADD CATEGORY ───
if (isset($_POST['add_category'])) {
    $cat_name = mysqli_real_escape_string($conn, $_POST['cat_name']);
    $cat_icon = mysqli_real_escape_string($conn, $_POST['cat_icon']);
    $cat_desc = mysqli_real_escape_string($conn, $_POST['cat_description']);

    if (empty($cat_name)) {
        $error = "Category name is required!";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM categories WHERE name = '$cat_name'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Category already exists!";
        } else {
            $sql = "INSERT INTO categories (name, icon, description) VALUES ('$cat_name', '$cat_icon', '$cat_desc')";
            if (mysqli_query($conn, $sql)) $success = "Category added successfully! 🎉";
            else $error = "Error: " . mysqli_error($conn);
        }
    }
}

// ─── DELETE CATEGORY ───
if (isset($_GET['delete_category'])) {
    $cid = intval($_GET['delete_category']);
    // Check if category has products
    $prod_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE category_id = $cid"))['count'];
    if ($prod_check > 0) {
        $error = "Cannot delete! This category has $prod_check product(s). Move products first.";
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE id = $cid");
        $success = "Category deleted.";
    }
    header("Location: admin.php?tab=categories"); exit();
}

// ─── ADD MANUFACTURER ───
if (isset($_POST['add_manufacturer'])) {
    $m_name = mysqli_real_escape_string($conn, $_POST['m_name']);
    $m_country = mysqli_real_escape_string($conn, $_POST['m_country']);
    $m_desc = mysqli_real_escape_string($conn, $_POST['m_description']);
    $m_website = mysqli_real_escape_string($conn, $_POST['m_website']);
    $m_logo = "";

    if (isset($_FILES['m_logo']) && $_FILES['m_logo']['error'] == 0) {
        $allowed = ['jpg','jpeg','png','gif','webp'];
        $filename = $_FILES['m_logo']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($filetype, $allowed)) {
            $m_logo = 'manu_' . uniqid() . '_' . basename($filename);
            $target = $upload_dir . $m_logo;
            if (!move_uploaded_file($_FILES['m_logo']['tmp_name'], $target))
                $error = "Failed to upload manufacturer logo.";
        }
    }

    if (empty($error)) {
        $sql = "INSERT INTO manufacturers (name, country, description, website, logo) 
                VALUES ('$m_name', '$m_country', '$m_desc', '$m_website', '$m_logo')";
        if (mysqli_query($conn, $sql)) $success = "Manufacturer added successfully! 🎉";
        else $error = "Error: " . mysqli_error($conn);
    }
}

// ─── DELETE MANUFACTURER ───
if (isset($_GET['delete_manufacturer'])) {
    $mid = intval($_GET['delete_manufacturer']);
    $img_res = mysqli_query($conn, "SELECT logo FROM manufacturers WHERE id = $mid");
    $img_data = mysqli_fetch_assoc($img_res);
    if ($img_data && !empty($img_data['logo']) && file_exists($upload_dir . $img_data['logo']))
        unlink($upload_dir . $img_data['logo']);
    mysqli_query($conn, "UPDATE products SET manufacturer_id = NULL WHERE manufacturer_id = $mid");
    mysqli_query($conn, "DELETE FROM manufacturers WHERE id = $mid");
    $success = "Manufacturer deleted.";
    header("Location: admin.php?tab=manufacturers"); exit();
}

// ─── ADD PRODUCT ───
if (isset($_POST['add_product'])) {
    $name        = mysqli_real_escape_string($conn, $_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $price       = floatval($_POST['price']);
    $old_price   = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : 0;
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $badge       = mysqli_real_escape_string($conn, $_POST['badge']);
    $age_range   = mysqli_real_escape_string($conn, $_POST['age_range']);
    $manufacturer_id = !empty($_POST['manufacturer_id']) ? intval($_POST['manufacturer_id']) : 'NULL';
    $stock       = intval($_POST['stock'] ?? 0);
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
        $sql = "INSERT INTO products (name, category_id, price, old_price, description, badge, image_url, age_range, stock, manufacturer_id, created_at)
                VALUES ('$name', $category_id, $price, $old_price, '$description', '$badge', '$image_name', '$age_range', $stock, $manufacturer_id, NOW())";
        if (mysqli_query($conn, $sql)) $success = "Product added successfully! 🎉";
        else $error = "Error: " . mysqli_error($conn);
    }
}

// ─── DELETE PRODUCT ───
if (isset($_GET['delete'])) {
    $id         = intval($_GET['delete']);
    $img_result = mysqli_query($conn, "SELECT image_url FROM products WHERE id = $id");
    $img_data   = mysqli_fetch_assoc($img_result);
    if ($img_data && !empty($img_data['image_url']) && file_exists($upload_dir . $img_data['image_url']))
        unlink($upload_dir . $img_data['image_url']);
    if (mysqli_query($conn, "DELETE FROM products WHERE id = $id")) $success = "Product deleted.";
    else $error = "Error deleting product!";
    header("Location: admin.php?tab=products"); exit();
}

// Fetch products with manufacturer info
$products_query = "SELECT p.*, m.name as manufacturer_name, m.country as manufacturer_country, m.logo as manufacturer_logo, c.name as category_name 
                   FROM products p 
                   LEFT JOIN manufacturers m ON p.manufacturer_id = m.id 
                   LEFT JOIN categories c ON p.category_id = c.id
                   ORDER BY p.created_at DESC";
$products = mysqli_query($conn, $products_query);
$total_products = mysqli_num_rows($products);

// Get counts
$total_users    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"))['count'] ?? 0;

// Manufacturers data
$manufacturers = mysqli_query($conn, "SELECT * FROM manufacturers ORDER BY name ASC");
$total_manufacturers = mysqli_num_rows($manufacturers);

// Categories data
$categories = mysqli_query($conn, "SELECT c.*, COUNT(p.id) as product_count 
                                     FROM categories c 
                                     LEFT JOIN products p ON c.id = p.category_id 
                                     GROUP BY c.id 
                                     ORDER BY c.name ASC");
$total_categories = mysqli_num_rows($categories);

// Categories for dropdown (products form)
$categories_dropdown = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// Get active tab
$active_tab = $_GET['tab'] ?? 'dashboard';
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
    :root {
      --rose:      #E8738A;
      --rose-dark: #C44D65;
      --rose-glow: rgba(232,115,138,.18);
      --mint:      #5FB8A0;
      --mint-soft: rgba(95,184,160,.12);
      --gold:      #F0C060;
      --gold-soft: rgba(240,192,96,.14);
      --ink:       #1A0A0E;
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
    *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
    html { font-size:16px; }
    body {
      font-family: 'Cabinet Grotesk', sans-serif;
      background: var(--parchment);
      color: var(--text);
      min-height: 100vh;
      display: flex;
    }
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
    .main {
      margin-left: 256px;
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
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
    .alert {
      display: flex; align-items: center; gap: 12px;
      padding: 14px 20px; border-radius: var(--r);
      font-size: 14px; font-weight: 600; margin-bottom: 24px;
      animation: slideDown .3s ease;
    }
    @keyframes slideDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
    .alert-success { background: #EAF8F4; border: 1px solid #B0E0D0; color: #2E7D62; }
    .alert-error   { background: #FFF0F3; border: 1px solid #F5B8C8; color: var(--rose-dark); }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
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
    .stat-card:nth-child(4)::after { background: #8B7FD4; }
    .stat-icon {
      width: 52px; height: 52px; border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; flex-shrink: 0;
    }
    .stat-icon.pink { background: linear-gradient(135deg, #FFE4EC, #FFB8CC); }
    .stat-icon.teal { background: linear-gradient(135deg, #D4F5EC, #A8E0D0); }
    .stat-icon.gold { background: linear-gradient(135deg, #FFF0C0, #FFE080); }
    .stat-icon.purple { background: linear-gradient(135deg, #E8E0FF, #D0C4FF); }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text); line-height: 1; }
    .stat-label { font-size: 13px; color: var(--text-soft); margin-top: 4px; font-weight: 500; }
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
    .manu-cell { display: flex; align-items: center; gap: 10px; }
    .manu-logo {
      width: 32px; height: 32px; border-radius: 8px;
      background: linear-gradient(135deg, #E8E0FF, #D0C4FF);
      display: flex; align-items: center; justify-content: center;
      font-size: 14px; overflow: hidden;
    }
    .manu-logo img { width: 100%; height: 100%; object-fit: cover; }
    .manu-name { font-weight: 600; font-size: 13px; }
    .manu-country { font-size: 11px; color: var(--text-soft); }
    .manu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
    }
    .manu-card {
      background: var(--silk);
      border-radius: 16px;
      padding: 24px;
      border: 2px solid var(--border-light);
      transition: all .25s;
      position: relative;
    }
    .manu-card:hover {
      border-color: var(--rose);
      transform: translateY(-3px);
      box-shadow: var(--shadow-lg);
    }
    .manu-card-header { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
    .manu-card-logo {
      width: 56px; height: 56px; border-radius: 14px;
      background: linear-gradient(135deg, #E8E0FF, #D0C4FF);
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; overflow: hidden; flex-shrink: 0;
    }
    .manu-card-logo img { width: 100%; height: 100%; object-fit: cover; }
    .manu-card-title { font-size: 16px; font-weight: 700; }
    .manu-card-country {
      display: inline-flex; align-items: center; gap: 4px;
      font-size: 12px; color: var(--text-soft); margin-top: 2px;
    }
    .manu-card-desc { font-size: 13px; color: var(--text-mid); line-height: 1.6; margin-bottom: 14px; }
    .manu-card-footer {
      display: flex; align-items: center; justify-content: space-between;
      padding-top: 14px;
      border-top: 1px solid var(--border-light);
    }
    .manu-card-link { font-size: 13px; color: var(--rose); text-decoration: none; font-weight: 600; }
    .manu-card-link:hover { text-decoration: underline; }

    /* Category Cards */
    .cat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 20px;
    }
    .cat-card-admin {
      background: var(--silk);
      border-radius: 16px;
      padding: 24px;
      border: 2px solid var(--border-light);
      transition: all .25s;
      position: relative;
      text-align: center;
    }
    .cat-card-admin:hover {
      border-color: var(--rose);
      transform: translateY(-3px);
      box-shadow: var(--shadow-lg);
    }
    .cat-card-icon {
      width: 64px; height: 64px; border-radius: 16px;
      background: linear-gradient(135deg, #FFE4EC, #FFD0DC);
      display: flex; align-items: center; justify-content: center;
      font-size: 32px; margin: 0 auto 14px;
    }
    .cat-card-title { font-size: 17px; font-weight: 700; margin-bottom: 6px; }
    .cat-card-desc { font-size: 13px; color: var(--text-mid); line-height: 1.5; margin-bottom: 14px; }
    .cat-card-footer {
      display: flex; align-items: center; justify-content: space-between;
      padding-top: 14px;
      border-top: 1px solid var(--border-light);
    }
    .cat-card-count {
      font-size: 13px; color: var(--text-soft); font-weight: 600;
    }
    .cat-card-count i { color: var(--rose); }

    .empty-state { padding: 60px 20px; text-align: center; }
    .empty-state .empty-icon { font-size: 56px; margin-bottom: 14px; opacity: .5; }
    .empty-state p { color: var(--text-soft); font-size: 15px; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
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
      .manu-grid   { grid-template-columns: 1fr; }
      .cat-grid    { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-dot">🍼</div>
    <div class="wordmark">BabyBliss</div>
  </div>
  <div class="sidebar-label">Main</div>
  <a href="admin.php?tab=dashboard" class="nav-item <?= $active_tab == 'dashboard' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="admin.php?tab=products" class="nav-item <?= $active_tab == 'products' ? 'active' : '' ?>"><i class="fas fa-box"></i> Products</a>
  <a href="admin.php?tab=categories" class="nav-item <?= $active_tab == 'categories' ? 'active' : '' ?>"><i class="fas fa-tags"></i> Categories</a>
  <a href="admin.php?tab=manufacturers" class="nav-item <?= $active_tab == 'manufacturers' ? 'active' : '' ?>"><i class="fas fa-industry"></i> Manufacturers</a>
  <div class="sidebar-label">Store</div>
  <a href="index.php" class="nav-item"><i class="fas fa-store"></i> View Shop</a>
  <a href="#" class="nav-item"><i class="fas fa-shopping-bag"></i> Orders</a>
  <a href="#" class="nav-item"><i class="fas fa-users"></i> Customers</a>
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

<div class="main">
  <header class="topbar">
    <div class="topbar-title">
      <?php
      $tab_titles = ['dashboard' => 'Dashboard', 'products' => 'Products', 'categories' => 'Categories', 'manufacturers' => 'Manufacturers'];
      echo $tab_titles[$active_tab] ?? 'Dashboard';
      ?> &nbsp;👋
    </div>
    <div class="topbar-right">
      <a href="index.php" style="font-size:13px;font-weight:700;color:var(--text-soft);text-decoration:none;padding:8px 16px;border:2px solid var(--border-light);border-radius:10px;"><i class="fas fa-store"></i> View Store</a>
      <?php if ($active_tab == 'products'): ?>
      <button class="btn-primary" onclick="document.getElementById('add-product-section').scrollIntoView({behavior:'smooth'})"><i class="fas fa-plus"></i> Add Product</button>
      <?php elseif ($active_tab == 'categories'): ?>
      <button class="btn-primary" onclick="document.getElementById('add-category-section').scrollIntoView({behavior:'smooth'})"><i class="fas fa-plus"></i> Add Category</button>
      <?php elseif ($active_tab == 'manufacturers'): ?>
      <button class="btn-primary" onclick="document.getElementById('add-manufacturer-section').scrollIntoView({behavior:'smooth'})"><i class="fas fa-plus"></i> Add Manufacturer</button>
      <?php endif; ?>
    </div>
  </header>

  <div class="page-body">
    <?php if ($success): ?>
      <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- DASHBOARD -->
    <div class="tab-content <?= $active_tab == 'dashboard' ? 'active' : '' ?>">
      <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon pink">📦</div><div><div class="stat-value"><?= $total_products ?></div><div class="stat-label">Total Products</div></div></div>
        <div class="stat-card"><div class="stat-icon teal">👥</div><div><div class="stat-value"><?= $total_users ?></div><div class="stat-label">Customers</div></div></div>
        <div class="stat-card"><div class="stat-icon gold">🛒</div><div><div class="stat-value"><?= $total_orders ?></div><div class="stat-label">Orders</div></div></div>
        <div class="stat-card"><div class="stat-icon purple">🏭</div><div><div class="stat-value"><?= $total_manufacturers ?></div><div class="stat-label">Manufacturers</div></div></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> Quick Actions</div></div>
        <div class="card-body">
          <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
            <a href="admin.php?tab=products" class="btn-primary" style="text-align:center; justify-content:center;"><i class="fas fa-plus"></i> Add Product</a>
            <a href="admin.php?tab=categories" class="btn-primary" style="text-align:center; justify-content:center; background: linear-gradient(135deg, var(--gold), #D4A030);"><i class="fas fa-tags"></i> Add Category</a>
            <a href="admin.php?tab=manufacturers" class="btn-primary" style="text-align:center; justify-content:center; background: linear-gradient(135deg, #8B7FD4, #6B5DB4);"><i class="fas fa-industry"></i> Add Manufacturer</a>
          </div>
        </div>
      </div>
    </div>

    <!-- PRODUCTS -->
    <div class="tab-content <?= $active_tab == 'products' ? 'active' : '' ?>">
      <div class="card" id="add-product-section">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> Add New Product</div></div>
        <div class="card-body">
          <form method="POST" action="admin.php?tab=products" enctype="multipart/form-data">
            <div class="form-grid">
              <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" class="form-control" placeholder="e.g. Cuddle Bear Plush" required/>
              </div>
              <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control" required>
                  <option value="">Select category…</option>
                  <?php mysqli_data_seek($categories_dropdown, 0); while ($cat = mysqli_fetch_assoc($categories_dropdown)): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Price (Tsh)</label>
                <input type="number" name="price" class="form-control" step="0.01" placeholder="24.99" required/>
              </div>
              <div class="form-group">
                <label>Old Price (Tsh) <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
                <input type="number" name="old_price" class="form-control" step="0.01" placeholder="34.99"/>
              </div>
              <div class="form-group">
                <label>Manufacturer</label>
                <select name="manufacturer_id" class="form-control">
                  <option value="">Select manufacturer…</option>
                  <?php mysqli_data_seek($manufacturers, 0); while ($m = mysqli_fetch_assoc($manufacturers)): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?> <?= $m['country'] ? '(' . htmlspecialchars($m['country']) . ')' : '' ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" placeholder="10" value="10"/>
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
            </div>
            <div class="form-actions" style="margin-top:20px;">
              <button type="submit" name="add_product" class="btn-submit"><i class="fas fa-plus"></i> Add Product</button>
              <button type="reset" class="btn-reset" onclick="document.getElementById('previewBox').style.display='none'">Clear Form</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title"><div class="title-dot"></div> All Products (<?= $total_products ?>)</div>
          <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" class="search-input" id="searchInput" placeholder="Search products…" oninput="filterTable(this.value)"/>
          </div>
        </div>
        <div class="table-wrap">
          <?php mysqli_data_seek($products, 0); ?>
          <?php if ($total_products === 0): ?>
            <div class="empty-state"><div class="empty-icon">📦</div><p>No products yet. Add your first one above!</p></div>
          <?php else: ?>
          <table id="prodTable">
            <thead>
              <tr><th>Product</th><th>Category</th><th>Manufacturer</th><th>Price</th><th>Badge</th><th>Age</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <?php while ($p = mysqli_fetch_assoc($products)): ?>
              <tr>
                <td>
                  <div class="prod-cell">
                    <div class="prod-thumb">
                      <?php if (!empty($p['image_url']) && file_exists($upload_dir . $p['image_url'])): ?>
                        <img src="<?= $upload_dir . htmlspecialchars($p['image_url']) ?>" alt=""/>
                      <?php else: ?>🧸<?php endif; ?>
                    </div>
                    <div><div class="prod-name"><?= htmlspecialchars($p['name']) ?></div><div class="prod-cat">#<?= $p['id'] ?></div></div>
                  </div>
                </td>
                <td><?= htmlspecialchars($p['category_name'] ?? 'Uncategorized') ?></td>
                <td>
                  <?php if (!empty($p['manufacturer_name'])): ?>
                    <div class="manu-cell">
                      <div class="manu-logo">
                        <?php if (!empty($p['manufacturer_logo']) && file_exists($upload_dir . $p['manufacturer_logo'])): ?>
                          <img src="<?= $upload_dir . htmlspecialchars($p['manufacturer_logo']) ?>" alt=""/>
                        <?php else: ?>🏭<?php endif; ?>
                      </div>
                      <div><div class="manu-name"><?= htmlspecialchars($p['manufacturer_name']) ?></div><div class="manu-country"><?= htmlspecialchars($p['manufacturer_country'] ?? '') ?></div></div>
                    </div>
                  <?php else: ?><span style="color:var(--text-soft); font-size:13px;">—</span><?php endif; ?>
                </td>
                <td>
                  <div class="price-now">Tsh<?= number_format($p['price'], 2) ?></div>
                  <?php if (!empty($p['old_price']) && $p['old_price'] > 0): ?><div class="price-was">Tsh<?= number_format($p['old_price'], 2) ?></div><?php endif; ?>
                </td>
                <td>
                  <?php $badge = strtolower($p['badge'] ?? ''); $pillClass = match($badge) { 'hot' => 'pill-hot', 'new' => 'pill-new', 'sale' => 'pill-sale', default => 'pill-none' }; echo '<span class="badge-pill ' . $pillClass . '">' . ($p['badge'] ?: '—') . '</span>'; ?>
                </td>
                <td><?= htmlspecialchars($p['age_range'] ?? '—') ?></td>
                <td>
                  <div class="action-wrap">
                    <button class="act-btn act-edit" title="Edit" onclick="alert('Edit feature coming soon!')"><i class="fas fa-edit"></i></button>
                    <a href="admin.php?tab=products&delete=<?= $p['id'] ?>" onclick="return confirm('Delete this product permanently?')" class="act-btn act-delete" title="Delete"><i class="fas fa-trash"></i></a>
                  </div>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- CATEGORIES -->
    <div class="tab-content <?= $active_tab == 'categories' ? 'active' : '' ?>">
      <div class="card" id="add-category-section">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> Add New Category</div></div>
        <div class="card-body">
          <form method="POST" action="admin.php?tab=categories">
            <div class="form-grid">
              <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="cat_name" class="form-control" placeholder="e.g. Plush Toys" required/>
              </div>
              <div class="form-group">
                <label>Icon (Emoji)</label>
                <input type="text" name="cat_icon" class="form-control" placeholder="e.g. 🧸" maxlength="10"/>
              </div>
              <div class="form-group full">
                <label>Description</label>
                <textarea name="cat_description" class="form-control" placeholder="Brief description of this category…"></textarea>
              </div>
            </div>
            <div class="form-actions" style="margin-top:20px;">
              <button type="submit" name="add_category" class="btn-submit" style="background: linear-gradient(135deg, var(--gold), #D4A030);"><i class="fas fa-plus"></i> Add Category</button>
              <button type="reset" class="btn-reset">Clear Form</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> All Categories (<?= $total_categories ?>)</div></div>
        <div class="card-body">
          <?php if ($total_categories === 0): ?>
            <div class="empty-state"><div class="empty-icon">🏷️</div><p>No categories yet. Add your first one above!</p></div>
          <?php else: ?>
            <div class="cat-grid">
              <?php mysqli_data_seek($categories, 0); while ($cat = mysqli_fetch_assoc($categories)): ?>
                <div class="cat-card-admin">
                  <div class="cat-card-icon"><?= htmlspecialchars($cat['icon'] ?? '📦') ?></div>
                  <div class="cat-card-title"><?= htmlspecialchars($cat['name']) ?></div>
                  <div class="cat-card-desc"><?= htmlspecialchars($cat['description'] ?: 'No description.') ?></div>
                  <div class="cat-card-footer">
                    <span class="cat-card-count"><i class="fas fa-box"></i> <?= $cat['product_count'] ?> product<?= $cat['product_count'] != 1 ? 's' : '' ?></span>
                    <a href="admin.php?tab=categories&delete_category=<?= $cat['id'] ?>" onclick="return confirm('Delete this category?')" class="act-btn act-delete" title="Delete" style="width:28px;height:28px;font-size:12px;"><i class="fas fa-trash"></i></a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- MANUFACTURERS -->
    <div class="tab-content <?= $active_tab == 'manufacturers' ? 'active' : '' ?>">
      <div class="card" id="add-manufacturer-section">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> Add New Manufacturer</div></div>
        <div class="card-body">
          <form method="POST" action="admin.php?tab=manufacturers" enctype="multipart/form-data">
            <div class="form-grid">
              <div class="form-group">
                <label>Manufacturer Name</label>
                <input type="text" name="m_name" class="form-control" placeholder="e.g. Fisher-Price" required/>
              </div>
              <div class="form-group">
                <label>Country</label>
                <input type="text" name="m_country" class="form-control" placeholder="e.g. USA, China, Germany"/>
              </div>
              <div class="form-group">
                <label>Website</label>
                <input type="url" name="m_website" class="form-control" placeholder="https://example.com"/>
              </div>
              <div class="form-group full">
                <label>Description</label>
                <textarea name="m_description" class="form-control" placeholder="Brief description of the manufacturer…"></textarea>
              </div>
              <div class="form-group full">
                <label>Manufacturer Logo</label>
                <div class="file-zone" onclick="document.getElementById('manuLogo').click()">
                  <input type="file" name="m_logo" id="manuLogo" accept="image/*" onchange="previewManuLogo(this)"/>
                  <div class="file-zone-icon">🏭</div>
                  <div class="file-zone-text">Click to upload logo</div>
                  <div class="file-zone-sub">JPG, PNG, WEBP — max 2 MB</div>
                </div>
                <div class="preview-box" id="manuPreviewBox">
                  <img id="manuPreviewImg" src="" alt="Preview"/>
                  <p id="manuPreviewName"></p>
                </div>
              </div>
            </div>
            <div class="form-actions" style="margin-top:20px;">
              <button type="submit" name="add_manufacturer" class="btn-submit" style="background: linear-gradient(135deg, #8B7FD4, #6B5DB4);"><i class="fas fa-plus"></i> Add Manufacturer</button>
              <button type="reset" class="btn-reset" onclick="document.getElementById('manuPreviewBox').style.display='none'">Clear Form</button>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title"><div class="title-dot"></div> All Manufacturers (<?= $total_manufacturers ?>)</div></div>
        <div class="card-body">
          <?php if ($total_manufacturers === 0): ?>
            <div class="empty-state"><div class="empty-icon">🏭</div><p>No manufacturers yet. Add your first one above!</p></div>
          <?php else: ?>
            <div class="manu-grid">
              <?php mysqli_data_seek($manufacturers, 0); while ($m = mysqli_fetch_assoc($manufacturers)): 
                $mid = $m['id']; $prod_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE manufacturer_id = $mid"))['count'];
              ?>
                <div class="manu-card">
                  <div class="manu-card-header">
                    <div class="manu-card-logo">
                      <?php if (!empty($m['logo']) && file_exists($upload_dir . $m['logo'])): ?>
                        <img src="<?= $upload_dir . htmlspecialchars($m['logo']) ?>" alt=""/>
                      <?php else: ?>🏭<?php endif; ?>
                    </div>
                    <div>
                      <div class="manu-card-title"><?= htmlspecialchars($m['name']) ?></div>
                      <div class="manu-card-country"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($m['country'] ?: 'Unknown') ?></div>
                    </div>
                  </div>
                  <div class="manu-card-desc"><?= htmlspecialchars($m['description'] ?: 'No description provided.') ?></div>
                  <div class="manu-card-footer">
                    <span style="font-size:13px; color:var(--text-soft);"><i class="fas fa-box"></i> <?= $prod_count ?> product<?= $prod_count != 1 ? 's' : '' ?></span>
                    <div class="action-wrap">
                      <?php if (!empty($m['website'])): ?>
                        <a href="<?= htmlspecialchars($m['website']) ?>" target="_blank" class="manu-card-link"><i class="fas fa-external-link-alt"></i> Visit</a>
                      <?php endif; ?>
                      <a href="admin.php?tab=manufacturers&delete_manufacturer=<?= $m['id'] ?>" onclick="return confirm('Delete this manufacturer? Products will be unlinked.')" class="act-btn act-delete" title="Delete" style="width:28px;height:28px;font-size:12px;"><i class="fas fa-trash"></i></a>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  function previewImg(input) {
    const box = document.getElementById('previewBox'), img = document.getElementById('previewImg'), name = document.getElementById('previewName');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => { img.src = e.target.result; name.textContent = input.files[0].name; box.style.display = 'block'; };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function previewManuLogo(input) {
    const box = document.getElementById('manuPreviewBox'), img = document.getElementById('manuPreviewImg'), name = document.getElementById('manuPreviewName');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => { img.src = e.target.result; name.textContent = input.files[0].name; box.style.display = 'block'; };
      reader.readAsDataURL(input.files[0]);
    }
  }
  function filterTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#prodTable tbody tr').forEach(row => { row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none'; });
  }
</script>
</body>
</html