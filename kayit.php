<?php
session_start();
require_once 'config.php';

if(isset($_SESSION["kullanici_id"])){ header("location: index.php"); exit; }

$hata_mesaji = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = trim($_POST["ad_soyad"]);
    $email = trim($_POST["email"]);
    $sifre = $_POST["sifre"];

    $kontrol = mysqli_query($db, "SELECT id FROM kullanicilar WHERE email = '$email'");
    if (mysqli_num_rows($kontrol) > 0) {
        $hata_mesaji = "Bu e-posta zaten kayıtlı.";
    } else {
        $kriptolu_sifre = password_hash($sifre, PASSWORD_DEFAULT);
        $sorgu = "INSERT INTO kullanicilar (ad_soyad, email, sifre, rol) VALUES ('$ad_soyad', '$email', '$kriptolu_sifre', 'ogrenci')";
        if (mysqli_query($db, $sorgu)) {
            header("location: login.php?kayit=basarili");
            exit;
        } else { $hata_mesaji = "Hata oluştu."; }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>LibReserve - Kayıt Ol</title>
    <style>
        /* login.php ile aynı CSS kullanılacak */
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
        <?php if(!empty($hata_mesaji)) echo "<div class='error-message'>$hata_mesaji</div>"; ?>
        <form action="kayit.php" method="POST">
            <div class="input-group"><label>Ad Soyad</label><input type="text" name="ad_soyad" required></div>
            <div class="input-group"><label>E-posta</label><input type="email" name="email" required></div>
            <div class="input-group"><label>Şifre</label><input type="password" name="sifre" required></div>
            <button type="submit" class="btn-submit">KAYIT OL</button>
        </form>
        <div class="register-link">Zaten hesabın var mı? <a href="login.php">Giriş Yap</a></div>
    </div>
</body>
</html>