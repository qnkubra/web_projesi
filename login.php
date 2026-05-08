<?php
session_start();
require_once 'config.php';

$hata_mesaji = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $sifre = trim($_POST["sifre"]);

    if (empty($email) || empty($sifre)) {
        $hata_mesaji = "Lütfen email adresinizi ve şifrenizi giriniz.";
    } else {
        $sorgu = "SELECT id, ad_soyad, sifre, rol FROM kullanicilar WHERE email = ?";
        
        if ($stmt = mysqli_prepare($db, $sorgu)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $sonuc = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($sonuc) == 1) {
                $kullanici = mysqli_fetch_assoc($sonuc);
                
                // Şifre kontrolü (Dikkat: Gerçek projede password_verify() kullanılmalı, şimdilik düz metin)
                if ($sifre === $kullanici['sifre']) { 
                    $_SESSION["kullanici_id"] = $kullanici['id'];
                    $_SESSION["ad_soyad"] = $kullanici['ad_soyad'];
                    $_SESSION["rol"] = $kullanici['rol'];

                    if ($kullanici['rol'] == 'admin') {
                        header("location: admin_panel.php");
                    } else {
                        header("location: index.php");
                    }
                    exit;
                } else {
                    $hata_mesaji = "Hatalı şifre girdiniz.";
                }
            } else {
                $hata_mesaji = "Bu email adresi ile kayıtlı bir kullanıcı bulunamadı.";
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
    <title>LibReserve - Giriş Yap</title>
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
        .bg-slider {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2; 
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            animation: fadeAnim 15s infinite; 
        }

        .slide:nth-child(1) { animation-delay: 0s; }
        .slide:nth-child(2) { animation-delay: 5s; }
        .slide:nth-child(3) { animation-delay: 10s; }

        @keyframes fadeAnim {
            0% { opacity: 0; }
            10% { opacity: 1; }
            33% { opacity: 1; } 
            43% { opacity: 0; }
            100% { opacity: 0; }
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(13, 13, 18, 0.8); 
            z-index: -1; 
        }

        /* --- GİRİŞ KUTUSU --- */
        .login-box { 
            background-color: rgba(26, 26, 36, 0.85); 
            padding: 40px; 
            border-radius: 12px; 
            width: 100%; 
            max-width: 400px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.8); 
            z-index: 1; 
        }

        .logo { text-align: center; font-size: 28px; font-weight: bold; margin-bottom: 30px; letter-spacing: 1px; }
        .logo span { color: #58493e; } 

        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-size: 14px; color: #a0a0b0; }
        .input-group input { 
            width: 100%; 
            padding: 12px; 
            background-color: #0d0d12; 
            border: 1px solid #2d2d3a; 
            border-radius: 6px; 
            color: #fff; 
            box-sizing: border-box; 
            font-size: 16px; 
            transition: border-color 0.3s; 
        }
        .input-group input:focus { outline: none; border-color: #58493e; } 

        .btn-submit { 
            width: 100%; 
            padding: 14px; 
            background-color: #58493e; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            font-size: 16px; 
            cursor: pointer; 
            font-weight: bold; 
            transition: background 0.3s; 
            margin-top: 10px; 
        }
        .btn-submit:hover { background-color: #443830; } 

        .error-message { background-color: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; padding: 10px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        
        .register-link { text-align: center; margin-top: 25px; font-size: 14px; color: #a0a0b0; }
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

        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="email">E-posta Adresi</label>
                <input type="email" id="email" name="email" autocomplete="off" required>
            </div>
            
            <div class="input-group">
                <label for="sifre">Şifre</label>
                <input type="password" id="sifre" name="sifre" required>
            </div>
            
            <button type="submit" class="btn-submit">GİRİŞ YAP</button>
        </form>

        <div class="register-link">
            Sisteme kayıtlı değil misin? <a href="register.php">Kayıt Ol</a>
        </div>
    </div>

</body>
</html>