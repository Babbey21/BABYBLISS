<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Restore this customer's saved cart from the database once per session
// (e.g. right after logging in) and merge it with anything already in the
// session cart, such as items added while still browsing as a guest.
if ($user_id && !isset($_SESSION['cart_synced'])) {
    $saved = mysqli_query($conn, "SELECT product_id, product_name, price, image, quantity FROM cart_items WHERE user_id = $user_id");
    if ($saved) {
        while ($srow = mysqli_fetch_assoc($saved)) {
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $srow['product_id'] && $item['name'] == $srow['product_name']) {
                    $item['quantity'] += (int)$srow['quantity'];
                    $found = true;
                    break;
                }
            }
            unset($item);
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => (int)$srow['product_id'],
                    'name' => $srow['product_name'],
                    'price' => (float)$srow['price'],
                    'image' => $srow['image'],
                    'quantity' => (int)$srow['quantity'],
                ];
            }
        }
    }
    $_SESSION['cart_synced'] = true;
}

// Persist the current session cart to the database for the logged-in customer,
// so it is still there next time they log in on any device/browser.
function saveCartToDb($conn, $user_id) {
    if (!$user_id) return;
    mysqli_query($conn, "DELETE FROM cart_items WHERE user_id = $user_id");
    foreach ($_SESSION['cart'] as $item) {
        $pid = intval($item['id']);
        $name = mysqli_real_escape_string($conn, $item['name']);
        $price = floatval($item['price']);
        $image = mysqli_real_escape_string($conn, $item['image']);
        $qty = intval($item['quantity']);
        mysqli_query($conn, "INSERT INTO cart_items (user_id, product_id, product_name, price, image, quantity) VALUES ($user_id, $pid, '$name', $price, '$image', $qty)");
    }
}

// Add to cart
if (isset($_GET['add']) && isset($_GET['name']) && isset($_GET['price'])) {
    $product_id = intval($_GET['add']);
    $product_name = htmlspecialchars($_GET['name']);
    $add_qty = isset($_GET['qty']) ? max(1, intval($_GET['qty'])) : 1;
    $found = false;
    // Match on id AND name so different variants (e.g. "Teddy Bear (Color: Pink)"
    // vs "Teddy Bear (Color: Blue)") are kept as separate cart lines.
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id && $item['name'] == $product_name) {
            $item['quantity'] += $add_qty;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => floatval($_GET['price']),
            'image' => isset($_GET['image']) ? $_GET['image'] : '🧸',
            'quantity' => $add_qty
        ];
    }
    saveCartToDb($conn, $user_id);
    header("Location: cart.php"); exit();
}

// Remove item
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$index])) { unset($_SESSION['cart'][$index]); $_SESSION['cart'] = array_values($_SESSION['cart']); }
    saveCartToDb($conn, $user_id);
    header("Location: cart.php"); exit();
}

// Update quantity
if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $index => $quantity) {
        if (isset($_SESSION['cart'][$index])) $_SESSION['cart'][$index]['quantity'] = max(1, intval($quantity));
    }
    saveCartToDb($conn, $user_id);
    header("Location: cart.php"); exit();
}

$subtotal = 0; $shipping = 0;
foreach ($_SESSION['cart'] as $item) $subtotal += $item['price'] * $item['quantity'];
$shipping = $subtotal > 50000 ? 0 : 5000;
$total = $subtotal + $shipping;
$cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));

function isImagePath($str) {
    if (empty($str)) return false;
    foreach (['.jpg','.jpeg','.png','.gif','.webp'] as $ext) if (stripos($str, $ext) !== false) return true;
    return strpos($str, '/') !== false || strpos($str, 'uploads') !== false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BabyBliss Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root { --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A; --deep-rose: #C44D65; --mint-dark: #5FB8A0; --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A; --shadow: rgba(196,77,101,0.12); }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--cream); color: var(--text-dark); }
        header { background: var(--white); position: sticky; top: 0; z-index: 999; box-shadow: 0 2px 20px var(--shadow); }
        .header-main { display: flex; align-items: center; justify-content: space-between; padding: 0 48px; height: 72px; }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .logo-icon { width: 44px; height: 44px; background: linear-gradient(135deg, var(--blush), var(--deep-rose)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .logo-text { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 700; color: var(--deep-rose); }
        .header-actions { display: flex; align-items: center; gap: 12px; }
        .icon-btn { position: relative; background: none; border: none; font-size: 20px; color: var(--text-mid); cursor: pointer; padding: 8px; border-radius: 10px; transition: all 0.2s; text-decoration: none; }
        .icon-btn:hover { background: var(--cream); color: var(--deep-rose); }
        .badge { position: absolute; top: 2px; right: 2px; background: var(--deep-rose); color: var(--white); font-size: 10px; font-weight: 600; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .btn-login { padding: 9px 22px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; border: 2px solid var(--rose); color: var(--rose); background: transparent; transition: all 0.2s; text-decoration: none; }
        .btn-login:hover { background: var(--rose); color: var(--white); }
        .btn-register { padding: 9px 22px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: var(--white); transition: all 0.2s; text-decoration: none; }
        .cart-container { max-width: 1200px; margin: 40px auto; padding: 0 48px; display: grid; grid-template-columns: 2fr 1fr; gap: 32px; }
        .cart-title { font-family: 'Playfair Display', serif; font-size: 32px; margin-bottom: 24px; color: var(--text-dark); }
        .cart-items { background: var(--white); border-radius: 20px; padding: 24px; box-shadow: 0 4px 20px var(--shadow); }
        .cart-item { display: flex; align-items: center; gap: 20px; padding: 20px 0; border-bottom: 1px solid #F0E4DC; }
        .cart-item:last-child { border-bottom: none; }
        .item-image { width: 80px; height: 80px; background: linear-gradient(135deg, #FFF0F5, #FFE4EA); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 40px; flex-shrink: 0; overflow: hidden; }
        .item-image img { width: 100%; height: 100%; object-fit: cover; border-radius: 16px; }
        .item-details { flex: 1; }
        .item-name { font-size: 16px; font-weight: 600; color: var(--text-dark); margin-bottom: 4px; }
        .item-price { font-size: 18px; font-weight: 700; color: var(--deep-rose); }
        .item-actions { display: flex; align-items: center; gap: 16px; }
        .qty-control { display: flex; align-items: center; gap: 12px; background: var(--cream); border-radius: 10px; padding: 4px; }
        .qty-btn { width: 32px; height: 32px; border: none; background: var(--white); border-radius: 8px; cursor: pointer; font-size: 16px; color: var(--text-mid); transition: all 0.2s; }
        .qty-btn:hover { background: var(--rose); color: var(--white); }
        .qty-value { font-size: 15px; font-weight: 600; min-width: 24px; text-align: center; }
        .remove-btn { background: none; border: none; color: var(--text-light); cursor: pointer; font-size: 18px; padding: 8px; border-radius: 8px; transition: all 0.2s; text-decoration: none; }
        .remove-btn:hover { color: var(--deep-rose); background: #FFF0F3; }
        .cart-summary { background: var(--white); border-radius: 20px; padding: 24px; box-shadow: 0 4px 20px var(--shadow); height: fit-content; position: sticky; top: 100px; }
        .summary-title { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; padding: 12px 0; font-size: 15px; color: var(--text-mid); }
        .summary-row.total { border-top: 2px solid #F0E4DC; font-size: 20px; font-weight: 700; color: var(--text-dark); margin-top: 8px; padding-top: 16px; }
        .checkout-btn { width: 100%; padding: 16px; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: var(--white); border: none; border-radius: 14px; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 20px; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.25s; text-decoration: none; text-align: center; }
        .checkout-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(196,77,101,0.35); }
        .continue-shopping { display: block; text-align: center; margin-top: 16px; color: var(--rose); text-decoration: none; font-weight: 600; font-size: 14px; }
        .empty-cart { text-align: center; padding: 60px 20px; }
        .empty-cart-icon { font-size: 80px; margin-bottom: 20px; }
        .empty-cart h3 { font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 12px; }
        .empty-cart p { color: var(--text-light); margin-bottom: 24px; }
        .escrow-note { background: linear-gradient(135deg, #EAF8F4, #D4F5EC); border-radius: 12px; padding: 14px; margin-top: 16px; font-size: 13px; color: #2E7D62; display: flex; align-items: center; gap: 8px; }
        @media (max-width: 900px) { .cart-container { grid-template-columns: 1fr; padding: 0 20px; } .header-main { padding: 0 20px; } }
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
            <a href="index.php" class="icon-btn"><i class="fas fa-home"></i></a>
            <a href="cart.php" class="icon-btn"><i class="fas fa-shopping-cart"></i><span class="badge"><?php echo $cart_count; ?></span></a>
            <?php if(isset($_SESSION['user_name'])): ?>
                <span style="font-size:14px; color:var(--text-mid);">👋 <?php echo $_SESSION['user_name']; ?></span>
                <a href="logout.php" class="btn-login">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="cart-container">
    <div>
        <h1 class="cart-title">Shopping Cart 🛒</h1>
        <?php if(empty($_SESSION['cart'])): ?>
            <div class="cart-items">
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h3>Your cart is empty</h3>
                    <p>Looks like you haven't added anything to your cart yet.</p>
                    <a href="index.php" class="btn-register" style="display:inline-block;">Start Shopping</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <div class="cart-items">
                    <?php foreach($_SESSION['cart'] as $index => $item): ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <?php $img = $item['image']; if (isImagePath($img)) { echo '<img src="' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($item['name']) . '">'; } else { echo $img; } ?>
                            </div>
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-price">Tsh<?php echo number_format($item['price'], 0); ?></div>
                            </div>
                            <div class="item-actions">
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" onclick="updateQty(<?php echo $index; ?>, -1)">−</button>
                                    <input type="hidden" name="qty[<?php echo $index; ?>]" id="qty-<?php echo $index; ?>" value="<?php echo $item['quantity']; ?>">
                                    <span class="qty-value" id="display-qty-<?php echo $index; ?>"><?php echo $item['quantity']; ?></span>
                                    <button type="button" class="qty-btn" onclick="updateQty(<?php echo $index; ?>, 1)">+</button>
                                </div>
                                <a href="cart.php?remove=<?php echo $index; ?>" class="remove-btn" onclick="return confirm('Remove this item?')"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" name="update_qty" style="display:none;" id="update-form"></button>
            </form>
        <?php endif; ?>
    </div>

    <?php if(!empty($_SESSION['cart'])): ?>
    <div class="cart-summary">
        <h2 class="summary-title">Order Summary</h2>
        <div class="summary-row"><span>Subtotal (<?php echo $cart_count; ?> items)</span><span>Tsh<?php echo number_format($subtotal, 0); ?></span></div>
        <div class="summary-row"><span>Shipping</span><span><?php echo $shipping == 0 ? 'FREE' : 'Tsh' . number_format($shipping, 0); ?></span></div>
        <div class="summary-row"><span>Tax</span><span>Calculated at checkout</span></div>
        <div class="summary-row total"><span>Total</span><span>Tsh<?php echo number_format($total, 0); ?></span></div>
        <a href="checkout.php" class="checkout-btn"><i class="fas fa-lock"></i> Proceed to Checkout</a>
        <div class="escrow-note"><i class="fas fa-shield-alt"></i> Your payment is protected by our secure escrow system. Funds are only released to the seller after you confirm delivery.</div>
        <a href="index.php" class="continue-shopping"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
    </div>
    <?php endif; ?>
</div>

<script>
    function updateQty(index, change) {
        const input = document.getElementById('qty-' + index);
        const display = document.getElementById('display-qty-' + index);
        let newVal = parseInt(input.value) + change;
        if (newVal < 1) newVal = 1;
        input.value = newVal;
        display.textContent = newVal;
        document.getElementById('update-form').click();
    }
</script>
</body>
</html>