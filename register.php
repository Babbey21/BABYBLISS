<?php
session_start();
require_once "config.php";

$error = "";
$success = "";

if (isset($_POST['submit'])) {
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $fname = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $lname = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // Validation
    if (empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($pass)) {
        $error = "Please fill in all required fields!";
    } elseif ($pass !== $confirm_pass) {
        $error = "Passwords do not match!";
    } elseif (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        // Check email exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "This email is already registered!";
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);

            // Insert user
            $sql = "INSERT INTO users (first_name, last_name, email, password_hash, phone, role, is_active, created_at) 
                    VALUES ('$fname', '$lname', '$email', '$hashed', '$phone', '$role', 1, NOW())";

            if (mysqli_query($conn, $sql)) {
                $user_id = mysqli_insert_id($conn);

                // If manufacturer, create manufacturer profile
                if ($role === 'manufacturer') {
                    $company = mysqli_real_escape_string($conn, trim($_POST['company_name'] ?? $fname . ' ' . $lname));
                    $country = mysqli_real_escape_string($conn, trim($_POST['country'] ?? ''));
                    $city = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
                    $desc = mysqli_real_escape_string($conn, trim($_POST['company_description'] ?? ''));

                    $m_sql = "INSERT INTO manufacturers (user_id, company_name, company_description, country, city, verification_status, created_at) 
                              VALUES ($user_id, '$company', '$desc', '$country', '$city', 'pending', NOW())";
                    mysqli_query($conn, $m_sql);
                }

                $success = "Registration successful! Please sign in.";
                header("Refresh: 2; URL=login.php");
            } else {
                $error = "Registration failed: " . mysqli_error($conn);
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
  <title>Register – BabyBliss Marketplace</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root { --cream: #FFF8F0; --blush: #F2A7B3; --rose: #E8738A; --deep-rose: #C44D65; --mint-dark: #5FB8A0; --white: #FFFFFF; --text-dark: #2D1B14; --text-mid: #6B4C3B; --text-light: #A07D6A; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: 'DM Sans', sans-serif; min-height: 100vh; display: flex; background: var(--cream); }
    .left-panel { width: 45%; background: linear-gradient(135deg, var(--deep-rose) 0%, #8B3A50 50%, #5A1A30 100%); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 48px; position: relative; overflow: hidden; }
    .left-panel::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/svg%3E"); }
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
    .right-panel { width: 55%; display: flex; align-items: center; justify-content: center; padding: 48px 60px; overflow-y: auto; }
    .form-box { width: 100%; max-width: 500px; }
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
    input[type=email], input[type=password], input[type=text], input[type=tel], select, textarea {
      width: 100%; padding: 13px 15px 13px 44px; border: 2px solid #F0E4DC; border-radius: 13px;
      font-family: 'DM Sans', sans-serif; font-size: 15px; color: var(--text-dark); background: var(--white);
      transition: all 0.2s; outline: none;
    }
    select { -webkit-appearance: none; cursor: pointer; padding-left: 15px; }
    textarea { padding-left: 15px; resize: vertical; min-height: 80px; }
    input:focus, select:focus, textarea:focus { border-color: var(--rose); box-shadow: 0 0 0 4px rgba(232,115,138,0.1); }
    .toggle-pass { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: var(--text-light); font-size: 15px; }
    .role-selector { display: flex; gap: 12px; margin-bottom: 24px; }
    .role-option { flex: 1; padding: 16px; border: 2px solid #F0E4DC; border-radius: 14px; cursor: pointer; text-align: center; transition: all 0.2s; background: var(--white); }
    .role-option:hover { border-color: var(--rose); }
    .role-option.active { border-color: var(--rose); background: linear-gradient(135deg, #FFF0F3, #FFE4EA); }
    .role-option i { font-size: 24px; margin-bottom: 8px; color: var(--rose); }
    .role-option .role-title { font-size: 14px; font-weight: 700; color: var(--text-dark); }
    .role-option .role-desc { font-size: 12px; color: var(--text-light); margin-top: 4px; }
    .manufacturer-fields { display: none; }
    .manufacturer-fields.active { display: block; }
    .strength-bar { height: 4px; border-radius: 2px; background: #F0E4DC; margin-top: 8px; overflow: hidden; }
    .strength-fill { height: 100%; width: 0; border-radius: 2px; transition: all 0.4s; }
    .strength-label { font-size: 12px; color: var(--text-light); margin-top: 4px; }
    .terms-check { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 22px; }
    .terms-check input { width: auto; padding: 0; margin-top: 2px; accent-color: var(--rose); flex-shrink: 0; }
    .terms-check label { font-size: 14px; color: var(--text-mid); cursor: pointer; line-height: 1.5; }
    .terms-check a { color: var(--rose); }
    .btn-primary { width: 100%; padding: 16px; background: linear-gradient(135deg, var(--rose), var(--deep-rose)); color: var(--white); border: none; border-radius: 14px; font-size: 16px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.25s; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(196,77,101,0.35); }
    .alert { padding: 14px 20px; border-radius: 12px; font-size: 14px; font-weight: 600; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .alert-success { background: #EAF8F4; border: 1px solid #B0E0D0; color: #2E7D62; }
    .alert-error { background: #FFF0F3; border: 1px solid #F5B8C8; color: var(--deep-rose); }
    @media (max-width: 768px) { body { flex-direction: column; } .left-panel, .right-panel { width: 100%; } .right-panel { padding: 32px 24px; } .form-row-2 { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="left-panel">
    <div class="deco1"></div>
    <div class="deco2"></div>
    <div class="panel-logo">
      <span class="panel-logo-icon">🍼</span>
      <div class="panel-logo-text">BabyBliss</div>
      <div class="panel-logo-sub">Marketplace</div>
    </div>
    <div class="panel-content">
      <h2>Join Our Marketplace 🌟</h2>
      <p>Shop from verified manufacturers worldwide or start selling your own baby products today.</p>
    </div>
    <div class="perks">
      <div class="perk"><div class="perk-icon">🛒</div><div class="perk-info"><strong>Shop Globally</strong><span>Products from manufacturers worldwide</span></div></div>
      <div class="perk"><div class="perk-icon">🏭</div><div class="perk-info"><strong>Become a Seller</strong><span>Register as manufacturer and start selling</span></div></div>
      <div class="perk"><div class="perk-icon">🛡️</div><div class="perk-info"><strong>Buyer Protection</strong><span>Secure escrow payments & dispute resolution</span></div></div>
    </div>
  </div>

  <div class="right-panel">
    <div class="form-box">
      <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Shop</a>
      <div class="form-header">
        <h1>Create Account 🌸</h1>
        <p>Already have an account? <a href="login.php">Sign in here</a></p>
      </div>

      <?php if($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
      <?php endif; ?>
      <?php if($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
      <?php endif; ?>

      <form action="register.php" method="POST">
        <!-- Role Selection -->
        <div class="role-selector">
          <div class="role-option active" onclick="selectRole('customer', this)">
            <i class="fas fa-shopping-bag"></i>
            <div class="role-title">Buyer</div>
            <div class="role-desc">Shop products</div>
          </div>
          <div class="role-option" onclick="selectRole('manufacturer', this)">
            <i class="fas fa-industry"></i>
            <div class="role-title">Seller</div>
            <div class="role-desc">Sell products</div>
          </div>
        </div>
        <input type="hidden" name="role" id="roleInput" value="customer">

        <div class="form-row-2">
          <div class="form-group">
            <label>First Name</label>
            <div class="input-wrap"><i class="fas fa-user"></i><input type="text" name="first_name" placeholder="Emma" required/></div>
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <div class="input-wrap"><i class="fas fa-user"></i><input type="text" name="last_name" placeholder="Johnson" required/></div>
          </div>
        </div>

        <div class="form-group">
          <label>Email Address</label>
          <div class="input-wrap"><i class="fas fa-envelope"></i><input type="email" name="email" placeholder="emma@example.com" required/></div>
        </div>

        <div class="form-row-2">
          <div class="form-group">
            <label>Phone Number</label>
            <div class="input-wrap"><i class="fas fa-phone"></i><input type="tel" name="phone" placeholder="+255 700 000 000" required/></div>
          </div>
          <div class="form-group">
            <label>Country</label>
            <div class="input-wrap"><i class="fas fa-globe"></i><input type="text" name="country" placeholder="Tanzania"/></div>
          </div>
        </div>

        <!-- Manufacturer Fields -->
        <div class="manufacturer-fields" id="manufacturerFields">
          <div class="form-group">
            <label>Company Name</label>
            <div class="input-wrap"><i class="fas fa-building"></i><input type="text" name="company_name" placeholder="Your Company Ltd"/></div>
          </div>
          <div class="form-group">
            <label>City</label>
            <div class="input-wrap"><i class="fas fa-map-marker-alt"></i><input type="text" name="city" placeholder="Dar es Salaam"/></div>
          </div>
          <div class="form-group">
            <label>Company Description</label>
            <textarea name="company_description" placeholder="Tell us about your company and products..."></textarea>
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
          <div class="input-wrap"><i class="fas fa-lock"></i><input type="password" name="confirm_password" placeholder="Repeat your password" required/></div>
        </div>

        <div class="terms-check">
          <input type="checkbox" id="terms" required/>
          <label for="terms">I agree to BabyBliss <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a></label>
        </div>

        <button type="submit" name="submit" class="btn-primary"><i class="fas fa-user-plus"></i> Create My Account</button>
      </form>
    </div>
  </div>

  <script>
    function selectRole(role, el) {
      document.querySelectorAll('.role-option').forEach(o => o.classList.remove('active'));
      el.classList.add('active');
      document.getElementById('roleInput').value = role;
      document.getElementById('manufacturerFields').classList.toggle('active', role === 'manufacturer');
    }
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
      const levels = [{w:'0%', c:'transparent', t:'Enter password'}, {w:'25%', c:'#E84D4D', t:'Weak'}, {w:'50%', c:'#F5A623', t:'Fair'}, {w:'75%', c:'#5CB85C', t:'Good'}, {w:'100%', c:'#3A9E88', t:'Strong 🎉'}];
      fill.style.width = levels[score].w; fill.style.background = levels[score].c; label.textContent = levels[score].t;
    }
  </script>
</body>
</html>