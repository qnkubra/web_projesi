<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION["kullanici_id"])) {
    header("location: login.php");
    exit;
}

if (isset($_GET['cikis'])) {
    session_destroy();
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - Salonlar</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect x='15' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='35' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='55' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><polygon points='75,20 87,20 97,80 85,80' fill='%23b89c88'/></svg>">
    <style>
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1a1a24; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d2d3a; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        .nav-links a { color: #a0a0b0; text-decoration: none; margin: 0 15px; font-size: 14px; font-weight: bold; transition: 0.3s; }
        .nav-links a.active, .nav-links a:hover { color: #ffffff; }
        .page-header { text-align: center; padding: 50px 20px; }
        .page-header h1 { font-size: 32px; margin-bottom: 10px; }
        .quote-text { font-style: italic; font-size: 1.6rem; color: #b89c88; letter-spacing: 1.5px; display: inline-block; margin-top: 15px; overflow: hidden; white-space: nowrap; border-right: 2px solid transparent; animation: revealText 2.5s cubic-bezier(0.19, 1, 0.22, 1) forwards; }
        @keyframes revealText { from { width: 0; } to { width: 100%; } }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px 40px 60px; }
        .room-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
        .room-card { background-color: #1a1a24; border-radius: 12px; border: 1px solid #2d2d3a; overflow: hidden; transition: 0.3s; display: flex; flex-direction: column; }
        .room-card:hover { transform: translateY(-5px); border-color: #58493e; box-shadow: 0 10px 20px rgba(0,0,0,0.5); }
        .card-top { height: 180px; background-size: cover; background-position: center; border-bottom: 1px solid #2d2d3a; }
        .card-body { padding: 25px; flex-grow: 1; }
        .card-body h3 { margin: 0 0 10px; font-size: 20px; }
        .card-desc { color: #a0a0b0; font-size: 14px; line-height: 1.5; margin-bottom: 20px; }
        .card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #2d2d3a; padding-top: 20px; }
        .capacity { font-size: 13px; color: #a0a0b0; }
        .btn-view { color: #b89c88; text-decoration: none; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <div class="nav-links">
            <a href="index.php">Ana Sayfa</a>
            <a href="salonlar.php" class="active">Salonlar</a>
            <a href="rezervasyonlarim.php">Rezervasyonlarım</a>
        </div>
        <div class="user-menu">
            <span style="color:#a0a0b0; font-size:14px; margin-right:15px;">Merhaba, <?php echo htmlspecialchars($_SESSION["ad_soyad"]); ?></span>
            <a href="?cikis=1" style="color:#ff4d4d; text-decoration:none; font-weight:bold; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 6px;" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">Çıkış Yap</a>
        </div>
    </nav>

    <div class="page-header">
        <h1>Çalışma Salonları</h1>
        <div class="quote-text">"one day or day one..."</div>
    </div>

    <div class="container">
        <div class="room-grid">
            <div class="room-card">
                <div class="card-top" style="background-image: url('images/ana_salon.jpg');"></div>
                <div class="card-body"><h3>Ana Salon</h3><p class="card-desc">Geniş ve ferah genel çalışma alanı.</p><div class="card-footer"><span class="capacity">80 Masa</span><a href="salon_detay.php?isim=Ana+Salon" class="btn-view">Gör ></a></div></div>
            </div>
            <div class="room-card">
                <div class="card-top" style="background-image: url('images/sessiz_salon.jpg');"></div>
                <div class="card-body"><h3>Sessiz Salon</h3><p class="card-desc">Odaklanma için tamamen sessiz alan.</p><div class="card-footer"><span class="capacity">48 Masa</span><a href="salon_detay.php?isim=Sessiz+Salon" class="btn-view">Gör ></a></div></div>
            </div>
            <div class="room-card">
                <div class="card-top" style="background-image: url('images/grup_odasi.jpg');"></div>
                <div class="card-body"><h3>Grup Çalışma</h3><p class="card-desc">Proje ve ekip çalışmaları için yalıtılmış alan.</p><div class="card-footer"><span class="capacity">6 Masa</span><a href="salon_detay.php?isim=Grup+Çalışma+Salonu" class="btn-view">Gör ></a></div></div>
            </div>
            <div class="room-card">
                <div class="card-top" style="background-image: url('images/bilgisayar_odasi.jpg');"></div>
                <div class="card-body"><h3>Bilgisayar Salonu</h3><p class="card-desc">Yüksek performanslı çalışma alanı.</p><div class="card-footer"><span class="capacity">28 Masa</span><a href="salon_detay.php?isim=Bilgisayar+Salonu" class="btn-view">Gör ></a></div></div>
            </div>
        </div>
    </div>

</body>
</html>