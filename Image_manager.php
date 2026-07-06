
<?php
session_start();
require_once "config.php";
 
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php"); exit();
}
 
$upload_dir = "uploads/products/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
 
$success = ""; $error = "";
 
// ---- Upload new photos straight into the products folder ----
if (isset($_POST['upload_new']) && isset($_FILES['new_images'])) {
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $count = 0;
    foreach ($_FILES['new_images']['name'] as $i => $filename) {
        if ($_FILES['new_images']['error'][$i] !== 0 || $filename === '') continue;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) continue;
        $clean = preg_replace('/[^a-zA-Z0-9\-_\.]/', '-', pathinfo($filename, PATHINFO_FILENAME));
        $saved = $clean . '_' . substr(uniqid(), -5) . '.' . $ext;
        if (move_uploaded_file($_FILES['new_images']['tmp_name'][$i], $upload_dir . $saved)) $count++;
    }
    $success = "$count photo(s) uploaded. They now appear below, ready to assign.";
}
 
// ---- Assign an existing photo to a product ----
if (isset($_POST['assign_action'])) {
    $filename = basename($_POST['filename'] ?? '');
    $product_id = intval($_POST['product_id'] ?? 0);
    $full_path = $upload_dir . $filename;
 
    if (!$filename || !file_exists($full_path)) {
        $error = "That photo could not be found.";
    } elseif (!$product_id) {
        $error = "Please search and click a product from the dropdown list before assigning.";
    } else {
        $owns = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM products WHERE id = $product_id"));
        if (!$owns) {
            $error = "That product id does not exist.";
        } else {
            $filename_safe = mysqli_real_escape_string($conn, $filename);
            if ($_POST['assign_action'] === 'set_cover') {
                mysqli_query($conn, "UPDATE products SET image_url = '$filename_safe' WHERE id = $product_id");
                mysqli_query($conn, "UPDATE product_images SET is_primary = 0 WHERE product_id = $product_id");
                $exists = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM product_images WHERE product_id = $product_id AND image_url = '$filename_safe'"));
                if ($exists) mysqli_query($conn, "UPDATE product_images SET is_primary = 1, display_order = 0 WHERE id = " . $exists['id']);
                else mysqli_query($conn, "INSERT INTO product_images (product_id, image_url, display_order, is_primary) VALUES ($product_id, '$filename_safe', 0, 1)");
                $success = "Set as the cover photo for product #$product_id.";
            } elseif ($_POST['assign_action'] === 'add_gallery') {
                $exists = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM product_images WHERE product_id = $product_id AND image_url = '$filename_safe'"));
                if (!$exists) {
                    $next_order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM product_images WHERE product_id = $product_id"))['c'];
                    mysqli_query($conn, "INSERT INTO product_images (product_id, image_url, display_order, is_primary) VALUES ($product_id, '$filename_safe', $next_order, 0)");
                }
                $success = "Added to the photo gallery for product #$product_id.";
            }
        }
    }
}
 
// ---- Data for the page ----
$images = [];
foreach (glob($upload_dir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE) as $path) {
    $images[] = basename($path);
}
sort($images);
 
$products = mysqli_query($conn, "SELECT p.id, p.name, p.image_url, c.name as category_name, c.pillar
                                  FROM products p LEFT JOIN categories c ON p.category_id = c.id
                                  ORDER BY c.pillar, p.name ASC");
$product_list = [];
$missing_photo = [];
while ($row = mysqli_fetch_assoc($products)) {
    $product_list[] = $row;
    if (empty($row['image_url']) || !file_exists($upload_dir . $row['image_url'])) $missing_photo[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Image Manager - BabyBliss Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
  :root { --cream:#FFF8F0; --rose:#E8738A; --deep-rose:#C44D65; --mint-dark:#5FB8A0; --text-dark:#2D1B14; --text-mid:#6B4C3B; --text-light:#A07D6A; --shadow:rgba(196,77,101,.12); }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DM Sans',sans-serif; background:var(--cream); color:var(--text-dark); }
  header { background:#fff; padding:20px 32px; box-shadow:0 2px 20px var(--shadow); display:flex; align-items:center; justify-content:space-between; }
  header h1 { font-family:'Playfair Display',serif; font-size:22px; color:var(--deep-rose); }
  header a { color:var(--rose); text-decoration:none; font-weight:600; font-size:14px; }
  .wrap { max-width:1200px; margin:0 auto; padding:28px 24px 60px; }
  .alert { padding:14px 18px; border-radius:12px; margin-bottom:20px; font-size:14px; font-weight:600; }
  .alert-success { background:#EAF8F4; color:#2E7D62; border:1px solid #B0E0D0; }
  .alert-error { background:#FFF0F3; color:var(--deep-rose); border:1px solid #F5B8C8; }
  .card { background:#fff; border-radius:18px; padding:24px; box-shadow:0 4px 20px var(--shadow); margin-bottom:28px; }
  .card h2 { font-family:'Playfair Display',serif; font-size:18px; margin-bottom:14px; }
  .upload-zone { border:2px dashed #F0C8D2; border-radius:14px; padding:22px; text-align:center; cursor:pointer; color:var(--text-mid); }
  .upload-zone:hover { border-color:var(--rose); color:var(--deep-rose); }
  .img-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(220px,1fr)); gap:18px; }
  .img-card { border:1px solid #F0E4DC; border-radius:14px; overflow:hidden; background:#FFFDFB; }
  .img-card img { width:100%; height:160px; object-fit:cover; display:block; }
  .img-card-body { padding:12px; }
  .img-card-body .fname { font-size:11px; color:var(--text-light); margin-bottom:8px; word-break:break-all; }
  .img-card-body input[type=text] { width:100%; padding:8px 10px; border:1.5px solid #F0E4DC; border-radius:8px; font-size:12.5px; margin-bottom:8px; }
  .product-picker { position:relative; margin-bottom:8px; }
  .suggestions { display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1.5px solid var(--rose); border-radius:0 0 10px 10px; max-height:180px; overflow-y:auto; z-index:50; box-shadow:0 8px 20px rgba(0,0,0,.12); }
  .sug-item { padding:9px 12px; font-size:12.5px; cursor:pointer; border-bottom:1px solid #F5EDE7; }
  .sug-item:last-child { border-bottom:none; }
  .sug-item:hover, .sug-item.hl { background:#FFF0F3; }
  .sug-item .sug-cat { color:var(--text-light); font-size:11px; }
  .sug-empty { padding:10px 12px; font-size:12.5px; color:var(--text-light); font-style:italic; }
  .picked-tag { display:none; align-items:center; gap:6px; background:#EAF8F4; color:#2E7D62; font-size:11.5px; font-weight:600; padding:5px 10px; border-radius:8px; margin-bottom:8px; }
  .picked-tag i { cursor:pointer; }
  .btn-row { display:flex; gap:6px; }
  .btn-row button { flex:1; padding:8px; border:none; border-radius:8px; font-size:11.5px; font-weight:700; cursor:pointer; }
  .btn-cover { background:var(--rose); color:#fff; }
  .btn-gallery { background:#F0E4DC; color:var(--text-dark); }
  .missing-table { width:100%; border-collapse:collapse; font-size:13.5px; }
  .missing-table th { text-align:left; padding:10px; background:#FFF0F3; color:var(--deep-rose); font-size:12px; text-transform:uppercase; }
  .missing-table td { padding:10px; border-bottom:1px solid #F5EDE7; }
  .pill { display:inline-block; padding:2px 10px; border-radius:20px; font-size:11px; font-weight:700; background:#EEF0FB; color:#4455AA; }
</style>
</head>
<body>
 
<header>
  <h1>🖼️ Image Manager</h1>
  <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Shop</a>
</header>
 
<div class="wrap">
  <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
 
  <div class="card">
    <h2>Upload New Photos</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="upload-zone" onclick="document.getElementById('newImages').click()">
        <input type="file" name="new_images[]" id="newImages" accept="image/*" multiple style="display:none" onchange="this.form.submit()"/>
        <i class="fas fa-cloud-upload-alt" style="font-size:26px;"></i>
        <div style="margin-top:6px;font-weight:600;">Click to add photos to the library</div>
        <div style="font-size:12px;margin-top:4px;">They'll appear below so you can assign them to any product</div>
      </div>
      <input type="hidden" name="upload_new" value="1"/>
    </form>
  </div>
 
  <div class="card">
    <h2>Available Photos (<?= count($images) ?>)</h2>
 
    <?php if (empty($images)): ?>
      <p style="color:var(--text-light);">No photos yet — upload some above.</p>
    <?php else: ?>
    <div class="img-grid">
      <?php foreach ($images as $img): ?>
      <div class="img-card">
        <img src="<?= $upload_dir . htmlspecialchars($img) ?>" alt=""/>
        <div class="img-card-body">
          <div class="fname"><?= htmlspecialchars($img) ?></div>
          <form method="POST">
            <input type="hidden" name="filename" value="<?= htmlspecialchars($img) ?>"/>
            <div class="picked-tag"><i class="fas fa-check-circle"></i> <span class="picked-name"></span> <i class="fas fa-times" onclick="clearPick(this)" style="margin-left:auto;"></i></div>
            <div class="product-picker">
              <input type="text" class="product-search" placeholder="Type product name or ID..." autocomplete="off" oninput="filterProducts(this)" onfocus="filterProducts(this)"/>
              <input type="hidden" name="product_id" class="product-id-field" value=""/>
              <div class="suggestions"></div>
            </div>
            <div class="btn-row">
              <button type="submit" name="assign_action" value="set_cover" class="btn-cover">Set Cover</button>
              <button type="submit" name="assign_action" value="add_gallery" class="btn-gallery">+ Gallery</button>
            </div>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
 
  <div class="card">
    <h2>Products Still Missing a Real Photo (<?= count($missing_photo) ?>)</h2>
    <?php if (empty($missing_photo)): ?>
      <p style="color:var(--mint-dark);font-weight:600;"><i class="fas fa-check-circle"></i> Every product has a real photo assigned. 🎉</p>
    <?php else: ?>
    <table class="missing-table">
      <thead><tr><th>ID</th><th>Product</th><th>Category</th><th>Pillar</th></tr></thead>
      <tbody>
        <?php foreach ($missing_photo as $mp): ?>
        <tr>
          <td>#<?= $mp['id'] ?></td>
          <td><?= htmlspecialchars($mp['name']) ?></td>
          <td><?= htmlspecialchars($mp['category_name'] ?? '—') ?></td>
          <td><span class="pill"><?= htmlspecialchars($mp['pillar'] ?? '—') ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>
 
<script>
  const ALL_PRODUCTS = <?= json_encode(array_map(function($p) {
      return ['id' => (int)$p['id'], 'name' => $p['name'], 'cat' => $p['category_name'] ?? ''];
  }, $product_list)) ?>;
 
  function escapeHtml(str) {
    return String(str).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }
 
  function filterProducts(input) {
    const wrap = input.closest('.product-picker');
    const box = wrap.querySelector('.suggestions');
    const hidden = wrap.querySelector('.product-id-field');
    hidden.value = ''; // typing again means the previous pick no longer counts
    const q = input.value.trim().toLowerCase();
 
    if (!q) { box.style.display = 'none'; box.innerHTML = ''; return; }
 
    const matches = ALL_PRODUCTS.filter(p =>
      p.name.toLowerCase().includes(q) || String(p.id) === q
    ).slice(0, 20);
 
    if (matches.length === 0) {
      box.innerHTML = '<div class="sug-empty">No product matches "' + escapeHtml(input.value) + '"</div>';
      box.style.display = 'block';
      return;
    }
 
    box.innerHTML = matches.map(p =>
      '<div class="sug-item" data-id="' + p.id + '" data-name="' + escapeHtml(p.name) + '">' +
      '#' + p.id + ' — ' + escapeHtml(p.name) +
      (p.cat ? ' <span class="sug-cat">(' + escapeHtml(p.cat) + ')</span>' : '') +
      '</div>'
    ).join('');
    box.style.display = 'block';
  }
 
  // Event delegation: works no matter how the suggestion HTML was built, and
  // sidesteps any quoting issues from product names containing quotes/apostrophes.
  document.addEventListener('click', function (e) {
    const item = e.target.closest('.sug-item');
    if (!item || !item.dataset.id) return;
    const wrap = item.closest('.product-picker');
    const card = item.closest('.img-card-body');
    const id = item.dataset.id, name = item.dataset.name;
    wrap.querySelector('.product-search').value = '#' + id + ' — ' + name;
    wrap.querySelector('.product-id-field').value = id;
    wrap.querySelector('.suggestions').style.display = 'none';
    const tag = card.querySelector('.picked-tag');
    tag.style.display = 'flex';
    tag.querySelector('.picked-name').textContent = '#' + id + ' — ' + name;
  });
 
  function clearPick(el) {
    const card = el.closest('.img-card-body');
    card.querySelector('.product-search').value = '';
    card.querySelector('.product-id-field').value = '';
    card.querySelector('.picked-tag').style.display = 'none';
  }
 
  // Close any open dropdown when clicking elsewhere on the page
  document.addEventListener('click', function (e) {
    document.querySelectorAll('.product-picker').forEach(function (wrap) {
      if (!wrap.contains(e.target)) wrap.querySelector('.suggestions').style.display = 'none';
    });
  });
 
  // Require an actual dropdown pick (not just typed text) before submitting
  document.querySelectorAll('.img-card-body form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      const idField = form.querySelector('.product-id-field');
      if (!idField.value) {
        e.preventDefault();
        alert('Tafadhali chagua bidhaa kwenye orodha inayotokea chini ya search box kabla ya kubofya Set Cover / + Gallery.');
      }
    });
  });
</script>
</body>
</html>