<?php
session_start();
require_once "config.php";

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = intval($_SESSION['user_id']);
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Customer');

$orders_query = "SELECT o.*, a.street, a.city, a.country, a.postal_code, p.method as payment_method, p.status as payment_status, p.paid_at FROM orders o LEFT JOIN addresses a ON o.address_id = a.id LEFT JOIN payments p ON o.id = p.order_id WHERE o.user_id = $user_id ORDER BY o.ordered_at DESC";
$orders = mysqli_query($conn, $orders_query);
$total_orders = mysqli_num_rows($orders);

$total_spent = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE user_id = $user_id AND status != 'cancelled'"))['total'] ?? 0;
$completed_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE user_id = $user_id AND status = 'completed'"))['count'] ?? 0;
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

function getStatusClass($status) {
    switch(strtolower($status ?? 'pending')) {
        case 'pending': return ['status-pending', 'clock'];
        case 'processing': return ['status-processing', 'cog'];
        case 'shipped': return ['status-shipped', 'truck'];
        case 'delivered': return ['status-completed', 'check-circle'];
        case 'completed': return ['status-completed', 'check-circle'];
        case 'cancelled': return ['status-cancelled', 'times-circle'];
        case 'disputed': return ['status-cancelled', 'exclamation-circle'];
        default: return ['status-pending', 'clock'];
    }
}

function getPayClass($status) {
    switch(strtolower($status ?? 'pending')) {
        case 'held_in_escrow': return 'pay-pending';
        case 'released': return 'pay-paid';
        case 'refunded': return 'pay-failed';
        default: return 'pay-pending';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - BabyBliss Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        :root { --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A; --deep-rose: #C44D65; --mint: #A8D8C8; --mint-dark: #5FB8A0; --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A; --shadow: rgba(196,77,101,0.12); }
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
        .icon-btn.active { color: var(--deep-rose); background: var(--cream); }
        .badge { position: absolute; top: 2px; right: 2px; background: var(--deep-rose); color: var(--white); font-size: 10px; font-weight: 600; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .btn-login { padding: 9px 22px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; border: 2px solid var(--rose); color: var(--rose); background: transparent; transition: all 0.2s; text-decoration: none; }
        .btn-login:hover { background: var(--rose); color: var(--white); }
        .orders-container { max-width: 1100px; margin: 40px auto; padding: 0 48px; }
        .page-header { margin-bottom: 32px; }
        .page-header h1 { font-family: 'Playfair Display', serif; font-size: 36px; margin-bottom: 8px; }
        .page-header p { color: var(--text-light); font-size: 15px; }
        .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px; }
        .stat-box { background: var(--white); border-radius: 20px; padding: 24px; box-shadow: 0 4px 20px var(--shadow); display: flex; align-items: center; gap: 16px; }
        .stat-box-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        .stat-box-icon.pink { background: linear-gradient(135deg, #FFE4EC, #FFB8CC); }
        .stat-box-icon.teal { background: linear-gradient(135deg, #D4F5EC, #A8E0D0); }
        .stat-box-icon.gold { background: linear-gradient(135deg, #FFF0C0, #FFE080); }
        .stat-box-value { font-size: 26px; font-weight: 800; color: var(--text-dark); }
        .stat-box-label { font-size: 13px; color: var(--text-light); margin-top: 2px; }
        .order-card { background: var(--white); border-radius: 20px; box-shadow: 0 4px 20px var(--shadow); margin-bottom: 24px; overflow: hidden; }
        .order-header { padding: 20px 28px; border-bottom: 1px solid #F0E4DC; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
        .order-number { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: var(--deep-rose); }
        .order-date { font-size: 13px; color: var(--text-light); }
        .order-status { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pending { background: #FFF8E0; color: #B8860B; }
        .status-processing { background: #E0F0FF; color: #1E6FD9; }
        .status-shipped { background: #E8F4FF; color: #0066CC; }
        .status-completed { background: #EAF8F4; color: #2E7D62; }
        .status-cancelled { background: #FFF0F3; color: var(--deep-rose); }
        .order-body { padding: 24px 28px; }
        .order-items { display: flex; flex-direction: column; gap: 16px; }
        .order-item { display: flex; align-items: center; gap: 16px; padding: 12px 0; border-bottom: 1px solid #F0E4DC; }
        .order-item:last-child { border-bottom: none; }
        .item-img { width: 60px; height: 60px; background: linear-gradient(135deg, #FFF0F5, #FFE4EA); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 28px; flex-shrink: 0; overflow: hidden; }
        .item-img img { width: 100%; height: 100%; object-fit: cover; }
        .item-info { flex: 1; }
        .item-name { font-weight: 600; font-size: 15px; margin-bottom: 3px; }
        .item-meta { font-size: 13px; color: var(--text-light); }
        .item-price { font-weight: 700; color: var(--deep-rose); font-size: 16px; }
        .order-footer { padding: 20px 28px; background: #FFF8F5; border-top: 1px solid #F0E4DC; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .order-total { font-size: 18px; font-weight: 700; }
        .order-total span { color: var(--deep-rose); }
        .pay-status { display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; border-radius: 16px; font-size: 12px; font-weight: 600; }
        .pay-paid { background: #EAF8F4; color: #2E7D62; }
        .pay-pending { background: #FFF8E0; color: #B8860B; }
        .pay-failed { background: #FFF0F3; color: var(--deep-rose); }
        .shipping-info { margin-top: 16px; padding: 16px; background: var(--cream); border-radius: 12px; font-size: 13px; color: var(--text-mid); line-height: 1.7; }
        .shipping-info strong { color: var(--text-dark); display: block; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .escrow-badge { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #EAF8F4, #D4F5EC); color: #2E7D62; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 8px; }
        .btn-confirm { padding: 10px 20px; background: linear-gradient(135deg, var(--mint-dark), #3A9E88); color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-dispute { padding: 10px 20px; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: white; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; margin-left: 8px; }
        .empty-orders { text-align: center; padding: 80px 20px; }
        .empty-orders-icon { font-size: 80px; margin-bottom: 20px; opacity: 0.5; }
        .empty-orders h3 { font-family: 'Playfair Display', serif; font-size: 24px; margin-bottom: 12px; }
        .empty-orders p { color: var(--text-light); margin-bottom: 24px; }
        .btn-shop { display: inline-flex; align-items: center; gap: 8px; padding: 14px 28px; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: var(--white); border: none; border-radius: 14px; font-size: 15px; font-weight: 700; text-decoration: none; transition: all 0.25s; }
        .btn-shop:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(196,77,101,0.35); }
        @media (max-width: 768px) { .orders-container { padding: 0 20px; } .header-main { padding: 0 20px; } .stats-row { grid-template-columns: 1fr; } .order-header { flex-direction: column; align-items: flex-start; } }
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
            <a href="cart.php" class="icon-btn"><i class="fas fa-shopping-cart"></i><?php if ($cart_count > 0): ?><span class="badge"><?php echo $cart_count; ?></span><?php endif; ?></a>
            <a href="orders.php" class="icon-btn active" title="My Orders"><i class="fas fa-clipboard-list"></i></a>
            <span style="font-size:14px; color:var(--text-mid);">👋 <?php echo $user_name; ?></span>
            <a href="logout.php" class="btn-login">Logout</a>
        </div>
    </div>
</header>

<div class="orders-container">
    <div class="page-header">
        <h1>My Orders 📦</h1>
        <p>Track your purchases and manage deliveries</p>
    </div>

    <div class="stats-row">
        <div class="stat-box"><div class="stat-box-icon pink">📦</div><div><div class="stat-box-value"><?php echo $total_orders; ?></div><div class="stat-box-label">Total Orders</div></div></div>
        <div class="stat-box"><div class="stat-box-icon teal">✅</div><div><div class="stat-box-value"><?php echo $completed_count; ?></div><div class="stat-box-label">Completed</div></div></div>
        <div class="stat-box"><div class="stat-box-icon gold">💰</div><div><div class="stat-box-value">Tsh<?php echo number_format($total_spent, 0); ?></div><div class="stat-box-label">Total Spent</div></div></div>
    </div>

    <?php if ($total_orders === 0): ?>
        <div class="empty-orders">
            <div class="empty-orders-icon">📦</div>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="index.php" class="btn-shop"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
        </div>
    <?php else: ?>
        <?php mysqli_data_seek($orders, 0); while ($o = mysqli_fetch_assoc($orders)): 
            $oid = intval($o['id']);
            $items_query = "SELECT oi.*, p.name as product_name, p.image_url, m.company_name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id LEFT JOIN manufacturers m ON oi.manufacturer_id = m.id WHERE oi.order_id = $oid";
            $items = mysqli_query($conn, $items_query);
            list($status_class, $status_icon) = getStatusClass($o['status']);
            $pay_class = getPayClass($o['payment_status']);
            $pay_status = strtolower($o['payment_status'] ?? 'pending');
        ?>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <div class="order-number">Order #<?php echo $oid; ?></div>
                    <div class="order-date"><i class="fas fa-calendar-alt" style="margin-right:4px;"></i><?php echo date('F j, Y \a\t g:i A', strtotime($o['ordered_at'])); ?></div>
                </div>
                <span class="order-status <?php echo $status_class; ?>"><i class="fas fa-<?php echo $status_icon; ?>"></i> <?php echo ucfirst(htmlspecialchars($o['status'] ?? 'pending')); ?></span>
            </div>
            <div class="order-body">
                <div class="order-items">
                    <?php while ($it = mysqli_fetch_assoc($items)): ?>
                    <div class="order-item">
                        <div class="item-img"><?php if (!empty($it['image_url']) && file_exists('uploads/products/' . $it['image_url'])): ?><img src="uploads/products/<?php echo htmlspecialchars($it['image_url']); ?>" alt=""/><?php else: ?>🧸<?php endif; ?></div>
                        <div class="item-info">
                            <div class="item-name"><?php echo htmlspecialchars($it['product_name'] ?? 'Product #' . $it['product_id']); ?></div>
                            <div class="item-meta">Qty: <?php echo intval($it['quantity']); ?> · Seller: <?php echo htmlspecialchars($it['company_name'] ?? 'Unknown'); ?> · Unit: Tsh<?php echo number_format($it['unit_price'], 0); ?></div>
                        </div>
                        <div class="item-price">Tsh<?php echo number_format($it['unit_price'] * $it['quantity'], 0); ?></div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php if (!empty($o['street'])): ?>
                <div class="shipping-info">
                    <strong><i class="fas fa-map-marker-alt" style="margin-right:6px; color:var(--rose);"></i>Shipping Address</strong>
                    <?php echo htmlspecialchars($o['street']); ?>, <?php echo htmlspecialchars($o['city']); ?>, <?php echo htmlspecialchars($o['country']); ?> <?php echo htmlspecialchars($o['postal_code'] ?? ''); ?>
                </div>
                <?php endif; ?>
                <?php if (!empty($o['notes'])): ?>
                <div class="shipping-info" style="margin-top:8px;">
                    <strong><i class="fas fa-sticky-note" style="margin-right:6px; color:var(--rose);"></i>Order Notes</strong>
                    <?php echo htmlspecialchars($o['notes']); ?>
                </div>
                <?php endif; ?>
                <div class="escrow-badge"><i class="fas fa-shield-alt"></i> Payment protected by escrow</div>
            </div>
            <div class="order-footer">
                <div>
                    <span class="pay-status <?php echo $pay_class; ?>"><i class="fas fa-<?php echo ($pay_status == 'released') ? 'check-circle' : 'clock'; ?>"></i> <?php echo ucfirst(htmlspecialchars(str_replace('_', ' ', $pay_status))); ?> · <?php echo htmlspecialchars($o['payment_method'] ?? 'N/A'); ?></span>
                    <?php if (!empty($o['paid_at'])): ?><span style="font-size:12px; color:var(--text-light); margin-left:8px;">Paid on <?php echo date('M j, Y', strtotime($o['paid_at'])); ?></span><?php endif; ?>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="order-total">Total: <span>Tsh<?php echo number_format($o['total_amount'], 0); ?></span></div>
                    <?php if ($o['status'] == 'delivered'): ?>
                    <a href="orders.php?confirm=<?php echo $oid; ?>" class="btn-confirm"><i class="fas fa-check"></i> Confirm Delivery</a>
                    <a href="dispute.php?order=<?php echo $oid; ?>" class="btn-dispute"><i class="fas fa-exclamation-triangle"></i> Open Dispute</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
</body>
</html>