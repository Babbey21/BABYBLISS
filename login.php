
<?php
session_start();
require_once "config.php";
 
$error = "";
 
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
 
    $query = "SELECT u.*, m.id as manufacturer_id, m.verification_status, m.company_name 
              FROM users u 
              LEFT JOIN manufacturers m ON u.id = m.user_id 
              WHERE u.email = '$email' AND u.is_active = 1";
    $result = mysqli_query($conn, $query);
 
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
 
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['manufacturer_id'] = $user['manufacturer_id'] ?? null;
 
            // Restore this customer's saved cart from the database and merge it with
            // whatever is already in the session (e.g. items added while browsing as a guest).
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            $saved_cart = mysqli_query($conn, "SELECT product_id, product_name, price, image, quantity FROM cart_items WHERE user_id = " . intval($user['id']));
            if ($saved_cart) {
                while ($citem = mysqli_fetch_assoc($saved_cart)) {
                    $found = false;
                    foreach ($_SESSION['cart'] as &$item) {
                        if ($item['id'] == $citem['product_id'] && $item['name'] == $citem['product_name']) {
                            $item['quantity'] += (int)$citem['quantity'];
                            $found = true;
                            break;
                        }
                    }
                    unset($item);
                    if (!$found) {
                        $_SESSION['cart'][] = [
                            'id' => (int)$citem['product_id'],
                            'name' => $citem['product_name'],
                            'price' => (float)$citem['price'],
                            'image' => $citem['image'],
                            'quantity' => (int)$citem['quantity'],
                        ];
                    }
                }
            }
            $_SESSION['cart_synced'] = true;
 
            if ($user['role'] === 'admin') {
                
                header("Location: admin.php");
            } elseif ($user['role'] === 'manufacturer') {
                if ($user['verification_status'] === 'verified') {
                    header("Location: manufacturer_dashboard.php");
                } else {
                    
                    header("Location: manufacturer_pending.php");
                    
                }
            } else {
                header("Location: index.php");
            }
            exit();
        } else
        {
            echo '<div class="alert alert-danger w-50 mt-3" role="alert"> Oops!! failed </div>';
        }
    } 
    else {
        $error = 'Oops!! failed';
        ;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In - BabyBliss Marketplace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
 
    <style>
        :root { --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A; --deep-rose: #C44D65; --mint-dark: #5FB8A0; --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { margin: 0; display: flex; min-height: 100vh; font-family: 'DM Sans', sans-serif; background: var(--cream); }
        .left-panel { width: 50%; background: linear-gradient(135deg, var(--deep-rose) 0%, #8B3A50 50%, #5A1A30 100%); display: flex; align-items: center; justify-content: center; position: relative; flex-direction: column; }
        .left-panel::after { content: ''; position: absolute; inset: 0; background: rgba(0,0,0,0.2); }
        .left-content { position: relative; z-index: 1; text-align: center; color: white; padding: 40px; }
        .left-content h2 { font-family: 'Playfair Display', serif; font-size: 32px; margin-bottom: 16px; }
        .left-content p { font-size: 16px; line-height: 1.6; opacity: 0.9; }
        .right-panel { width: 50%; background: var(--cream); display: flex; align-items: center; justify-content: center; padding: 40px; }
        .form-box { width: 100%; max-width: 400px; }
        .back-link { display: flex; align-items: center; gap: 6px; color: var(--text-light); text-decoration: none; font-size: 14px; margin-bottom: 28px; transition: color 0.2s; }
        .back-link:hover { color: var(--rose); }
        .form-header h1 { font-family: 'Playfair Display', serif; font-size: 34px; color: var(--text-dark); margin-bottom: 7px; }
        .form-header p { color: var(--text-light); font-size: 15px; margin-bottom: 28px; }
        .form-header a { color: var(--rose); font-weight: 600; text-decoration: none; }
        .error-msg { background: #FFF0F3; color: var(--deep-rose); padding: 14px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; border: 1px solid var(--blush); display: flex; align-items: center; gap: 8px; }
        .input-wrap { position: relative; margin-bottom: 18px; }
        .input-wrap i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-light); font-size: 15px; }
        input { width: 100%; padding: 14px 14px 14px 45px; border: 2px solid #F0E4DC; border-radius: 13px; outline: none; box-sizing: border-box; font-family: 'DM Sans', sans-serif; font-size: 15px; color: var(--text-dark); background: var(--white); transition: all 0.2s; }
        input:focus { border-color: var(--rose); box-shadow: 0 0 0 4px rgba(232,115,138,0.1); }
        .btn-primary { width: 100%; padding: 16px; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: white; border: none; border-radius: 14px; font-size: 16px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.25s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(196,77,101,0.35); }
        .role-tabs { display: flex; gap: 8px; margin-bottom: 24px; }
        .role-tab { flex: 1; padding: 10px; text-align: center; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; border: 2px solid #F0E4DC; background: var(--white); color: var(--text-mid); transition: all 0.2s; }
        .role-tab:hover { border-color: var(--rose); color: var(--rose); }
        .role-tab.active { background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: white; border-color: var(--rose); }
        .admin-note { text-align: center; margin-top: 20px; padding: 12px; background: linear-gradient(135deg, #FFF0F3, #FFE4EA); border-radius: 12px; border: 1px dashed var(--blush); }
        .admin-note p { font-size: 13px; color: var(--text-mid); margin: 0; }
        .admin-note strong { color: var(--deep-rose); }
        @media (max-width: 768px) { body { flex-direction: column; } .left-panel, .right-panel { width: 100%; min-height: 300px; } }
    </style>
</head>
<body>
<div class="left-panel">
    <div class="left-content">
        <div style="font-size: 64px; margin-bottom: 20px;">🍼</div>
        <h2>Welcome Back!</h2>
        <p>Sign in to access your account, track orders, or manage your store.</p>
    </div>
</div>
<div class="right-panel">
    <div class="form-box">
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Shop</a>
        <div class="form-header">
            <h1>Sign In 👋</h1>
            <p>Don't have an account? <a href="register.php">Create one free</a></p>
        </div>
        <?php if($error): ?>
           <div class="alert alert-danger w-50 mt-3" role="alert"> Oops!! failed </div>'
         <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="input-wrap"><i class="fas fa-envelope"></i><input type="email" name="email" placeholder="you@example.com" required></div>
            <div class="input-wrap"><i class="fas fa-lock"></i><input type="password" name="password" placeholder="Your password" required></div>
            <button type="submit" name="login" class="btn-primary"><i class="fas fa-sign-in-alt"></i> Sign In</button>
        </form>
        <div class="admin-note">
            <p> <strong>Demo:</strong> admin@babybliss.com / password<br> <strong>Demo:</strong> manufacturer@demo.com / password<br>🛒 <strong>Demo:</strong> customer@demo.com / password</p>
        </div>
    </div>
</div>
</body>
</html>