<?php
session_start();
require_once 'config.php';

// GÜVENLİK KONTROLÜ: Kullanıcı giriş yapmamışsa VEYA rolü 'admin' değilse bu sayfaya giremez!
if (!isset($_SESSION["kullanici_id"]) || $_SESSION["rol"] !== 'admin') {
    // Admin değilse veya giriş yapmamışsa ana sayfaya (veya logine) geri gönder
    header("location: index.php"); 
    exit;
}

// Çıkış yapma işlemi
if (isset($_GET['cikis'])) {
    session_destroy();
    header("location: login.php");
    exit;
}

// Veritabanından basit istatistikler çekelim
// 1. Toplam Öğrenci Sayısı
$ogrenci_sorgu = mysqli_query($db, "SELECT COUNT(id) as sayi FROM kullanicilar WHERE rol = 'ogrenci'");
$ogrenci_sayisi = mysqli_fetch_assoc($ogrenci_sorgu)['sayi'];

// 2. Toplam Masa Sayısı
$masa_sorgu = mysqli_query($db, "SELECT COUNT(id) as sayi FROM masalar");
$masa_sayisi = mysqli_fetch_assoc($masa_sorgu)['sayi'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - Yönetim Paneli</title>
    <style>
        /* TEMEL AYARLAR (Ana temamızla aynı) */
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* NAVBAR */
        .navbar { background-color: #1a1a24; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d2d3a; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        .nav-links { display: flex; align-items: center; }
        .nav-links a { color: #a0a0b0; text-decoration: none; margin: 0 15px; font-size: 14px; font-weight: bold; transition: color 0.3s; }
        .nav-links a:hover { color: #ffffff; }
        /* Admin Paneli Butonu (Aktif olarak vurgulu) */
        .nav-links a.admin-active { color: #b89c88; border: 1px solid #b89c88; padding: 5px 10px; border-radius: 6px; background-color: rgba(88, 73, 62, 0.1); }
        
        .user-menu { display: flex; align-items: center; gap: 20px; }
        .user-info { color: #b89c88; font-size: 14px; font-weight: bold; display: flex; align-items: center; gap: 8px;}
        .logout-btn { background-color: rgba(255, 77, 77, 0.1); color: #ff4d4d; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; border: 1px solid #ff4d4d; transition: all 0.3s; }
        .logout-btn:hover { background-color: #ff4d4d; color: white; }

        /* DASHBOARD (Yönetim Paneli) DÜZENİ */
        .container { padding: 40px; max-width: 1200px; margin: 0 auto; }
        
        /* Üst İstatistik Kartları */
        .dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background-color: #1a1a24; border: 1px solid #2d2d3a; border-radius: 12px; padding: 25px; display: flex; justify-content: space-between; align-items: center; transition: transform 0.3s;}
        .stat-card:hover { transform: translateY(-5px); border-color: #58493e; }
        .stat-info h3 { margin: 0 0 5px 0; color: #a0a0b0; font-size: 13px; text-transform: uppercase; letter-spacing: 1px; }
        .stat-info .number { font-size: 32px; font-weight: bold; color: #ffffff; margin: 0; }
        .stat-icon { background-color: rgba(88, 73, 62, 0.15); width: 50px; height: 50px; border-radius: 12px; display: flex; justify-content: center; align-items: center; }
        .stat-icon svg { width: 24px; height: 24px; fill: #b89c88; }

        /* Yönetim Menüsü Sekmeleri */
        .control-center { background-color: #1a1a24; border: 1px solid #2d2d3a; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; padding: 10px 20px;}
        .control-title { font-size: 18px; font-weight: bold; color: #ffffff; display: flex; align-items: center; gap: 10px; margin-right: auto;}
        .control-title::before { content: ''; display: inline-block; width: 4px; height: 20px; background-color: #58493e; border-radius: 2px; }
        
        .control-tabs { display: flex; gap: 10px; }
        .tab-btn { background: transparent; border: none; color: #a0a0b0; font-size: 14px; font-weight: bold; padding: 10px 20px; cursor: pointer; border-radius: 6px; transition: all 0.3s; display: flex; align-items: center; gap: 8px;}
        .tab-btn:hover { background-color: rgba(255,255,255,0.05); color: #ffffff; }
        .tab-btn.active { background-color: #58493e; color: #ffffff; }

        /* Tablo Tasarımı */
        .table-container { background-color: #1a1a24; border: 1px solid #2d2d3a; border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: rgba(255,255,255,0.02); color: #a0a0b0; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; padding: 20px; border-bottom: 1px solid #2d2d3a; }
        td { padding: 20px; border-bottom: 1px solid #2d2d3a; color: #ffffff; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: rgba(255,255,255,0.02); }
        
        /* Tablo İçi Rozetler */
        .status-badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-waiting { background-color: rgba(255, 165, 2, 0.15); color: #ffa502; border: 1px solid rgba(255, 165, 2, 0.4); }
        .status-active { background-color: rgba(46, 213, 115, 0.15); color: #2ed573; border: 1px solid rgba(46, 213, 115, 0.4); }
        
        /* İşlem Butonları */
        .action-btn { background: transparent; border: none; cursor: pointer; padding: 5px; margin-right: 5px; border-radius: 4px; transition: background 0.2s;}
        .action-btn.approve { color: #2ed573; }
        .action-btn.approve:hover { background-color: rgba(46, 213, 115, 0.1); }
        .action-btn.reject { color: #ff4d4d; }
        .action-btn.reject:hover { background-color: rgba(255, 77, 77, 0.1); }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <div class="nav-links">
            <a href="index.php">Ana Sayfa</a>
            <a href="salonlar.php">Salonlar</a>
            <a href="#" class="admin-active">YÖNETİM PANELİ</a>
        </div>
        <div class="user-menu">
            <div class="user-info">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg>
                Sistem Yöneticisi (<?php echo $_SESSION["ad_soyad"]; ?>)
            </div>
            <a href="?cikis=1" class="logout-btn">Çıkış Yap</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="dashboard-cards">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>Toplam Öğrenci</h3>
                    <p class="number"><?php echo $ogrenci_sayisi; ?></p>
                </div>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3>Sistemdeki Masalar</h3>
                    <p class="number"><?php echo $masa_sayisi; ?></p>
                </div>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/></svg>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <h3>Sistem Durumu</h3>
                    <p class="number" style="color: #2ed573; font-size: 24px;">Online</p>
                </div>
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
                </div>
            </div>
        </div>

        <div class="control-center">
            <div class="control-title">SİSTEM KONTROL MERKEZİ</div>
            <div class="control-tabs">
                <button class="tab-btn">Kullanıcılar</button>
                <button class="tab-btn active">Rezervasyonlar</button>
                <button class="tab-btn">Masalar</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Masa Detayı</th>
                        <th>Kullanıcı Bilgisi</th>
                        <th>Zaman Dilimi</th>
                        <th>Sistem Durumu</th>
                        <th>Eylem</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong style="color: #ff4d4d; margin-right: 5px;">B13</strong> 
                            <br><span style="font-size: 12px; color: #a0a0b0;">Ana Salon (Zemin Kat)</span>
                        </td>
                        <td>Kübra Özaslan</td>
                        <td>2026-05-08<br><span style="font-size: 12px; color: #a0a0b0;">20:03 - 22:03</span></td>
                        <td><span class="status-badge status-waiting">Bekliyor</span></td>
                        <td>
                            <button class="action-btn approve" title="Onayla"><svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/></svg></button>
                            <button class="action-btn reject" title="İptal Et"><svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg></button>
                        </td>
                    </tr>
                    </tbody>
            </table>
        </div>

    </div>

</body>
</html>