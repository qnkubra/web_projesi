<?php
session_start();
require_once 'config.php';

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
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - Çalışma Salonları</title>
    <style>
        /* TEMEL AYARLAR */
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
        .nav-links a:hover, .nav-links a.active { 
            color: #ffffff; 
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }
        .user-menu { display: flex; align-items: center; gap: 20px; }
        .user-info { color: #a0a0b0; font-size: 14px; }
        .logout-btn { background-color: rgba(255, 77, 77, 0.1); color: #ff4d4d; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; border: 1px solid #ff4d4d; transition: all 0.3s; }
        .logout-btn:hover { background-color: #ff4d4d; color: white; }

        /* SAYFA BAŞLIĞI VE MOTİVASYON SÖZÜ */
        .page-header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 40px 10px 40px; /* Alt boşluğu biraz azalttık */
            text-align: center; /* Her şeyi ortaladık */
        }
        .page-title h1 { margin: 0 0 15px 0; font-size: 36px; letter-spacing: -1px; }
        .page-title p { margin: 0 0 20px 0; color: #a0a0b0; font-size: 16px; }
        
        /* --- YENİ MOTİVASYON SÖZÜ VE ANİMASYONU --- */
        .quote-text {
            font-style: italic; /* İtalik */
            color: #b89c88; /* Temamızın açık kahve/bej tonu */
            font-size: 1.2rem;
            margin-top: 20px;
            letter-spacing: 1px;
            display: inline-block;
            /* Parlama Animasyonu */
            animation: subtleGlow 3s ease-in-out infinite;
        }

        @keyframes subtleGlow {
            0%, 100% {
                opacity: 0.7;
                text-shadow: 0 0 5px rgba(184, 156, 136, 0.2);
            }
            50% {
                opacity: 1;
                text-shadow: 0 0 15px rgba(184, 156, 136, 0.6);
            }
        }
        /* ------------------------------------------ */

        /* SALON KARTLARI DÜZENİ */
        .container { padding: 40px; max-width: 1200px; margin: 0 auto; }
        .room-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; }
        
        .room-card { 
            background-color: #1a1a24; 
            border-radius: 12px; 
            overflow: hidden; 
            border: 1px solid #2d2d3a; 
            transition: transform 0.3s, border-color 0.3s; 
            display: flex; 
            flex-direction: column;
        }
        .room-card:hover { transform: translateY(-5px); border-color: #58493e; box-shadow: 0 10px 20px rgba(0,0,0,0.5); }

        .card-top { 
            height: 180px; 
            background-size: cover; 
            background-position: center; 
            border-bottom: 1px solid #2d2d3a; 
        }

        .card-body { padding: 25px; display: flex; flex-direction: column; flex-grow: 1;}
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .card-header h3 { margin: 0; font-size: 20px; color: #ffffff; }
        
        .badge { 
            background-color: rgba(88, 73, 62, 0.2); 
            color: #b89c88; 
            padding: 5px 12px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: bold; 
            border: 1px solid rgba(88, 73, 62, 0.4); 
        }
        
        .card-desc { color: #a0a0b0; font-size: 14px; margin-bottom: 25px; line-height: 1.5; flex-grow: 1;}

        .card-footer { 
            display: flex; justify-content: space-between; align-items: center; 
            border-top: 1px solid #2d2d3a; padding-top: 20px; margin-top: auto;
        }
        
        .capacity { color: #a0a0b0; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .capacity svg { width: 16px; height: 16px; fill: #a0a0b0; }
        
        .btn-view { 
            color: #b89c88; text-decoration: none; font-size: 14px; font-weight: bold; 
            transition: color 0.3s; display: flex; align-items: center; gap: 5px;
        }
        .btn-view:hover { color: #ffffff; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <div class="nav-links">
            <a href="index.php">Ana Sayfa</a>
            <a href="salonlar.php" class="active">Salonlar</a>
            <a href="#">Hakkında</a>
            <a href="#">Rezervasyonlarım</a>
            <?php if($_SESSION["rol"] == 'admin'): ?>
                <a href="admin_panel.php" style="color: #ff4d4d; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 6px;">YÖNETİM PANELİ</a>
            <?php endif; ?>
        </div>
        <div class="user-menu">
            <span class="user-info">Merhaba, <?php echo $_SESSION["ad_soyad"]; ?></span>
            <a href="?cikis=1" class="logout-btn">Çıkış Yap</a>
        </div>
    </nav>

    <div class="page-header-container">
        <div class="page-title">
            <h1>Çalışma Salonları</h1>
            <p>Sana en uygun çalışma ortamını seç.</p>
        </div>
        <div class="quote-text">"one day or day one..."</div>
    </div>

    <div class="container">
        <div class="room-grid">
            
            <div class="room-card">
                <div class="card-top" style="background-image: url('images/ana_salon.jpg');"></div>
                <div class="card-body">
                    <div class="card-header">
                        <h3>Ana Salon</h3>
                        <span class="badge">Genel</span>
                    </div>
                    <div class="card-desc">Kütüphanenin en geniş ve ferah genel çalışma alanı. Açık alanda çalışmayı sevenler için idealdir.</div>
                    <div class="card-footer">
                        <div class="capacity">
                            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            80 Masa Mevcut
                        </div>
                        <a href="salon_detay.php?isim=Ana+Salon" class="btn-view">Masaları Gör ></a>
                    </div>
                </div>
            </div>

            <div class="room-card">
                <div class="card-top" style="background-image: url('images/grup_odasi.jpg');"></div>
                <div class="card-body">
                    <div class="card-header">
                        <h3>Grup Çalışma Salonu</h3>
                        <span class="badge">Grup</span>
                    </div>
                    <div class="card-desc">Sunum hazırlıkları, proje toplantıları ve sesli tartışmalı grup çalışmaları için özel olarak yalıtılmış oda.</div>
                    <div class="card-footer">
                        <div class="capacity">
                            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            6 Masa Mevcut
                        </div>
                        <a href="salon_detay.php?isim=Grup+Çalışma+Salonu" class="btn-view">Masaları Gör ></a>
                    </div>
                </div>
            </div>

            <div class="room-card">
                <div class="card-top" style="background-image: url('images/sessiz_salon.jpg');"></div>
                <div class="card-body">
                    <div class="card-header">
                        <h3>Sessiz Salon</h3>
                        <span class="badge">Sessiz</span>
                    </div>
                    <div class="card-desc">Bireysel odaklanma ve sınav dönemlerindeki yoğun çalışmalar için tasarlanmış, çıt çıkmayan tamamen sessiz alan.</div>
                    <div class="card-footer">
                        <div class="capacity">
                            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            48 Masa Mevcut
                        </div>
                        <a href="salon_detay.php?isim=Sessiz+Salon" class="btn-view">Masaları Gör ></a>
                    </div>
                </div>
            </div>

            <div class="room-card">
                <div class="card-top" style="background-image: url('images/bilgisayar_odasi.jpg');"></div>
                <div class="card-body">
                    <div class="card-header">
                        <h3>Bilgisayar Salonu</h3>
                        <span class="badge">Teknoloji</span>
                    </div>
                    <div class="card-desc">Yüksek performanslı masaüstü bilgisayarların bulunduğu, araştırma ve yazılım geliştirme çalışmaları için uygun salon.</div>
                    <div class="card-footer">
                        <div class="capacity">
                            <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            28 Masa Mevcut
                        </div>
                        <a href="salon_detay.php?isim=Bilgisayar+Salonu" class="btn-view">Masaları Gör ></a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>