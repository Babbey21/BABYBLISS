<?php
session_start();
require_once "config.php";

if (empty($_SESSION['cart'])) { header("Location: cart.php"); exit(); }
if (!isset($_SESSION['user_id'])) { header("Location: login.php?redirect=checkout.php"); exit(); }

$subtotal = 0;
foreach ($_SESSION['cart'] as $item) $subtotal += $item['price'] * $item['quantity'];
$shipping = $subtotal > 50000 ? 0 : 5000;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;

$error = "";
$success = "";
$order_id = 0;

if (isset($_POST['place_order'])) {
    $user_id = intval($_SESSION['user_id']);
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $street = mysqli_real_escape_string($conn, trim($_POST['street']));
    $city = mysqli_real_escape_string($conn, trim($_POST['city']));
    $country = mysqli_real_escape_string($conn, trim($_POST['country']));
    $postal_code = mysqli_real_escape_string($conn, trim($_POST['postal_code']));
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $notes = mysqli_real_escape_string($conn, trim($_POST['notes']));

    if (empty($first_name) || empty($last_name) || empty($email) || empty($street) || empty($city) || empty($country)) {
        $error = "Please fill in all required fields!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } else {
        $address_sql = "INSERT INTO addresses (user_id, label, street, city, country, postal_code, is_default) VALUES ($user_id, 'Order Address', '$street', '$city', '$country', '$postal_code', 0)";
        mysqli_query($conn, $address_sql);
        $address_id = mysqli_insert_id($conn);

        $order_sql = "INSERT INTO orders (user_id, address_id, status, total_amount, shipping_cost, tax_amount, notes, ordered_at) VALUES ($user_id, $address_id, 'pending_payment', $total, $shipping, $tax, '$notes', NOW())";
        if (mysqli_query($conn, $order_sql)) {
            $order_id = mysqli_insert_id($conn);
            foreach ($_SESSION['cart'] as $item) {
                $product_id = intval($item['id']);
                $qty = intval($item['quantity']);
                $unit_price = floatval($item['price']);
                // Get manufacturer_id for this product
                $m_res = mysqli_query($conn, "SELECT manufacturer_id FROM products WHERE id = $product_id");
                $m_id = mysqli_fetch_assoc($m_res)['manufacturer_id'] ?? 0;
                mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, manufacturer_id, quantity, unit_price) VALUES ($order_id, $product_id, $m_id, $qty, $unit_price)");
                mysqli_query($conn, "UPDATE products SET stock = stock - $qty, orders_count = orders_count + 1 WHERE id = $product_id");
            }
            // Payment record starts as "pending_confirmation" — it is only moved to
            // held_in_escrow once the buyer completes the gateway step (transaction.php).
            $pay_sql = "INSERT INTO payments (order_id, method, status, amount) VALUES ($order_id, '$payment_method', 'pending_confirmation', $total)";
            mysqli_query($conn, $pay_sql);
            $_SESSION['cart'] = [];
            mysqli_query($conn, "DELETE FROM cart_items WHERE user_id = $user_id");
            // Send the buyer into the payment gateway simulation to complete the transaction.
            header("Location: payment_gateway.php?order_id=$order_id");
            exit();
        } else {
            $error = "Error placing order: " . mysqli_error($conn);
        }
    }
}

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
    <title>Checkout - BabyBliss Marketplace</title>
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
        .back-link { display: flex; align-items: center; gap: 8px; color: var(--text-mid); text-decoration: none; font-size: 14px; font-weight: 600; padding: 10px 20px; border-radius: 10px; border: 2px solid #F0E4DC; transition: all 0.2s; }
        .back-link:hover { border-color: var(--rose); color: var(--deep-rose); }
        .checkout-container { max-width: 1200px; margin: 40px auto; padding: 0 48px; display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px; }
        .checkout-title { font-family: 'Playfair Display', serif; font-size: 32px; margin-bottom: 8px; }
        .checkout-subtitle { color: var(--text-light); font-size: 15px; margin-bottom: 28px; }
        .form-section { background: var(--white); border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: 0 4px 20px var(--shadow); }
        .section-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }
        .section-number { width: 32px; height: 32px; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; }
        .section-title { font-size: 18px; font-weight: 700; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-group.full { grid-column: 1 / -1; }
        label { display: block; font-size: 13px; font-weight: 600; color: var(--text-mid); margin-bottom: 6px; }
        label .required { color: var(--deep-rose); }
        input[type="text"], input[type="email"], input[type="tel"], select, textarea { width: 100%; padding: 13px 16px; border: 2px solid #F0E4DC; border-radius: 12px; font-family: 'DM Sans', sans-serif; font-size: 15px; color: var(--text-dark); background: var(--white); outline: none; transition: all 0.2s; }
        input:focus, select:focus, textarea:focus { border-color: var(--rose); box-shadow: 0 0 0 4px rgba(232,115,138,0.1); }
        textarea { resize: vertical; min-height: 80px; }
        .payment-options { display: flex; flex-direction: column; gap: 12px; }
        .payment-option { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border: 2px solid #F0E4DC; border-radius: 14px; cursor: pointer; transition: all 0.2s; }
        .payment-option:hover { border-color: var(--rose); }
        .payment-option input[type="radio"] { width: 20px; height: 20px; accent-color: var(--deep-rose); }
        .payment-option.selected { border-color: var(--rose); background: #FFF5F7; }
        .payment-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .payment-icon.cash { background: #E8F8F4; }
        .payment-icon.card { background: #FFF0F3; }
        .payment-icon.mpesa { background: #E0F0FF; }
        .payment-info { flex: 1; }
        .payment-name { font-weight: 600; font-size: 15px; }
        .payment-desc { font-size: 12px; color: var(--text-light); }
        .order-summary { background: var(--white); border-radius: 20px; padding: 28px; box-shadow: 0 4px 20px var(--shadow); height: fit-content; position: sticky; top: 100px; }
        .summary-title { font-family: 'Playfair Display', serif; font-size: 22px; margin-bottom: 20px; }
        .summary-item { display: flex; gap: 14px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #F0E4DC; }
        .summary-item:last-of-type { border-bottom: none; }
        .item-image { width: 60px; height: 60px; background: linear-gradient(135deg, #FFF0F5, #FFE4EA); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0; overflow: hidden; }
        .item-image img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }
        .item-details { flex: 1; }
        .item-name { font-weight: 600; font-size: 14px; margin-bottom: 2px; }
        .item-qty { font-size: 12px; color: var(--text-light); }
        .item-price { font-weight: 700; color: var(--deep-rose); font-size: 15px; }
        .summary-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 15px; color: var(--text-mid); }
        .summary-row.total { border-top: 2px solid #F0E4DC; font-size: 20px; font-weight: 700; color: var(--text-dark); margin-top: 8px; padding-top: 16px; }
        .btn-place-order { width: 100%; padding: 16px; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: var(--white); border: none; border-radius: 14px; font-size: 16px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.25s; margin-top: 20px; }
        .btn-place-order:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(196,77,101,0.35); }
        .alert { padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #EAF8F4; border: 1px solid #B0E0D0; color: #2E7D62; }
        .alert-error { background: #FFF0F3; border: 1px solid #F5B8C8; color: var(--deep-rose); }
        .escrow-info { background: linear-gradient(135deg, #EAF8F4, #D4F5EC); border-radius: 12px; padding: 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; }
        .escrow-info i { font-size: 24px; color: #2E7D62; }
        .escrow-info div { font-size: 13px; color: #2E7D62; line-height: 1.5; }
        .escrow-info strong { display: block; font-size: 14px; margin-bottom: 4px; }
        @media (max-width: 900px) { .checkout-container { grid-template-columns: 1fr; padding: 0 20px; } .header-main { padding: 0 20px; } .order-summary { position: static; } }
    </style>
</head>
<body>
<header>
    <div class="header-main">
        <a href="index.php" class="logo">
            <div class="logo-icon">🍼</div>
            <div class="logo-text">BabyBliss</div>
        </a>
        <a href="cart.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Cart</a>
    </div>
</header>

<div class="checkout-container">
    <div>
        <h1 class="checkout-title">Checkout 🛒</h1>
        <p class="checkout-subtitle">Complete your order by filling in your details below.</p>

        <div class="escrow-info">
            <i class="fas fa-shield-alt"></i>
            <div><strong>Secure Escrow Payment</strong>Your payment will be held securely and only released to the seller after you confirm delivery. Full buyer protection included.</div>
        </div>

        <?php if ($error): ?><div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div><?php endif; ?>

        <form method="POST" action="checkout.php">
            <div class="form-section">
                <div class="section-header"><div class="section-number">1</div><h3 class="section-title">Contact Information</h3></div>
                <div class="form-row">
                    <div class="form-group"><label>First Name <span class="required">*</span></label><input type="text" name="first_name" placeholder="John" required value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>"/></div>
                    <div class="form-group"><label>Last Name <span class="required">*</span></label><input type="text" name="last_name" placeholder="Doe" required/></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Email <span class="required">*</span></label><input type="email" name="email" placeholder="john@example.com" required value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>"/></div>
                    <div class="form-group"><label>Phone Number</label><input type="tel" name="phone" placeholder="+255 700 000 000"/></div>
                </div>
            </div>

            <div class="form-section">
                <div class="section-header"><div class="section-number">2</div><h3 class="section-title">Shipping Address</h3></div>
                <div class="form-group full"><label>Street Address <span class="required">*</span></label><input type="text" name="street" placeholder="123 Blossom Lane" required/></div>
                <div class="form-row">
                    <div class="form-group"><label>City <span class="required">*</span></label><input type="text" name="city" placeholder="Dar es Salaam" required/></div>
                    <div class="form-group"><label>Country <span class="required">*</span></label><select name="country" required><option value="">Select country...</option><option value="Tanzania">🇹🇿 Tanzania</option><option value="Kenya">🇰🇪 Kenya</option><option value="Uganda">🇺🇬 Uganda</option><option value="Nigeria">🇳🇬 Nigeria</option><option value="South Africa">🇿🇦 South Africa</option><option value="USA">🇺🇸 United States</option><option value="UK">🇬🇧 United Kingdom</option><option value="China">🇨🇳 China</option><option value="India">🇮🇳 India</option><option value="Other">🌍 Other</option></select></div>
                </div>
                <div class="form-group"><label>Postal Code</label><input type="text" name="postal_code" placeholder="10001"/></div>
                <div class="form-group full"><label>Order Notes (Optional)</label><textarea name="notes" placeholder="Special instructions for delivery..."></textarea></div>
            </div>

            <div class="form-section">
                <div class="section-header"><div class="section-number">3</div><h3 class="section-title">Payment Method</h3></div>
                <div class="payment-options">
                    <label class="payment-option selected">
                        <input type="radio" name="payment_method" value="cash_on_delivery" checked/>
                        <div class="payment-icon cash">💵</div>
                        <div class="payment-info"><div class="payment-name">Cash on Delivery</div><div class="payment-desc">Pay when you receive your order</div></div>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="m_pesa"/>
                        <div class="payment-icon mpesa">📱</div>
                        <div class="payment-info"><div class="payment-name">M-Pesa</div><div class="payment-desc">Mobile money payment (held in escrow)</div></div>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="credit_card"/>
                        <div class="payment-icon card">💳</div>
                        <div class="payment-info"><div class="payment-name">Credit / Debit Card</div><div class="payment-desc">Visa, Mastercard (held in escrow)</div></div>
                    </label>
                </div>
            </div>

            <button type="submit" name="place_order" class="btn-place-order"><i class="fas fa-lock"></i> Place Order — Tsh<?= number_format($total, 0) ?></button>
        </form>
    </div>

    <div class="order-summary">
        <h2 class="summary-title">Order Summary</h2>
        <?php foreach ($_SESSION['cart'] as $item): ?>
        <div class="summary-item">
            <div class="item-image"><?php $img = $item['image']; if (isImagePath($img)) { echo '<img src="' . htmlspecialchars($img) . '" alt="">'; } else { echo $img; } ?></div>
            <div class="item-details"><div class="item-name"><?= htmlspecialchars($item['name']) ?></div><div class="item-qty">Qty: <?= $item['quantity'] ?></div></div>
            <div class="item-price">Tsh<?= number_format($item['price'] * $item['quantity'], 0) ?></div>
        </div>
        <?php endforeach; ?>
        <div class="summary-row"><span>Subtotal</span><span>Tsh<?= number_format($subtotal, 0) ?></span></div>
        <div class="summary-row"><span>Shipping</span><span><?= $shipping == 0 ? 'FREE' : 'Tsh' . number_format($shipping, 0) ?></span></div>
        <div class="summary-row"><span>Tax (8%)</span><span>Tsh<?= number_format($tax, 0) ?></span></div>
        <div class="summary-row total"><span>Total</span><span>Tsh<?= number_format($total, 0) ?></span></div>
    </div>
</div>

<script>
    document.querySelectorAll('.payment-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
            this.closest('.payment-option').classList.add('selected');
        });
    });
</script>
</body>
</html>