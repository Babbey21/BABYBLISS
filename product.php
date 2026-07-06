t · PHP
<?php
session_start();
require_once "config.php";
 
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$product_id) {
    header("Location: index.php");
    exit();
}
 
// Fetch product + manufacturer + category
$product_query = "SELECT p.*, m.company_name, m.country as manufacturer_country, m.logo as manufacturer_logo,
                          m.rating as manufacturer_rating, m.total_sales, c.name as category_name, c.icon as category_icon, c.pillar as category_pillar
                   FROM products p
                   LEFT JOIN manufacturers m ON p.manufacturer_id = m.id
                   LEFT JOIN categories c ON p.category_id = c.id
                   WHERE p.id = $product_id AND p.status = 'active' AND p.is_active = 1
                   LIMIT 1";
$result = mysqli_query($conn, $product_query);
 
if (!$result || mysqli_num_rows($result) === 0) {
    header("Location: index.php");
    exit();
}
$p = mysqli_fetch_assoc($result);
 
// Fetch variant groups (e.g. Color, Size) and their options for this product
$groups = [];
$groups_query = "SELECT * FROM product_variant_groups WHERE product_id = $product_id ORDER BY display_order ASC, id ASC";
$groups_result = mysqli_query($conn, $groups_query);
if ($groups_result) {
    while ($g = mysqli_fetch_assoc($groups_result)) {
        $options_query = "SELECT * FROM product_variant_options WHERE group_id = " . intval($g['id']) . " ORDER BY display_order ASC, id ASC";
        $options_result = mysqli_query($conn, $options_query);
        $options = [];
        while ($o = mysqli_fetch_assoc($options_result)) {
            $options[] = [
                'id' => (int)$o['id'],
                'value' => $o['value'],
                'swatch' => $o['swatch_hex'],
                'price_adjustment' => (float)$o['price_adjustment'],
                'image_url' => $o['image_url'],
                'stock' => (int)$o['stock'],
            ];
        }
        $groups[] = [
            'id' => (int)$g['id'],
            'attribute_name' => $g['attribute_name'],
            'options' => $options,
        ];
    }
}
$has_variants = count($groups) > 0;
 
// Related products (same category)
$related = [];
if (!empty($p['category_id'])) {
    $related_query = "SELECT id, name, price, old_price, image_url, badge, rating FROM products
                       WHERE category_id = " . intval($p['category_id']) . " AND id != $product_id
                       AND status='active' AND is_active=1 LIMIT 4";
    $related_result = mysqli_query($conn, $related_query);
    while ($r = mysqli_fetch_assoc($related_result)) $related[] = $r;
}
 
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
 
function isImagePath($str) {
    if (empty($str)) return false;
    $exts = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];
    foreach ($exts as $ext) if (stripos($str, $ext) !== false) return true;
    return strpos($str, '/') !== false || strpos($str, 'uploads') !== false;
}
 
// Real, royalty-free stock photo fallback (LoremFlickr), only used when no manufacturer
// photo has been uploaded yet. "lock" keeps it the same photo every time for this product.
function placeholderPhoto($product_id, $keyword = 'baby,product') {
    return "https://loremflickr.com/700/700/" . $keyword . "?lock=" . intval($product_id);
}
 
$pillar_keywords = ['baby_gear' => 'babytoy', 'nutrition' => 'babyfood', 'parenting' => 'newborn'];
$placeholder_keyword = $pillar_keywords[$p['category_pillar'] ?? ''] ?? 'baby';
 
$main_img = !empty($p['image_url']) && file_exists('uploads/products/')
    ? 'uploads/products/' . htmlspecialchars($p['image_url']) : '';
 
// Full photo gallery for this product (falls back to just the cover photo if none uploaded)
$gallery_images = [];
$gi = mysqli_query($conn, "SELECT image_url FROM product_images WHERE product_id = $product_id ORDER BY is_primary DESC, display_order ASC, id ASC");
if ($gi) {
    while ($g = mysqli_fetch_assoc($gi)) {
        if (!empty($g['image_url']) && file_exists('uploads/products/' . $g['image_url'])) {
            $gallery_images[] = 'uploads/products/' . htmlspecialchars($g['image_url']);
        }
    }
}
if (empty($gallery_images) && $main_img) $gallery_images[] = $main_img;
if (empty($main_img) && !empty($gallery_images)) $main_img = $gallery_images[0];
if (empty($main_img)) $main_img = placeholderPhoto($product_id, $placeholder_keyword);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($p['name']) ?> – BabyBliss</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
  :root { --cream:#FFF8F0; --blush:#F2A7B3; --rose:#E8738A; --deep-rose:#C44D65; --mint:#A8D8C8; --mint-dark:#5FB8A0;
    --gold:#F5C842; --white:#FFFFFF; --text-dark:#2D1B14; --text-mid:#6B4C3B; --text-light:#A07D6A; --shadow:rgba(196,77,101,0.12); }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DM Sans', sans-serif; background:var(--cream); color:var(--text-dark); line-height:1.6; }
 
  header { background:var(--white); position:sticky; top:0; z-index:999; box-shadow:0 2px 20px var(--shadow); }
  .header-main { display:flex; align-items:center; justify-content:space-between; padding:0 48px; height:72px; max-width:1400px; margin:0 auto; }
  .logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
  .logo-icon { width:44px; height:44px; background:linear-gradient(135deg, var(--blush), var(--deep-rose)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; }
  .logo-text { font-family:'Playfair Display', serif; font-size:26px; font-weight:700; color:var(--deep-rose); }
  .header-actions { display:flex; align-items:center; gap:12px; }
  .icon-btn { position:relative; background:none; border:none; font-size:20px; color:var(--text-mid); cursor:pointer; padding:8px; border-radius:10px; transition:all .2s; text-decoration:none; }
  .icon-btn:hover { background:var(--cream); color:var(--deep-rose); }
  .badge-cart { position:absolute; top:2px; right:2px; background:var(--deep-rose); color:var(--white); font-size:10px; font-weight:600; width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; }
  .btn-back { padding:9px 22px; border-radius:10px; font-size:14px; font-weight:600; cursor:pointer; border:2px solid var(--rose); color:var(--rose); background:transparent; transition:all .2s; text-decoration:none; }
  .btn-back:hover { background:var(--rose); color:var(--white); }
 
  .breadcrumb { max-width:1200px; margin:0 auto; padding:20px 48px 0; font-size:13px; color:var(--text-light); }
  .breadcrumb a { color:var(--text-light); text-decoration:none; }
  .breadcrumb a:hover { color:var(--deep-rose); }
 
  .product-main { max-width:1200px; margin:0 auto; padding:24px 48px 60px; display:grid; grid-template-columns:1fr 1fr; gap:48px; }
 
  .gallery { position:sticky; top:100px; height:fit-content; }
  .gallery-main { width:100%; aspect-ratio:1/1; background:linear-gradient(135deg,#FFF0F5,#FFE4EA); border-radius:24px; display:flex; align-items:center; justify-content:center; font-size:140px; overflow:hidden; box-shadow:0 8px 30px var(--shadow); transition:all .25s; }
  .gallery-main img { width:100%; height:100%; object-fit:cover; }
  .gallery-thumbs { display:flex; gap:10px; margin-top:14px; flex-wrap:wrap; }
  .gallery-thumb { width:72px; height:72px; border-radius:12px; overflow:hidden; cursor:pointer; border:2px solid transparent; opacity:.7; transition:all .2s; flex-shrink:0; }
  .gallery-thumb img { width:100%; height:100%; object-fit:cover; }
  .gallery-thumb:hover { opacity:1; }
  .gallery-thumb.active { border-color:var(--rose); opacity:1; }
 
  .product-info h1 { font-family:'Playfair Display', serif; font-size:30px; color:var(--text-dark); margin-bottom:10px; line-height:1.25; }
  .manufacturer-row { display:flex; align-items:center; gap:8px; margin-bottom:14px; font-size:13px; color:var(--text-light); }
  .manufacturer-row .verified { color:var(--mint-dark); font-weight:600; }
  .rating-row { display:flex; align-items:center; gap:8px; margin-bottom:18px; }
  .stars { color:var(--gold); font-size:14px; }
  .review-count { font-size:13px; color:var(--text-light); }
 
  .price-box { display:flex; align-items:baseline; gap:12px; margin-bottom:6px; }
  .price-current { font-size:34px; font-weight:700; color:var(--deep-rose); transition:all .15s; }
  .price-old { font-size:16px; color:var(--text-light); text-decoration:line-through; }
  .price-note { font-size:13px; color:var(--text-light); margin-bottom:22px; }
 
  .variant-block { margin-bottom:22px; }
  .variant-label { font-size:14px; font-weight:700; color:var(--text-dark); margin-bottom:10px; display:flex; align-items:center; gap:8px; }
  .variant-label .selected-value { color:var(--deep-rose); font-weight:600; }
  .variant-options { display:flex; flex-wrap:wrap; gap:10px; }
 
  .variant-pill { padding:10px 18px; border-radius:12px; border:2px solid #F0E4DC; background:var(--white); font-size:14px; font-weight:600; color:var(--text-mid); cursor:pointer; transition:all .2s; position:relative; }
  .variant-pill:hover { border-color:var(--blush); }
  .variant-pill.active { border-color:var(--rose); background:linear-gradient(135deg,#FFF0F3,#FFE4EA); color:var(--deep-rose); }
  .variant-pill.out-of-stock { opacity:0.4; cursor:not-allowed; text-decoration:line-through; }
 
  .variant-swatch { width:42px; height:42px; border-radius:50%; border:3px solid #F0E4DC; cursor:pointer; transition:all .2s; position:relative; }
  .variant-swatch:hover { transform:scale(1.08); }
  .variant-swatch.active { border-color:var(--rose); box-shadow:0 0 0 3px rgba(232,115,138,0.25); }
  .variant-swatch.out-of-stock { opacity:0.3; cursor:not-allowed; }
  .variant-swatch.active::after { content:'✓'; position:absolute; inset:0; display:flex; align-items:center; justify-content:center; color:var(--white); font-size:14px; font-weight:700; text-shadow:0 1px 2px rgba(0,0,0,0.4); }
 
  .stock-note { font-size:13px; margin-bottom:18px; display:flex; align-items:center; gap:6px; }
  .stock-note.in-stock { color:var(--mint-dark); }
  .stock-note.low-stock { color:#C88A00; }
  .stock-note.out-stock { color:var(--deep-rose); }
 
  .qty-row { display:flex; align-items:center; gap:16px; margin-bottom:24px; }
  .qty-control { display:flex; align-items:center; gap:0; background:var(--cream); border-radius:12px; padding:4px; }
  .qty-btn { width:38px; height:38px; border:none; background:var(--white); border-radius:9px; cursor:pointer; font-size:18px; color:var(--text-mid); transition:all .2s; }
  .qty-btn:hover { background:var(--rose); color:var(--white); }
  .qty-value { font-size:16px; font-weight:700; min-width:40px; text-align:center; }
 
  .action-row { display:flex; gap:14px; margin-bottom:28px; }
  .btn-add-cart { flex:1; padding:17px; background:linear-gradient(135deg, var(--rose), var(--deep-rose)); color:var(--white); border:none; border-radius:14px; font-size:16px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; transition:all .25s; text-decoration:none; }
  .btn-add-cart:hover { transform:translateY(-2px); box-shadow:0 10px 30px rgba(196,77,101,0.35); }
  .btn-add-cart.disabled { opacity:0.5; cursor:not-allowed; transform:none; box-shadow:none; }
  .btn-wishlist { width:56px; flex-shrink:0; background:var(--white); border:2px solid #F0E4DC; border-radius:14px; font-size:18px; color:var(--text-mid); cursor:pointer; transition:all .2s; }
  .btn-wishlist:hover { border-color:var(--rose); color:var(--rose); }
 
  .escrow-note { background:linear-gradient(135deg,#EAF8F4,#D4F5EC); border-radius:12px; padding:14px 16px; font-size:13px; color:#2E7D62; display:flex; align-items:center; gap:8px; margin-bottom:24px; }
  .meta-list { display:flex; flex-direction:column; gap:10px; padding-top:20px; border-top:1px solid #F0E4DC; }
  .meta-list .row { display:flex; align-items:center; gap:10px; font-size:14px; color:var(--text-mid); }
  .meta-list .row i { color:var(--rose); width:18px; }
 
  .description-section { max-width:1200px; margin:0 auto; padding:0 48px 60px; }
  .description-section h2 { font-family:'Playfair Display', serif; font-size:24px; margin-bottom:16px; padding-bottom:12px; border-bottom:2px solid #F0E4DC; }
  .description-section p { font-size:15px; color:var(--text-mid); line-height:1.8; }
 
  .related-section { max-width:1200px; margin:0 auto; padding:0 48px 60px; }
  .related-section h2 { font-family:'Playfair Display', serif; font-size:24px; margin-bottom:20px; }
  .related-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
  .rel-card { background:var(--white); border-radius:16px; overflow:hidden; box-shadow:0 4px 16px var(--shadow); text-decoration:none; color:inherit; transition:all .25s; display:block; }
  .rel-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px var(--shadow); }
  .rel-img { width:100%; height:160px; background:linear-gradient(135deg,#FFF0F5,#FFE4EA); display:flex; align-items:center; justify-content:center; font-size:60px; }
  .rel-img img { width:100%; height:100%; object-fit:cover; }
  .rel-body { padding:14px; }
  .rel-name { font-size:14px; font-weight:600; margin-bottom:6px; height:38px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
  .rel-price { font-size:17px; font-weight:700; color:var(--deep-rose); }
 
  @media (max-width: 900px) {
    .header-main { padding:0 20px; }
    .product-main { grid-template-columns:1fr; padding:24px 20px 40px; gap:28px; }
    .gallery { position:static; }
    .related-grid { grid-template-columns:repeat(2,1fr); }
    .description-section, .related-section, .breadcrumb { padding-left:20px; padding-right:20px; }
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
    <div class="header-actions">
      <a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Shop</a>
      <a href="cart.php" class="icon-btn"><i class="fas fa-shopping-cart"></i><span class="badge-cart"><?= $cart_count ?></span></a>
    </div>
  </div>
</header>
 
<div class="breadcrumb">
  <a href="index.php">Home</a> /
  <?php if (!empty($p['category_name'])): ?>
    <a href="index.php?category=<?= (int)$p['category_id'] ?>"><?= htmlspecialchars($p['category_name']) ?></a> /
  <?php endif; ?>
  <span><?= htmlspecialchars($p['name']) ?></span>
</div>
 
<div class="product-main">
  <!-- Gallery -->
  <div class="gallery">
    <div class="gallery-main" id="galleryMain">
      <?php if ($main_img): ?>
        <img src="<?= $main_img ?>" alt="<?= htmlspecialchars($p['name']) ?>" id="galleryImg"/>
      <?php else: ?>
        <span id="galleryEmoji">🧸</span>
      <?php endif; ?>
    </div>
    <?php if (count($gallery_images) > 1): ?>
    <div class="gallery-thumbs">
      <?php foreach ($gallery_images as $i => $img): ?>
      <div class="gallery-thumb <?= $img === $main_img ? 'active' : '' ?>" onclick="setMainGalleryImage('<?= $img ?>', this)">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['name']) ?> photo <?= $i + 1 ?>"/>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
 
  <!-- Info -->
  <div class="product-info">
    <h1><?= htmlspecialchars($p['name']) ?></h1>
 
    <div class="manufacturer-row">
      <span>🏭 <?= htmlspecialchars($p['company_name'] ?? 'Unknown') ?></span>
      <span class="verified"><i class="fas fa-check-circle"></i> Verified</span>
      <span>📍 <?= htmlspecialchars($p['manufacturer_country'] ?? 'Global') ?></span>
    </div>
 
    <div class="rating-row">
      <span class="stars"><?= str_repeat('★', round($p['rating'] ?? 5)) . str_repeat('☆', 5 - round($p['rating'] ?? 5)) ?></span>
      <span class="review-count">(<?= $p['review_count'] ?? rand(10, 200) ?> reviews)</span>
    </div>
 
    <div class="price-box">
      <span class="price-current" id="priceCurrent">Tsh<?= number_format($p['price'], 0) ?></span>
      <?php if (!empty($p['old_price']) && $p['old_price'] > 0): ?>
        <span class="price-old" id="priceOld">Tsh<?= number_format($p['old_price'], 0) ?></span>
      <?php endif; ?>
    </div>
    <div class="price-note" id="priceNote">Price includes selected options below.</div>
 
    <?php foreach ($groups as $gIndex => $g): ?>
      <div class="variant-block" data-group-index="<?= $gIndex ?>">
        <div class="variant-label">
          <?= htmlspecialchars($g['attribute_name']) ?>:
          <span class="selected-value" id="selectedLabel-<?= $gIndex ?>"><?= htmlspecialchars($g['options'][0]['value'] ?? '') ?></span>
        </div>
        <div class="variant-options">
          <?php foreach ($g['options'] as $oIndex => $o):
            $isSwatch = !empty($o['swatch']);
            $oos = $o['stock'] <= 0;
          ?>
            <?php if ($isSwatch): ?>
              <div class="variant-swatch <?= $oIndex === 0 ? 'active' : '' ?> <?= $oos ? 'out-of-stock' : '' ?>"
                   style="background:<?= htmlspecialchars($o['swatch']) ?>;"
                   title="<?= htmlspecialchars($o['value']) ?>"
                   data-group="<?= $gIndex ?>" data-option="<?= $oIndex ?>"
                   onclick="<?= $oos ? '' : "selectVariant($gIndex, $oIndex)" ?>"></div>
            <?php else: ?>
              <div class="variant-pill <?= $oIndex === 0 ? 'active' : '' ?> <?= $oos ? 'out-of-stock' : '' ?>"
                   data-group="<?= $gIndex ?>" data-option="<?= $oIndex ?>"
                   onclick="<?= $oos ? '' : "selectVariant($gIndex, $oIndex)" ?>">
                <?= htmlspecialchars($o['value']) ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
 
    <div class="stock-note in-stock" id="stockNote"><i class="fas fa-check-circle"></i> In stock, ready to ship</div>
 
    <div class="qty-row">
      <div class="qty-control">
        <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
        <span class="qty-value" id="qtyValue">1</span>
        <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
      </div>
    </div>
 
    <div class="action-row">
      <a href="#" class="btn-add-cart" id="addCartBtn"><i class="fas fa-shopping-cart"></i> Add to Cart</a>
      <button type="button" class="btn-wishlist"><i class="far fa-heart"></i></button>
    </div>
 
    <div class="escrow-note"><i class="fas fa-shield-alt"></i> Payment protected by secure escrow — funds release to seller only after you confirm delivery.</div>
 
    <div class="meta-list">
      <div class="row"><i class="fas fa-truck"></i> Estimated delivery: <?= htmlspecialchars($p['estimated_delivery'] ?? '7-14 days') ?></div>
      <div class="row"><i class="fas fa-box-open"></i> Condition: <?= ucfirst($p['condition_type'] ?? 'New') ?></div>
      <div class="row"><i class="fas fa-undo"></i> 30-day return policy — see <a href="returns_refunds.php" style="color:var(--rose);">details</a></div>
    </div>
  </div>
</div>
 
<?php if (!empty($p['description'])): ?>
<div class="description-section">
  <h2>Product Description</h2>
  <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
</div>
<?php endif; ?>
 
<?php if (!empty($related)): ?>
<div class="related-section">
  <h2>You May Also Like</h2>
  <div class="related-grid">
    <?php foreach ($related as $r):
      $r_img = !empty($r['image_url']) && file_exists('uploads/products/' . $r['image_url']) ? 'uploads/products/' . htmlspecialchars($r['image_url']) : placeholderPhoto($r['id'], $placeholder_keyword);
    ?>
      <a href="product.php?id=<?= $r['id'] ?>" class="rel-card">
        <div class="rel-img"><img src="<?= $r_img ?>" alt="" loading="lazy"/></div>
        <div class="rel-body">
          <div class="rel-name"><?= htmlspecialchars($r['name']) ?></div>
          <div class="rel-price">Tsh<?= number_format($r['price'], 0) ?></div>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
 
<script>
// ---- Data passed from PHP ----
const basePrice = <?= json_encode((float)$p['price']) ?>;
const baseOldPrice = <?= json_encode(!empty($p['old_price']) ? (float)$p['old_price'] : null) ?>;
const baseImage = <?= json_encode($main_img ?: null) ?>;
const productId = <?= (int)$p['id'] ?>;
const productName = <?= json_encode($p['name']) ?>;
const variantGroups = <?= json_encode($groups, JSON_UNESCAPED_SLASHES) ?>;
 
// Currently selected option index per group (defaults to first option of each)
let selected = variantGroups.map(() => 0);
let qty = 1;
 
function formatTsh(n) {
  return 'Tsh' + Math.round(n).toLocaleString('en-US');
}
 
function selectVariant(groupIndex, optionIndex) {
  selected[groupIndex] = optionIndex;
 
  // Update active state for pills/swatches in this group
  document.querySelectorAll(`[data-group="${groupIndex}"]`).forEach(el => {
    el.classList.toggle('active', parseInt(el.dataset.option) === optionIndex);
  });
 
  // Update the "selected value" label
  const group = variantGroups[groupIndex];
  const opt = group.options[optionIndex];
  const labelEl = document.getElementById(`selectedLabel-${groupIndex}`);
  if (labelEl) labelEl.textContent = opt.value;
 
  recalculate();
}
 
function setMainGalleryImage(src, el) {
  const galleryMain = document.getElementById('galleryMain');
  galleryMain.innerHTML = `<img src="${src}" alt="${productName}" id="galleryImg"/>`;
  document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
  if (el) el.classList.add('active');
}
 
function recalculate() {
  // Sum price adjustments across all selected options
  let totalAdjustment = 0;
  let stock = Infinity;
  let chosenImage = null;
 
  variantGroups.forEach((group, gIndex) => {
    const opt = group.options[selected[gIndex]];
    if (!opt) return;
    totalAdjustment += parseFloat(opt.price_adjustment || 0);
    if (opt.stock !== undefined && opt.stock !== null) stock = Math.min(stock, opt.stock);
    if (opt.image_url) chosenImage = opt.image_url;
  });
 
  const newPrice = basePrice + totalAdjustment;
  document.getElementById('priceCurrent').textContent = formatTsh(newPrice);
 
  const oldPriceEl = document.getElementById('priceOld');
  if (oldPriceEl && baseOldPrice) {
    oldPriceEl.textContent = formatTsh(baseOldPrice + totalAdjustment);
  }
 
  // Update gallery image if this variant has its own photo
  if (chosenImage) {
    const galleryMain = document.getElementById('galleryMain');
    galleryMain.innerHTML = `<img src="uploads/products/${chosenImage}" alt="${productName}" id="galleryImg"/>`;
  } else if (baseImage) {
    const galleryMain = document.getElementById('galleryMain');
    if (!document.getElementById('galleryImg')) {
      galleryMain.innerHTML = `<img src="${baseImage}" alt="${productName}" id="galleryImg"/>`;
    }
  }
 
  updateStockNote(stock === Infinity ? 100 : stock);
  updateAddToCartLink(newPrice, stock === Infinity ? 100 : stock);
}
 
function updateStockNote(stock) {
  const note = document.getElementById('stockNote');
  const addBtn = document.getElementById('addCartBtn');
  if (stock <= 0) {
    note.className = 'stock-note out-stock';
    note.innerHTML = '<i class="fas fa-times-circle"></i> Out of stock for this option';
    addBtn.classList.add('disabled');
  } else if (stock <= 5) {
    note.className = 'stock-note low-stock';
    note.innerHTML = `<i class="fas fa-exclamation-circle"></i> Only ${stock} left in stock`;
    addBtn.classList.remove('disabled');
  } else {
    note.className = 'stock-note in-stock';
    note.innerHTML = '<i class="fas fa-check-circle"></i> In stock, ready to ship';
    addBtn.classList.remove('disabled');
  }
}
 
function changeQty(delta) {
  qty = Math.max(1, qty + delta);
  document.getElementById('qtyValue').textContent = qty;
  recalculate();
}
 
function updateAddToCartLink(price, stock) {
  const btn = document.getElementById('addCartBtn');
  if (stock <= 0) {
    btn.removeAttribute('href');
    btn.onclick = (e) => e.preventDefault();
    return;
  }
  // Build a human-readable variant label, e.g. "Color: Pink, Size: M"
  const variantLabel = variantGroups.map((g, i) => `${g.attribute_name}: ${g.options[selected[i]].value}`).join(', ');
  const cartImage = (function() {
    let img = null;
    variantGroups.forEach((g, i) => { if (g.options[selected[i]].image_url) img = g.options[selected[i]].image_url; });
    return img ? `uploads/products/${img}` : baseImage;
  })();
 
  const params = new URLSearchParams({
    add: productId,
    name: variantGroups.length ? `${productName} (${variantLabel})` : productName,
    price: price,
    image: cartImage,
    qty: qty
  });
  btn.href = 'cart.php?' + params.toString();
  btn.onclick = null;
}
 
// Initialize on load
recalculate();
</script>
 
</body>
</html>