<?php
session_start();
require_once 'config.php';

if(isset($_SESSION["kullanici_id"])){
    header("location: index.php");
    exit;
}

$hata_mesaji = "";
$basari_mesaji = "";

if(isset($_GET['kayit']) && $_GET['kayit'] == 'basarili'){
    $basari_mesaji = "Kayıt başarıyla oluşturuldu! Şimdi giriş yapabilirsiniz.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $sifre = $_POST["sifre"];
//güvenli sorgu
    $sorgu = "SELECT id, ad_soyad, sifre, rol FROM kullanicilar WHERE email = ?";
    if ($stmt = mysqli_prepare($db, $sorgu)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $sonuc = mysqli_stmt_get_result($stmt);

        if ($kullanici = mysqli_fetch_assoc($sonuc)) {
            if (password_verify($sifre, $kullanici['sifre'])) { //şifre doğrulama
                $_SESSION["kullanici_id"] = $kullanici['id'];
                $_SESSION["ad_soyad"] = $kullanici['ad_soyad'];
                $_SESSION["rol"] = $kullanici['rol'];
                header("location: " . ($kullanici['rol'] == 'admin' ? "admin_panel.php" : "index.php"));
                exit;
            } else { $hata_mesaji = "Hatalı şifre girdiniz."; }
        } else { $hata_mesaji = "Kullanıcı bulunamadı."; }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>LibReserve - Giriş Yap</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect x='15' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='35' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='55' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><polygon points='75,20 87,20 97,80 85,80' fill='%23b89c88'/></svg>">
    <style>
        body { color: #ffffff; font-family: 'Segoe UI', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; overflow: hidden; }
        .bg-slider { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2; }
        .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; animation: fadeAnim 15s infinite; }
        .slide:nth-child(1) { animation-delay: 0s; }
        .slide:nth-child(2) { animation-delay: 5s; }
        .slide:nth-child(3) { animation-delay: 10s; }
        @keyframes fadeAnim { 0% { opacity: 0; } 10% { opacity: 1; } 33% { opacity: 1; } 43% { opacity: 0; } 100% { opacity: 0; } }
        .bg-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(13, 13, 18, 0.8); z-index: -1; }
        .login-box { background-color: rgba(26, 26, 36, 0.85); padding: 40px; border-radius: 12px; width: 100%; max-width: 400px; box-shadow: 0 15px 35px rgba(0,0,0,0.8); z-index: 1; }
        .logo { text-align: center; font-size: 28px; font-weight: bold; margin-bottom: 30px; }
        .logo span { color: #58493e; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; color: #a0a0b0; }
        .input-group input { width: 100%; padding: 12px; background-color: #0d0d12; border: 1px solid #2d2d3a; border-radius: 6px; color: #fff; box-sizing: border-box; }
        .btn-submit { width: 100%; padding: 14px; background-color: #58493e; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .error-message { background-color: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
        .success-message { background-color: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid #2ed573; padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
        .register-link { text-align: center; margin-top: 25px; font-size: 14px; color: #a0a0b0; }
        .register-link a { color: #58493e; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="bg-slider">
        <div class="slide" style="background-image: url('images/1767940234-4188b0260a.jpg');"></div>
        <div class="slide" style="background-image: url('images/resized_ea26e-0c2ea193c3a7ilek162.jpg');"></div>
        <div class="slide" style="background-image: url('images/modern-library-1.jpg');"></div>
    </div>
    <div class="bg-overlay"></div>
    <div class="login-box">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <?php if(!empty($basari_mesaji)) echo "<div class='success-message'>$basari_mesaji</div>"; ?>
        <?php if(!empty($hata_mesaji)) echo "<div class='error-message'>$hata_mesaji</div>"; ?>
        <form action="login.php" method="POST">
            <div class="input-group"><label>E-posta</label><input type="email" name="email" required></div>
            <div class="input-group"><label>Şifre</label><input type="password" name="sifre" required></div>
            <button type="submit" class="btn-submit">GİRİŞ YAP</button>
        </form>
        <div class="register-link">Kayıtlı değil misin? <a href="register.php">Kayıt Ol</a></div>
    </div>
</body>
</html>