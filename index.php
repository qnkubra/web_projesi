<?php
session_start();
require_once 'config.php'; // Veritabanı bağlantısı eklendi

// Kullanıcı giriş yapmamışsa login sayfasına at
if (!isset($_SESSION["kullanici_id"])) {
    header("location: login.php");
    exit;
}

// Çıkış yapma işlemi
if (isset($_GET['cikis'])) {
    session_destroy();
    header("location: login.php");
    exit;
}

// VERİTABANINDAN TOPLAM REZERVASYON SAYISINI ÇEKME
$toplam_rezervasyon = 0;
// rezervasyonlar tablosundaki tüm id'leri sayıyoruz
$rez_sorgu = "SELECT COUNT(id) AS toplam FROM rezervasyonlar";
$rez_sonuc = mysqli_query($db, $rez_sorgu);

if ($rez_sonuc) {
    $rez_satir = mysqli_fetch_assoc($rez_sonuc);
    $toplam_rezervasyon = $rez_satir['toplam'];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - Ana Sayfa</title>
    <style>
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* NAVBAR */
        .navbar { 
            background-color: #1a1a24; 
            padding: 15px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid #2d2d3a; 
            position: sticky; top: 0; z-index: 100;
        }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        .nav-links a { 
            color: #a0a0b0; 
            text-decoration: none; 
            margin: 0 15px; 
            font-size: 14px; 
            font-weight: bold; 
            transition: all 0.3s; 
        }
        /* Aktif link parlaması */
        .nav-links a:hover, .nav-links a.active { 
            color: #ffffff; 
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        .user-menu { display: flex; align-items: center; gap: 20px; }
        .user-info { color: #a0a0b0; font-size: 14px; }
        .logout-btn { background-color: rgba(255, 77, 77, 0.1); color: #ff4d4d; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; border: 1px solid #ff4d4d; transition: all 0.3s; }
        .logout-btn:hover { background-color: #ff4d4d; color: white; }

        /* HERO BÖLÜMÜ */
        .hero { text-align: center; padding: 100px 20px 60px; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 20px; letter-spacing: -1px; line-height: 1.2; }
        .hero h1 span { color: #b89c88; }
        .hero p { color: #a0a0b0; font-size: 1.2rem; margin-bottom: 40px; }
        
        .hero-buttons { display: flex; justify-content: center; gap: 20px; }
        .btn-primary { background-color: #58493e; color: white; padding: 15px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 16px; transition: background 0.3s; }
        .btn-primary:hover { background-color: #443830; }
        .btn-secondary { background-color: transparent; color: white; padding: 15px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 16px; border: 1px solid #2d2d3a; transition: all 0.3s; }
        .btn-secondary:hover { background-color: #2d2d3a; }

        /* İSTATİSTİKLER (Aktif Öğrenci Çıkarıldı) */
        .stats { 
            display: flex; 
            justify-content: space-around; 
            padding: 50px 20px; 
            border-top: 1px solid #2d2d3a; 
            border-bottom: 1px solid #2d2d3a; 
            background-color: #1a1a24;
            flex-wrap: wrap;
        }
        .stat-item { text-align: center; min-width: 200px; }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: #b89c88; margin-bottom: 10px; }
        .stat-label { color: #a0a0b0; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; }

        /* NASIL REZERVASYON YAPARIM */
        .how-it-works { padding: 80px 20px; text-align: center; max-width: 1000px; margin: 0 auto; }
        .how-it-works h2 { font-size: 2.5rem; margin-bottom: 50px; }
        
        .steps-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        
        .step-card { 
            background-color: #1a1a24; 
            padding: 40px 30px; 
            border-radius: 16px; 
            border: 1px solid #2d2d3a; 
            transition: transform 0.3s;
        }
        .step-card:hover { transform: translateY(-10px); }
        
        .step-icon { 
            background-color: rgba(88, 73, 62, 0.15); 
            width: 70px; 
            height: 70px; 
            border-radius: 16px; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            margin: 0 auto 25px; 
        }
        .step-icon svg { width: 35px; height: 35px; fill: #b89c88; }
        
        .step-card h3 { font-size: 1.4rem; margin-bottom: 15px; }
        .step-card p { color: #a0a0b0; font-size: 1rem; line-height: 1.6; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <div class="nav-links">
            <a href="index.php" class="active">Ana Sayfa</a>
            <a href="salonlar.php">Salonlar</a>
            <a href="#">Hakkında</a>
            <a href="#">Rezervasyonlarım</a>
            <?php if($_SESSION["rol"] == 'admin'): ?>
                <a href="admin_panel.php" style="color: #ff4d4d; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 6px;">YÖNETİM PANELİ</a>
            <?php endif; ?>
        </div>
        <div class="user-menu">
            <span class="user-info">Merhaba, <?php echo htmlspecialchars($_SESSION["ad_soyad"]); ?></span>
            <a href="?cikis=1" class="logout-btn">Çıkış Yap</a>
        </div>
    </nav>

    <section class="hero">
        <h1>Kütüphanede<br>Yerinizi <span>Şimdiden Ayırın</span></h1>
        <p>Vize döneminde boş masa arayan son kişi sen olma.</p>
        <div class="hero-buttons">
            <a href="salonlar.php" class="btn-primary">Hemen Başla →</a>
            <a href="#rezervasyon-nasil" class="btn-secondary">Nasıl Rezervasyon Yaparım?</a>
        </div>
    </section>

    <section class="stats">
        <div class="stat-item">
            <div class="stat-number"><?php echo number_format($toplam_rezervasyon, 0, ',', '.'); ?></div>
            <div class="stat-label">Toplam Rezervasyon</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">4</div>
            <div class="stat-label">Çalışma Salonu</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">162</div> 
            <div class="stat-label">Toplam Masa</div>
        </div>
    </section>

    <section id="rezervasyon-nasil" class="how-it-works">
        <h2>Nasıl Rezervasyon Yaparım?</h2>
        <div class="steps-grid">
            
            <div class="step-card">
                <div class="step-icon">
                    <svg viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7v-5z"/></svg>
                </div>
                <h3>Salon Seç</h3>
                <p>Salonlar arasından sana uygun olanı bul.</p>
            </div>

            <div class="step-card">
                <div class="step-icon">
                    <svg viewBox="0 0 24 24"><path d="M7 11.52.95 2.11 20.65 9.8 12.06 13 7 11.52z"/></svg>
                </div>
                <h3>Masa Seç</h3>
                <p>Harita üzerinden boş masalardan dilediğini tıkla ve ayırt.</p>
            </div>

            <div class="step-card">
                <div class="step-icon">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1