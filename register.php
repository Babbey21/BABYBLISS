<?php
include('config.php');

if(isset($_POST['submit'])){
  
    $fname = mysqli_real_escape_string($conn, trim($_POST['first_name'])); 
    $lname = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $baby_age = mysqli_real_escape_string($conn, $_POST['baby_age']); 
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // Validation
    if(empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($baby_age) || empty($pass)){
        echo "<script>alert('Tafadhali jaza taarifa zote!');</script>";
    }
    elseif($pass !== $confirm_pass){
        echo "<script>alert('Password hazifanani!');</script>";
    }
    else {
        // Check email
        $check_email = "SELECT id FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $check_email);
        
        if(mysqli_num_rows($result) > 0){
            echo "<script>alert('Email hii tayari imesajiliwa!');</script>";
        } else {
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (first_name, last_name, email, password_hash, phone, baby_age_range, role, is_active, created_at) 
                    VALUES ('$fname', '$lname', '$email', '$hashed_password', '$phone', '$baby_age', 'customer', 1, NOW())";

            try {
                if(mysqli_query($conn, $sql)){
                    echo "<script>alert('Hongera! Umesajiliwa kikamilifu.'); window.location='login.php';</script>";
                } else {
                    throw new Exception(mysqli_error($conn));
                }
            } catch (Exception $e) {
                // Kama auto-increment imekwama, jaribu tena
                if(strpos($e->getMessage(), 'auto-increment') !== false){
                    mysqli_query($conn, "ALTER TABLE users AUTO_INCREMENT = 1");
                    echo "<script>alert('Jaribu tena!'); window.location='register.php';</script>";
                } else {
                    echo "Kuna tatizo: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register – BabyBliss</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root {
      --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A;
      --deep-rose: #C44D65; --mint-dark: #5FB8A0;
      --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh; display: flex; background: var(--cream);
    }
    .left-panel {
      width: 50%; background-image: url("img/📚 Toddler Learning Activities That Turn Play Into Progress.jpg");
      background-size: cover; background-position: center; background-repeat: no-repeat;
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      padding: 60px 48px; position: relative; overflow: hidden;
    }
    .left-panel::before {
      content: ''; position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/svg%3E");
    }
    .panel-logo { position: relative; z-index: 1; text-align: center; margin-bottom: 32px; }
    .panel-logo-icon { font-size: 56px; display: block; margin-bottom: 10px; animation: float 3s ease-in-out infinite; }
    @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
    .panel-logo-text { font-family: 'Playfair Display', serif; font-size: 36px; color: var(--white); font-weight: 700; }
    .panel-logo-sub { color: rgba(255,255,255,0.75); font-size: 12px; letter-spacing: 3px; text-transform: uppercase; margin-top: 5px; }
    .panel-content { position: relative; z-index: 1; text-align: center; }
    .panel-content h2 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--white); margin-bottom: 14px; }
    .panel-content p { color: rgba(255,255,255,0.8); font-size: 15px; line-height: 1.7; }
    .perks { position: relative; z-index: 1; margin-top: 36px; display: flex; flex-direction: column; gap: 14px; width: 100%; }
    .perk { background: rgba(255,255,255,0.15); border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; backdrop-filter: blur(6px); }
    .perk-icon { font-size: 24px; }
    .perk-info { color: var(--white); }
    .perk-info strong { font-size: 14px; font-weight: 600; display: block; }
    .perk-info span { font-size: 13px; opacity: 0.8; }
    .deco1 { position: absolute; width: 200px; height: 200px; border-radius: 50%; background: rgba(255,255,255,0.08); top: -80px; right: -80px; }
    .deco2 { position: absolute; width: 140px; height: 140px; border-radius: 50%; background: rgba(255,255,255,0.06); bottom: 40px; left: -50px; }

    .right-panel { width: 58%; display: flex; align-items: center; justify-content: center; padding: 48px 60px; overflow-y: auto; }
    .form-box { width: 100%; max-width: 480px; }
    .back-link { display: flex; align-items: center; gap: 6px; color: var(--text-light); text-decoration: none; font-size: 14px; margin-bottom: 28px; transition: color 0.2s; }
    .back-link:hover { color: var(--rose); }
    .form-header { margin-bottom: 32px; }
    .form-header h1 { font-family: 'Playfair Display', serif; font-size: 34px; color: var(--text-dark); margin-bottom: 7px; }
    .form-header p { color: var(--text-light); font-size: 15px; }
    .form-header a { color: var(--rose); font-weight: 600; text-decoration: none; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { margin-bottom: 18px; }
    label { display: block; font-size: 13px; font-weight: 600; color: var(--text-mid); margin-bottom: 7px; }
    .input-wrap { position: relative; }
    .input-wrap i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-light); font-size: 15px; }
    input[type=email], input[type=password], input[type=text], input[type=tel], select {
      width: 100%; padding: 13px 15px 13px 44px;
      border: 2px solid #F0E4DC; border-radius: 13px;
      font-family: 'DM Sans', sans-serif; font-size: 15px;
      color: var(--text-dark); background: var(--white);
      transition: all 0.2s; outline: none;
    }
    select { -webkit-appearance: none; cursor: pointer; }
    input:focus, select:focus { border-color: var(--rose); box-shadow: 0 0 0 4px rgba(232,115,138,0.1); }
    .strength-bar { height: 4px; border-radius: 2px; background: #F0E4DC; margin-top: 8px; overflow: hidden; }
    .strength-fill { height: 100%; width: 0; border-radius: 2px; transition: all 0.4s; }
    .strength-label { font-size: 12px; color: var(--text-light); margin-top: 4px; }
    .toggle-pass { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: var(--text-light); font-size: 15px; }
    .terms-check { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 22px; }
    .terms-check input { width: auto; padding: 0; margin-top: 2px; accent-color: var(--rose); flex-shrink: 0; }
    .terms-check label { font-size: 14px; color: var(--text-mid); cursor: pointer; line-height: 1.5; }
    .terms-check a { color: var(--rose); }
    .btn-primary {
      width: 100%; padding: 16px;
      background: linear-gradient(135deg, var(--rose), var(--deep-rose));
      color: var(--white); border: none; border-radius: 14px;
      font-size: 16px; font-weight: 700; cursor: pointer;
      display: flex; align-items: center; justify-content: center; gap: 10px;
      transition: all 0.25s;
    }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(196,77,101,0.35); }
    .divider-text { text-align: center; margin: 22px 0; position: relative; }
    .divider-text::before, .divider-text::after { content: ''; position: absolute; top: 50%; width: 43%; height: 1px; background: #F0E4DC; }
    .divider-text::before { left: 0; } .divider-text::after { right: 0; }
    .divider-text span { font-size: 13px; color: var(--text-light); background: var(--cream); padding: 0 12px; position: relative; }
    .social-login { display: flex; gap: 12px; }
    .btn-social { flex: 1; padding: 12px; border-radius: 12px; border: 2px solid #F0E4DC; background: var(--white); font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; color: var(--text-mid); transition: all 0.2s; }
    .btn-social:hover { border-color: var(--rose); color: var(--rose); background: #FFF0F3; }
    .step-indicator { display: flex; gap: 8px; margin-bottom: 28px; }
    .step { flex: 1; height: 4px; border-radius: 2px; background: #F0E4DC; transition: background 0.3s; }
    .step.done { background: linear-gradient(90deg, var(--rose), var(--deep-rose)); }
    @media (max-width: 768px) {
      body { flex-direction: column; }
      .left-panel, .right-panel { width: 100%; }
      .right-panel { padding: 32px 24px; }
      .form-row-2 { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <div class="left-panel">
    <div class="deco1"></div>
    <div class="deco2"></div>
    <div class="panel-logo">
      <span class="panel-logo-icon">🍼</span>
      <div class="panel-logo-text">BabyBliss</div>
      <div class="panel-logo-sub">Nurture Every Moment</div>
    </div>
    <div class="panel-content">
      <h2>Join the BabyBliss Family 💚</h2>
      <p>Create your free account and unlock a world of amazing baby products, exclusive deals, and parenting resources.</p>
    </div>
    <div class="perks">
      <div class="perk">
        <div class="perk-icon">🎁</div>
        <div class="perk-info">
          <strong>Welcome Gift</strong>
          <span>$10 off your first order</span>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon">⭐</div>
        <div class="perk-info">
          <strong>Loyalty Points</strong>
          <span>Earn with every purchase</span>
        </div>
      </div>
      <div class="perk">
        <div class="perk-icon">🔔</div>
        <div class="perk-info">
          <strong>Early Access</strong>
          <span>Be first for new arrivals & sales</span>
        </div>
      </div>
    </div>
  </div>

  <div class="right-panel">
    <div class="form-box">
      <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Shop</a>
      <div class="step-indicator">
        <div class="step done"></div>
        <div class="step done"></div>
        <div class="step"></div>
      </div>
      <div class="form-header">
        <h1>Create Account 🌸</h1>
        <p>Already have an account? <a href="login.php">Sign in here</a></p>
      </div>

      <form action="register.php" method="POST">
      
        <div class="form-row-2">
          <div class="form-group">
            <label>First Name</label>
            <div class="input-wrap">
              <i class="fas fa-user"></i>
              <input type="text" name="first_name" placeholder="Emma" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <div class="input-wrap">
              <i class="fas fa-user"></i>
              <input type="text" name="last_name" placeholder="Johnson" required/>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Email Address</label>
          <div class="input-wrap">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="emma@example.com" required/>
          </div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label>Phone Number</label>
            <div class="input-wrap">
              <i class="fas fa-phone"></i>
              <input type="tel" name="phone" placeholder="+1 (555) 000-0000" required/>
            </div>
          </div>
          <div class="form-group">
            <label>Baby's Age Range</label>
            <div class="input-wrap">
              <i class="fas fa-baby"></i>
              <select name="baby_age" required>
                <option value="">Select age...</option>
                <option value="Expecting">Expecting</option>
                <option value="0-6 months">0–6 months</option>
                <option value="6-12 months">6–12 months</option>
                <option value="1-2 years">1–2 years</option>
                <option value="2-4 years">2–4 years</option>
                <option value="4+ years">4+ years</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label>Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="password" placeholder="Create a strong password" oninput="checkStrength(this.value)" required/>
            <button type="button" class="toggle-pass" onclick="toggleP()"><i class="fas fa-eye" id="eye"></i></button>
          </div>
          <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
          <div class="strength-label" id="strengthLabel">Enter password to check strength</div>
        </div>

        <div class="form-group">
          <label>Confirm Password</label>
          <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" name="confirm_password" placeholder="Repeat your password" required/>
          </div>
        </div>

        <div class="terms-check">
          <input type="checkbox" id="terms" required/>
          <label for="terms">I agree to BabyBliss <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I'd love to receive parenting tips and exclusive offers!</label>
        </div>

        <button type="submit" name="submit" class="btn-primary">
          <i class="fas fa-baby"></i> Create My Account
        </button>
        
      </form>

      <div class="divider-text"><span>or sign up with</span></div>
      <div class="social-login">
        <button class="btn-social"><i class="fab fa-google" style="color:#EA4335"></i> Google</button>
        <button class="btn-social"><i class="fab fa-facebook-f" style="color:#1877F2"></i> Facebook</button>
        <button class="btn-social"><i class="fab fa-apple" style="color:#000"></i> Apple</button>
      </div>
    </div>
  </div>

  <script>
    function toggleP() {
      const p = document.getElementById('password');
      const e = document.getElementById('eye');
      if(p.type === 'password') { p.type = 'text'; e.className = 'fas fa-eye-slash'; }
      else { p.type = 'password'; e.className = 'fas fa-eye'; }
    }
    function checkStrength(v) {
      const fill = document.getElementById('strengthFill');
      const label = document.getElementById('strengthLabel');
      let score = 0;
      if(v.length >= 8) score++;
      if(/[A-Z]/.test(v)) score++;
      if(/[0-9]/.test(v)) score++;
      if(/[^A-Za-z0-9]/.test(v)) score++;
      const levels = [
        {w:'0%', c:'transparent', t:'Enter password'},
        {w:'25%', c:'#E84D4D', t:'Weak'},
        {w:'50%', c:'#F5A623', t:'Fair'},
        {w:'75%', c:'#5CB85C', t:'Good'},
        {w:'100%', c:'#3A9E88', t:'Strong 🎉'}
      ];
      fill.style.width = levels[score].w;
      fill.style.background = levels[score].c;
      label.textContent = levels[score].t;
    }
  </script>
</body>
</html>