<?php
session_start();
require_once "config.php"; 

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; 

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password_hash'])) {
            // ✅ Password sahihi!
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];
            
            // 🔥 MUHIMU: Angalia role na uelekeze
            if ($user['role'] === 'admin') {
                header("Location: admin.php");  // Admin dashboard
            } else {
                header("Location: index.php");   // Normal shop
            }
            exit();
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "Account not found: These credentials do not exist.";
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Sign In - BabyBliss</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A;
            --deep-rose: #C44D65; --mint-dark: #5FB8A0;
            --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            margin: 0; 
            display: flex; 
            min-height: 100vh; 
            font-family: 'DM Sans', sans-serif; 
            background: var(--cream);
        }
        
        .left-panel {
            width: 50%;
            background: url("img/📚 Toddler Learning Activities That Turn Play Into Progress.jpg") center/cover no-repeat;
            display: flex; 
            align-items: center; 
            justify-content: center;
            position: relative;
        }
        .left-panel::after { 
            content: ''; 
            position: absolute; 
            inset: 0; 
            background: rgba(0,0,0,0.2); 
        }

        .right-panel { 
            width: 50%; 
            background: var(--cream); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 40px; 
        }
        .form-box { 
            width: 100%; 
            max-width: 400px; 
        }
        
        .back-link { 
            display: flex; 
            align-items: center; 
            gap: 6px; 
            color: var(--text-light); 
            text-decoration: none; 
            font-size: 14px; 
            margin-bottom: 28px; 
            transition: color 0.2s; 
        }
        .back-link:hover { color: var(--rose); }
        
        .form-header h1 { 
            font-family: 'Playfair Display', serif;
            font-size: 34px; 
            color: var(--text-dark); 
            margin-bottom: 7px; 
        }
        .form-header p { 
            color: var(--text-light); 
            font-size: 15px; 
            margin-bottom: 28px;
        }
        .form-header a { 
            color: var(--rose); 
            font-weight: 600; 
            text-decoration: none; 
        }
        
        .error-msg { 
            background: #FFF0F3; 
            color: var(--deep-rose); 
            padding: 14px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            font-size: 14px; 
            border: 1px solid var(--blush);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .input-wrap { 
            position: relative; 
            margin-bottom: 18px; 
        }
        .input-wrap i { 
            position: absolute; 
            left: 15px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: var(--text-light); 
            font-size: 15px;
        }
        input { 
            width: 100%; 
            padding: 14px 14px 14px 45px; 
            border: 2px solid #F0E4DC; 
            border-radius: 13px; 
            outline: none; 
            box-sizing: border-box;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: var(--text-dark);
            background: var(--white);
            transition: all 0.2s;
        }
        input:focus { 
            border-color: var(--rose); 
            box-shadow: 0 0 0 4px rgba(232,115,138,0.1);
        }

        .btn-primary {
            width: 100%; 
            padding: 16px; 
            background: linear-gradient(135deg, var(--rose), var(--deep-rose));
            color: white; 
            border: none; 
            border-radius: 14px; 
            font-size: 16px; 
            font-weight: 700; 
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s;
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 30px rgba(196,77,101,0.35); 
        }
        
        .admin-note {
            text-align: center;
            margin-top: 20px;
            padding: 12px;
            background: linear-gradient(135deg, #FFF0F3, #FFE4EA);
            border-radius: 12px;
            border: 1px dashed var(--blush);
        }
        .admin-note p {
            font-size: 13px;
            color: var(--text-mid);
            margin: 0;
        }
        .admin-note strong {
            color: var(--deep-rose);
        }
        
        @media (max-width: 768px) { 
            body { flex-direction: column; } 
            .left-panel, .right-panel { width: 100%; min-height: 300px; } 
        }
    </style>
</head>
<body>

<div class="left-panel"></div>

<div class="right-panel">
    <div class="form-box">
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Shop</a>
        
        <div class="form-header">
            <h1>Sign In 👋</h1>
            <p>Don't have an account? <a href="register.php">Create one free</a></p>
        </div>

        <?php if($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Your password" required>
            </div>
            <button type="submit" name="login" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        
        <div class="admin-note">
            <p>👑 <strong>Admin Login:</strong> admin@babybliss.com / password</p>
        </div>
    </div>
</div>

</body>
</html>