<?php
session_start();
require_once "config.php";
 
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
 
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : (isset($_POST['order_id']) ? intval($_POST['order_id']) : 0);
$user_id  = intval($_SESSION['user_id']);
 
// Load the order + payment record, making sure it belongs to the logged-in buyer.
$res = mysqli_query($conn, "SELECT o.id as order_id, o.status as order_status, o.total_amount,
                                    p.id as payment_id, p.method, p.status as payment_status, p.amount, p.transaction_id
                             FROM orders o JOIN payments p ON p.order_id = o.id
                             WHERE o.id = $order_id AND o.user_id = $user_id");
$row = $res ? mysqli_fetch_assoc($res) : null;
 
if (!$row) { header("Location: orders.php"); exit(); }
 
$error = "";
$confirmed = ($row['payment_status'] === 'held_in_escrow' || $row['payment_status'] === 'released');
 
function generateTransactionId($prefix) {
    return $prefix . '-' . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8)) . '-' . date('ymd');
}
 
// ---- Handle the gateway submission ----
if (isset($_POST['confirm_payment']) && !$confirmed) {
    $method = $row['method'];
 
    if ($method === 'cash_on_delivery') {
        $txn = generateTransactionId('COD');
        $status = 'held_in_escrow';
        $message = 'Cash on Delivery confirmed. Amount will be collected on delivery.';
    } elseif ($method === 'm_pesa') {
        $phone = trim($_POST['mpesa_phone'] ?? '');
        $pin   = trim($_POST['mpesa_pin'] ?? '');
        if (!preg_match('/^0?7\d{8}$/', $phone)) {
            $error = "Please enter a valid Tanzanian mobile number (e.g. 0712345678).";
        } elseif (!preg_match('/^\d{4}$/', $pin)) {
            $error = "M-Pesa PIN must be exactly 4 digits.";
        } else {
            $txn = generateTransactionId('MP');
            $status = 'held_in_escrow';
            $message = "M-Pesa payment of Tsh" . number_format($row['amount'], 0) . " confirmed from $phone.";
        }
    } elseif ($method === 'credit_card') {
        $card_number = preg_replace('/\s+/', '', $_POST['card_number'] ?? '');
        $expiry      = trim($_POST['card_expiry'] ?? '');
        $cvv         = trim($_POST['card_cvv'] ?? '');
        if (!preg_match('/^\d{16}$/', $card_number)) {
            $error = "Card number must be 16 digits.";
        } elseif (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
            $error = "Expiry date must be in MM/YY format.";
        } elseif (!preg_match('/^\d{3,4}$/', $cvv)) {
            $error = "CVV must be 3 or 4 digits.";
        } else {
            $txn = generateTransactionId('CC');
            $status = 'held_in_escrow';
            $message = "Card payment of Tsh" . number_format($row['amount'], 0) . " authorized (card ending " . substr($card_number, -4) . ").";
        }
    } else {
        $error = "Unrecognized payment method.";
    }
 
    if (!$error) {
        $txn_safe = mysqli_real_escape_string($conn, $txn);
        $msg_safe = mysqli_real_escape_string($conn, $message);
        mysqli_query($conn, "UPDATE payments SET status='$status', transaction_id='$txn_safe', gateway_message='$msg_safe', paid_at=NOW() WHERE id=" . intval($row['payment_id']));
        mysqli_query($conn, "UPDATE orders SET status='processing' WHERE id=$order_id");
        header("Location: payment_gateway.php?order_id=$order_id");
        exit();
    }
}
 
// Refresh row after a successful confirmation
if (!$error) {
    $res = mysqli_query($conn, "SELECT o.id as order_id, o.status as order_status, o.total_amount,
                                        p.id as payment_id, p.method, p.status as payment_status, p.amount, p.transaction_id, p.gateway_message
                                 FROM orders o JOIN payments p ON p.order_id = o.id
                                 WHERE o.id = $order_id AND o.user_id = $user_id");
    $row = mysqli_fetch_assoc($res);
    $confirmed = ($row['payment_status'] === 'held_in_escrow' || $row['payment_status'] === 'released');
}
 
$method_labels = ['cash_on_delivery' => 'Cash on Delivery', 'm_pesa' => 'M-Pesa', 'credit_card' => 'Credit / Debit Card'];
$method_label = $method_labels[$row['method']] ?? $row['method'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Gateway - BabyBliss Marketplace</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
    :root { --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A; --deep-rose: #C44D65; --mint-dark: #5FB8A0; --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A; --shadow: rgba(196,77,101,0.12); }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DM Sans', sans-serif; background:var(--cream); color:var(--text-dark); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; }
    .gateway-card { background:var(--white); border-radius:24px; padding:40px; max-width:440px; width:100%; box-shadow:0 20px 60px var(--shadow); }
    .logo { display:flex; align-items:center; gap:10px; justify-content:center; margin-bottom:24px; text-decoration:none; }
    .logo-icon { width:44px; height:44px; background:linear-gradient(135deg,var(--blush),var(--deep-rose)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; }
    .logo-text { font-family:'Playfair Display', serif; font-size:24px; font-weight:700; color:var(--deep-rose); }
    h1 { font-family:'Playfair Display', serif; font-size:22px; text-align:center; margin-bottom:6px; }
    .amount { text-align:center; font-size:28px; font-weight:700; color:var(--deep-rose); margin-bottom:20px; }
    .method-badge { display:flex; align-items:center; justify-content:center; gap:8px; background:var(--cream); border-radius:10px; padding:10px; font-size:13px; font-weight:600; color:var(--text-mid); margin-bottom:24px; }
    .form-group { margin-bottom:16px; }
    label { display:block; font-size:13px; font-weight:600; color:var(--text-mid); margin-bottom:6px; }
    input { width:100%; padding:13px 16px; border:2px solid #F0E4DC; border-radius:12px; font-family:'DM Sans', sans-serif; font-size:15px; outline:none; transition:all .2s; }
    input:focus { border-color:var(--rose); box-shadow:0 0 0 4px rgba(232,115,138,.1); }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .btn-pay { width:100%; padding:16px; background:linear-gradient(135deg,var(--rose),var(--deep-rose)); color:#fff; border:none; border-radius:14px; font-size:16px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; margin-top:8px; transition:all .25s; }
    .btn-pay:hover { transform:translateY(-2px); box-shadow:0 10px 30px rgba(196,77,101,.35); }
    .alert-error { background:#FFF0F3; border:1px solid #F5B8C8; color:var(--deep-rose); padding:12px 16px; border-radius:12px; font-size:13px; margin-bottom:16px; }
    .cod-note { font-size:14px; color:var(--text-mid); text-align:center; margin-bottom:24px; line-height:1.6; }
    .success-icon { width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg,#A8D8C8,#5FB8A0); display:flex; align-items:center; justify-content:center; font-size:40px; color:#fff; margin:0 auto 20px; }
    .receipt { background:var(--cream); border-radius:14px; padding:18px 20px; margin-bottom:24px; }
    .receipt-row { display:flex; justify-content:space-between; font-size:13px; padding:6px 0; color:var(--text-mid); }
    .receipt-row strong { color:var(--text-dark); }
    .btn-secondary { display:block; text-align:center; width:100%; padding:14px; border:2px solid var(--rose); color:var(--deep-rose); border-radius:14px; text-decoration:none; font-weight:700; font-size:15px; transition:all .2s; }
    .btn-secondary:hover { background:var(--rose); color:#fff; }
    .secure-note { display:flex; align-items:center; gap:8px; justify-content:center; font-size:12px; color:var(--text-light); margin-top:18px; }
</style>
</head>
<body>
<div class="gateway-card">
    <a href="index.php" class="logo"><div class="logo-icon">🍼</div><div class="logo-text">BabyBliss</div></a>
 
    <?php if ($confirmed): ?>
        <div class="success-icon"><i class="fas fa-check"></i></div>
        <h1>Payment Confirmed</h1>
        <p style="text-align:center;color:var(--text-light);font-size:13px;margin-bottom:20px;">Your order is now being processed.</p>
        <div class="receipt">
            <div class="receipt-row"><span>Order #</span><strong>#<?= (int)$row['order_id'] ?></strong></div>
            <div class="receipt-row"><span>Method</span><strong><?= htmlspecialchars($method_label) ?></strong></div>
            <div class="receipt-row"><span>Amount</span><strong>Tsh<?= number_format($row['amount'], 0) ?></strong></div>
            <div class="receipt-row"><span>Transaction ID</span><strong><?= htmlspecialchars($row['transaction_id']) ?></strong></div>
        </div>
        <a href="orders.php" class="btn-secondary"><i class="fas fa-list"></i> View My Orders</a>
        <div class="secure-note"><i class="fas fa-shield-alt"></i> Funds held in escrow until delivery is confirmed</div>
 
    <?php else: ?>
        <h1>Complete Payment</h1>
        <div class="amount">Tsh<?= number_format($row['amount'], 0) ?></div>
        <div class="method-badge"><i class="fas fa-lock"></i> Order #<?= (int)$row['order_id'] ?> • <?= htmlspecialchars($method_label) ?></div>
 
        <?php if ($error): ?><div class="alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div><?php endif; ?>
 
        <form method="POST" action="payment_gateway.php">
            <input type="hidden" name="order_id" value="<?= (int)$order_id ?>"/>
 
            <?php if ($row['method'] === 'm_pesa'): ?>
                <div class="form-group">
                    <label>M-Pesa Phone Number</label>
                    <input type="text" name="mpesa_phone" placeholder="0712345678" required maxlength="10"/>
                </div>
                <div class="form-group">
                    <label>Enter M-Pesa PIN</label>
                    <input type="password" name="mpesa_pin" placeholder="••••" required maxlength="4" inputmode="numeric"/>
                </div>
            <?php elseif ($row['method'] === 'credit_card'): ?>
                <div class="form-group">
                    <label>Card Number</label>
                    <input type="text" name="card_number" placeholder="4242 4242 4242 4242" required maxlength="19"/>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Expiry (MM/YY)</label><input type="text" name="card_expiry" placeholder="09/28" required maxlength="5"/></div>
                    <div class="form-group"><label>CVV</label><input type="password" name="card_cvv" placeholder="123" required maxlength="4"/></div>
                </div>
            <?php else: ?>
                <p class="cod-note"><i class="fas fa-truck" style="color:var(--mint-dark);"></i><br/>You will pay in cash when your order is delivered. Click below to confirm your order.</p>
            <?php endif; ?>
 
            <button type="submit" name="confirm_payment" class="btn-pay">
                <i class="fas fa-lock"></i> <?= $row['method'] === 'cash_on_delivery' ? 'Confirm Order' : 'Confirm & Pay' ?>
            </button>
        </form>
        <div class="secure-note"><i class="fas fa-shield-alt"></i> This is a simulated gateway for demonstration purposes</div>
    <?php endif; ?>
</div>
</body>
</html>