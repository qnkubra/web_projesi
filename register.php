<?php
session_start();
require_once 'config.php';

$hata_mesaji = "";
$basari_mesaji = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad_soyad = trim($_POST["ad_soyad"]);
    $email = trim($_POST["email"]);
    $sifre = trim($_POST["sifre"]);
    $sifre_tekrar = trim($_POST["sifre_tekrar"]);

    // 1. Boş alan kontrolü
    if (empty($ad_soyad) || empty($email) || empty($sifre) || empty($sifre_tekrar)) {
        $hata_mesaji = "Lütfen tüm alanları doldurunuz.";
    } 
    // --- YENİ EKLENEN KISIM: Şifre uzunluk kontrolü ---
    elseif (strlen($sifre) < 6) {
        $hata_mesaji = "Şifreniz güvenliğiniz için en az 6 karakter uzunluğunda olmalıdır.";
    }
    // ---------------------------------------------------
    // 2. Şifre eşleşme kontrolü
    elseif ($sifre !== $sifre_tekrar) {
        $hata_mesaji = "Şifreler birbiriyle eşleşmiyor.";
    } 
    else {
        // 3. E-posta adresi zaten var mı kontrolü
        $kontrol_sorgu = "SELECT id FROM kullanicilar WHERE email = ?";
        if ($stmt = mysqli_prepare($db, $kontrol_sorgu)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $hata_mesaji = "Bu e-posta adresi zaten sisteme kayıtlı.";
            } else {
                // 4. Yeni kullanıcıyı veritabanına ekleme
                $ekle_sorgu = "INSERT INTO kullanicilar (ad_soyad, email, sifre, rol) VALUES (?, ?, ?, 'ogrenci')";
                if ($ekle_stmt = mysqli_prepare($db, $ekle_sorgu)) {
                    mysqli_stmt_bind_param($ekle_stmt, "sss", $ad_soyad, $email, $sifre);
                    
                    if (mysqli_stmt_execute($ekle_stmt)) {
                        $basari_mesaji = "Kayıt işlemi başarılı! Giriş sayfasına yönlendiriliyorsunuz...";
                        header("refresh:2;url=login.php");
                    } else {
                        $hata_mesaji = "Kayıt sırasında bir hata oluştu.";
                    }
                    mysqli_stmt_close($ekle_stmt);
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            $hata_mesaji = "Veritabanı sorgu hatası oluştu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - Kayıt Ol</title>
    <style>
        body { 
            color: #ffffff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            overflow: hidden; 
        }

        /* --- ARKA PLAN ANİMASYON CSS KODLARI --- */
        .bg-slider { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2; }
        .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; animation: fadeAnim 15s infinite; }
        .slide:nth-child(1) { animation-delay: 0s; }
        .slide:nth-child(2) { animation-delay: 5s; }
        .slide:nth-child(3) { animation-delay: 10s; }

        @keyframes fadeAnim {
            0% { opacity: 0; } 10% { opacity: 1; } 33% { opacity: 1; } 43% { opacity: 0; } 100% { opacity: 0; }
        }

        .bg-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(13, 13, 18, 0.8); z-index: -1; }

        /* --- KAYIT KUTUSU --- */
        .login-box { 
            background-color: rgba(26, 26, 36, 0.85); 
            padding: 40px; 
            border-radius: 12px; 
            width: 100%; 
            max-width: 400px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.8); 
            z-index: 1; 
            /* Form biraz daha uzun olacağı için kaydırma çubuğu çıkmasını engelliyoruz */
            max-height: 90vh;
            overflow-y: auto;
        }
        /* Kaydırma çubuğunu gizlemek için */
        .login-box::-webkit-scrollbar { display: none; }
        .login-box { -ms-overflow-style: none; scrollbar-width: none; }

        .logo { text-align: center; font-size: 28px; font-weight: bold; margin-bottom: 25px; letter-spacing: 1px; }
        .logo span { color: #58493e; } 

        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; margin-bottom: 5px; font-size: 13px; color: #a0a0b0; }
        .input-group input { width: 100%; padding: 10px; background-color: #0d0d12; border: 1px solid #2d2d3a; border-radius: 6px; color: #fff; box-sizing: border-box; font-size: 15px; transition: border-color 0.3s; }
        .input-group input:focus { outline: none; border-color: #58493e; } 

        .btn-submit { width: 100%; padding: 12px; background-color: #58493e; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; font-weight: bold; transition: background 0.3s; margin-top: 10px; } 
        .btn-submit:hover { background-color: #443830; } 

        .error-message { background-color: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        .success-message { background-color: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid #2ed573; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-align: center; }
        
        .register-link { text-align: center; margin-top: 20px; font-size: 14px; color: #a0a0b0; }
        .register-link a { color: #58493e; text-decoration: none; font-weight: bold; } 
        .register-link a:hover { text-decoration: underline; }
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
        
        <?php if(!empty($hata_mesaji)): ?>
            <div class="error-message"> <?php echo $hata_mesaji; ?> </div>
        <?php endif; ?>

        <?php if(!empty($basari_mesaji)): ?>
            <div class="success-message"> <?php echo $basari_mesaji; ?> </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="input-group">
                <label for="ad_soyad">Ad Soyad</label>
                <input type="text" id="ad_soyad" name="ad_soyad" autocomplete="off" required>
            </div>

            <div class="input-group">
                <label for="email">E-posta Adresi</label>
                <input type="email" id="email" name="email" autocomplete="off" required>
            </div>
            
            <div class="input-group">
                <label for="sifre">Şifre</label>
                <input type="password" id="sifre" name="sifre" required>
            </div>

            <div class="input-group">
                <label for="sifre_tekrar">Şifre (Tekrar)</label>
                <input type="password" id="sifre_tekrar" name="sifre_tekrar" required>
            </div>
            
            <button type="submit" class="btn-submit">KAYIT OL</button>
        </form>

        <div class="register-link">
            Zaten üye misin? <a href="login.php">Giriş Yap</a>
        </div>
    </div>

</body>
</html>