<?php
session_start();
require_once 'config.php';

// Giriş kontrolü
if (!isset($_SESSION["kullanici_id"])) {
    header("location: login.php");
    exit;
}

// Çıkış işlemi
if (isset($_GET['cikis'])) {
    session_destroy();
    header("location: login.php");
    exit;
}

// Dinamik İstatistik: Toplam Rezervasyon Sayısı
$toplam_rez = 0;
$sorgu = mysqli_query($db, "SELECT COUNT(id) as toplam FROM rezervasyonlar WHERE iptal_edildi = 0");
if($row = mysqli_fetch_assoc($sorgu)) {
    $toplam_rez = $row['toplam'];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - Ana Sayfa</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect x='15' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='35' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='55' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><polygon points='75,20 87,20 97,80 85,80' fill='%23b89c88'/></svg>">
    <style>
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', sans-serif; scroll-behavior: smooth; }
        
        /* NAVBAR */
        .navbar { background-color: #1a1a24; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d2d3a; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        .nav-links a { color: #a0a0b0; text-decoration: none; margin: 0 15px; font-size: 14px; font-weight: bold; transition: 0.3s; }
        .nav-links a.active, .nav-links a:hover { color: #ffffff; }
        
        /* ADMIN BUTONU STİLİ */
        .admin-link { color: #ff4d4d !important; border: 1px solid #ff4d4d; padding: 5px 12px; border-radius: 6px; transition: 0.3s; }
        .admin-link:hover { background-color: #ff4d4d; color: white !important; }

        .user-menu { display: flex; align-items: center; gap: 20px; }
        .logout-btn { background-color: rgba(255, 77, 77, 0.1); color: #ff4d4d; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; border: 1px solid #ff4d4d; transition: 0.3s; }
        .logout-btn:hover { background-color: #ff4d4d; color: white; }

        /* HERO & DİĞERLERİ (Aynı kalıyor) */
        .hero { text-align: center; padding: 100px 20px 60px; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 20px; letter-spacing: -1px; }
        .hero h1 span { color: #b89c88; }
        .hero p { color: #a0a0b0; font-size: 1.2rem; margin-bottom: 40px; }
        .hero-btns { display: flex; justify-content: center; gap: 20px; }
        .btn-primary { background-color: #58493e; color: white; padding: 15px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .btn-primary:hover { background-color: #443830; }
        .btn-secondary { background-color: transparent; color: white; padding: 15px 30px; border-radius: 30px; text-decoration: none; font-weight: bold; border: 1px solid #2d2d3a; transition: 0.3s; }
        .btn-secondary:hover { background-color: #1a1a24; border-color: #58493e; }
        .stats { display: flex; justify-content: space-around; padding: 50px 20px; border-top: 1px solid #2d2d3a; background-color: #1a1a24; }
        .stat-item { text-align: center; }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: #b89c88; }
        .stat-label { color: #a0a0b0; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; }
        .how-it-works { padding: 100px 20px; text-align: center; max-width: 1000px; margin: 0 auto; }
        .steps-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; margin-top: 50px;}
        .step-card { background-color: #1a1a24; padding: 40px; border-radius: 16px; border: 1px solid #2d2d3a; transition: 0.3s; }
        .step-card:hover { transform: translateY(-10px); border-color: #58493e; }
        .step-icon { width: 60px; height: 60px; background: rgba(88, 73, 62, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 24px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <div class="nav-links">
            <a href="index.php" class="active">Ana Sayfa</a>
            <a href="salonlar.php">Salonlar</a>
            <a href="rezervasyonlarim.php">Rezervasyonlarım</a>
            
            <?php if(isset($_SESSION["rol"]) && $_SESSION["rol"] == 'admin'): ?>
                <a href="admin_panel.php" class="admin-link">Yönetim Paneli</a>
            <?php endif; ?>
        </div>
        
        <div class="user-menu">
            <span style="color:#a0a0b0; font-size:14px;">Merhaba, <?php echo htmlspecialchars($_SESSION["ad_soyad"]); ?></span>
            <a href="?cikis=1" class="logout-btn" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">Çıkış Yap</a>
        </div>
    </nav>

    <section class="hero">
        <h1>Kütüphanede<br>Yerinizi <span>Şimdiden Ayırın</span></h1>
        <p>Sınav döneminde boş masa arayan son kişi sen olma.</p>
        <div class="hero-btns">
            <a href="salonlar.php" class="btn-primary">Hemen Başla →</a>
            <a href="#nasil-yapilir" class="btn-secondary">Nasıl Rezervasyon Yaparım?</a>
        </div>
    </section>

    <section class="stats">
        <div class="stat-item">
            <div class="stat-number"><?php echo $toplam_rez; ?></div>
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

    <section class="how-it-works" id="nasil-yapilir">
        <h2>Nasıl Rezervasyon Yaparım?</h2>
        <div class="steps-grid">
            <div class="step-card"><div class="step-icon">📅</div><h3>Salon Seç</h3><p>Salonlar arasından sana uygun olanı bul.</p></div>
            <div class="step-card"><div class="step-icon">🪑</div><h3>Masa Seç</h3><p>Harita üzerinden boş masalardan dilediğini tıkla ve ayırt.</p></div>
            <div class="step-card"><div class="step-icon">✅</div><h3>Rezerve Et</h3><p>Rezervasyonunu onayla ve yerini garantiye al.</p></div>
        </div>
    </section>

</body>
</html>