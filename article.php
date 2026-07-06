<?php
session_start();
require_once "config.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = mysqli_query($conn, "SELECT * FROM articles WHERE id = $id AND is_published = 1");
$article = $stmt ? mysqli_fetch_assoc($stmt) : null;

if (!$article) {
    header("Location: index.php");
    exit();
}

// Bump view count
mysqli_query($conn, "UPDATE articles SET views = views + 1 WHERE id = $id");

$pillar = $article['pillar']; // 'nutrition' or 'parenting'
$back_page = $pillar === 'nutrition' ? 'nutrition.php' : 'parenting.php';
$accent = $pillar === 'nutrition' ? '#5FB8A0' : '#6678CC';
$accent_dark = $pillar === 'nutrition' ? '#3A9E88' : '#4455AA';
$accent_light = $pillar === 'nutrition' ? '#EAF8F4' : '#EEF0FB';

// Related articles from same pillar
$related = mysqli_query($conn, "SELECT * FROM articles WHERE pillar='$pillar' AND id != $id AND is_published=1 ORDER BY created_at DESC LIMIT 3");

$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($article['title']) ?> – BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --accent: <?= $accent ?>; --accent-dark: <?= $accent_dark ?>; --accent-light: <?= $accent_light ?>;
      --cream:#FFF8F0; --white:#FFFFFF; --text-dark:#2D1B14; --text-mid:#6B4C3B; --text-light:#A07D6A;
      --shadow: rgba(0,0,0,0.08);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DM Sans', sans-serif; background:var(--cream); color:var(--text-dark); line-height:1.7; }

    header { background:var(--white); position:sticky; top:0; z-index:999; box-shadow:0 2px 20px var(--shadow); }
    .header-main { display:flex; align-items:center; justify-content:space-between; padding:0 48px; height:72px; max-width:1100px; margin:0 auto; }
    .logo { display:flex; align-items:center; gap:10px; text-decoration:none; }
    .logo-icon { width:44px; height:44px; background:linear-gradient(135deg,var(--accent),var(--accent-dark)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; }
    .logo-text { font-family:'Playfair Display', serif; font-size:26px; font-weight:700; color:var(--accent-dark); }
    .btn-back { padding:9px 22px; border-radius:10px; font-size:14px; font-weight:600; border:2px solid var(--accent); color:var(--accent-dark); background:transparent; text-decoration:none; transition:all .2s; }
    .btn-back:hover { background:var(--accent); color:#fff; }

    .article-hero { background:var(--accent-light); padding:56px 24px 40px; text-align:center; }
    .article-topic-tag { display:inline-block; background:var(--accent); color:#fff; font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase; padding:5px 16px; border-radius:20px; margin-bottom:18px; }
    .article-hero h1 { font-family:'Playfair Display', serif; font-size:36px; max-width:700px; margin:0 auto 16px; color:var(--text-dark); line-height:1.25; }
    .article-meta-row { display:flex; align-items:center; justify-content:center; gap:18px; font-size:14px; color:var(--text-mid); }
    .article-big-icon { font-size:64px; margin-bottom:10px; }

    .article-body { max-width:700px; margin:0 auto; padding:48px 24px; background:var(--white); }
    .article-excerpt { font-size:18px; color:var(--text-mid); font-style:italic; margin-bottom:28px; padding-bottom:24px; border-bottom:2px solid var(--accent-light); }
    .article-content { font-size:16px; color:var(--text-dark); line-height:1.85; }
    .article-content p { margin-bottom:18px; }

    .disclaimer-box { background:var(--accent-light); border-left:4px solid var(--accent); border-radius:0 12px 12px 0; padding:18px 22px; margin-top:32px; font-size:13px; color:var(--text-mid); }

    .related-section { max-width:900px; margin:0 auto; padding:48px 24px 64px; }
    .related-title { font-family:'Playfair Display', serif; font-size:22px; margin-bottom:22px; text-align:center; }
    .related-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
    .related-card { background:var(--white); border-radius:16px; padding:20px; text-decoration:none; color:inherit; box-shadow:0 2px 12px var(--shadow); transition:all .25s; }
    .related-card:hover { transform:translateY(-4px); box-shadow:0 12px 28px rgba(0,0,0,.1); }
    .related-icon { font-size:32px; margin-bottom:10px; }
    .related-card h4 { font-size:14px; font-weight:700; margin-bottom:6px; line-height:1.4; }
    .related-card span { font-size:12px; color:var(--text-light); }

    .shop-cta { background:linear-gradient(135deg,var(--accent),var(--accent-dark)); border-radius:20px; padding:32px; text-align:center; color:#fff; margin-top:32px; }
    .shop-cta h3 { font-family:'Playfair Display', serif; font-size:22px; margin-bottom:10px; }
    .shop-cta p { opacity:.9; margin-bottom:18px; font-size:14px; }
    .shop-cta a { display:inline-flex; align-items:center; gap:8px; background:#fff; color:var(--accent-dark); padding:12px 26px; border-radius:11px; font-weight:700; text-decoration:none; transition:all .2s; }
    .shop-cta a:hover { transform:translateY(-2px); }

    @media (max-width:768px) {
      .header-main { padding:0 20px; }
      .article-hero { padding:40px 20px 32px; }
      .article-hero h1 { font-size:26px; }
      .related-grid { grid-template-columns:1fr; }
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
    <a href="<?= $back_page ?>?tab=articles" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Articles</a>
  </div>
</header>

<section class="article-hero">
  <div class="article-big-icon"><?= htmlspecialchars($article['icon'] ?: '📖') ?></div>
  <div class="article-topic-tag"><?= htmlspecialchars($article['topic'] ?: ucfirst($pillar)) ?></div>
  <h1><?= htmlspecialchars($article['title']) ?></h1>
  <div class="article-meta-row">
    <span><i class="fas fa-user"></i> <?= htmlspecialchars($article['author']) ?></span>
    <span><i class="fas fa-clock"></i> <?= (int)$article['read_time'] ?> min read</span>
    <span><i class="fas fa-eye"></i> <?= (int)$article['views'] ?> views</span>
  </div>
</section>

<div class="article-body">
  <p class="article-excerpt"><?= htmlspecialchars($article['excerpt']) ?></p>
  <div class="article-content">
    <?php foreach (explode("\n", trim($article['content'])) as $para): if (trim($para) !== ''): ?>
      <p><?= htmlspecialchars(trim($para)) ?></p>
    <?php endif; endforeach; ?>
  </div>

  <div class="disclaimer-box">
    <strong>ℹ️ Note:</strong> This article is for general guidance only and does not replace advice from your pediatrician or healthcare provider. Always consult a professional for concerns about your baby's health and development.
  </div>

  <div class="shop-cta">
    <h3><?= $pillar === 'nutrition' ? '🍎 Shop Nutrition Essentials' : '👶 Shop Parenting Essentials' ?></h3>
    <p>Find trusted products related to this topic from verified sellers.</p>
    <a href="<?= $back_page ?>?tab=products"><i class="fas fa-shopping-bag"></i> Browse Products</a>
  </div>
</div>

<?php if ($related && mysqli_num_rows($related) > 0): ?>
<section class="related-section">
  <h3 class="related-title">More Guides You Might Like</h3>
  <div class="related-grid">
    <?php while ($r = mysqli_fetch_assoc($related)): ?>
    <a href="article.php?id=<?= $r['id'] ?>" class="related-card">
      <div class="related-icon"><?= htmlspecialchars($r['icon'] ?: '📖') ?></div>
      <h4><?= htmlspecialchars($r['title']) ?></h4>
      <span><i class="fas fa-clock"></i> <?= (int)$r['read_time'] ?> min read</span>
    </a>
    <?php endwhile; ?>
  </div>
</section>
<?php endif; ?>

</body>
</html>
