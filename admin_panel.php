<?php
session_start();
require_once 'config.php';
date_default_timezone_set('Europe/Istanbul');


if (!isset($_SESSION["kullanici_id"]) || $_SESSION["rol"] != 'admin') {
    header("location: index.php");
    exit;
}

$bugun = date('Y-m-d');
$suan_saat = date('H:i:s');

// Tüm rezervasyonları kullanıcı ve masa bilgileriyle çekiyoruz
$sorgu = "SELECT r.*, m.masa_kodu, m.salon_adi, k.ad_soyad, k.email 
          FROM rezervasyonlar r 
          JOIN masalar m ON r.masa_id = m.id 
          JOIN kullanicilar k ON r.kullanici_id = k.id 
          ORDER BY r.id DESC"; // En yeni kullanıcı en üstte görünür

$sonuc = mysqli_query($db, $sorgu);//veri tabanına gönderme

$aktif_rez = [];
$gecmis_rez = [];
$iptal_rez = [];

while ($row = mysqli_fetch_assoc($sonuc)) {
    $bitis_zamani = strtotime($row['tarih'] . ' ' . $row['bitis_saati']);
    
    if ($row['iptal_edildi'] > 0) {
        $iptal_rez[] = $row;
    } elseif ($bitis_zamani > time()) {
        $aktif_rez[] = $row;
    } else {
        $gecmis_rez[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>LibReserve - Admin Yönetim Paneli</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect x='15' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='35' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='55' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><polygon points='75,20 87,20 97,80 85,80' fill='%23b89c88'/></svg>">
    
    <style>
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        
        /* NAVBAR */
        .navbar { background-color: #1a1a24; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d2d3a; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        .header-container { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; margin-top: 40px;}
        .header-container h1 { color: #b89c88; margin: 0; }
        
        /* ARAMA KUTUSU */
        .search-box { width: 100%; max-width: 400px; padding: 12px 20px; background-color: #1a1a24; border: 1px solid #2d2d3a; border-radius: 8px; color: #fff; font-size: 15px; outline: none; transition: 0.3s; font-family: inherit; }
        .search-box:focus { border-color: #b89c88; box-shadow: 0 0 10px rgba(184, 156, 136, 0.2); }
        .search-box::placeholder { color: #a0a0b0; }

        .section-title { border-left: 4px solid #58493e; padding-left: 15px; margin: 50px 0 20px; font-size: 22px; font-weight: bold; }
        
        /* TABLO TASARIMI */
        table { width: 100%; border-collapse: collapse; background-color: #1a1a24; border-radius: 12px; overflow: hidden; margin-bottom: 40px; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #2d2d3a; font-size: 14px; }
        th { background-color: #2d2d3a; color: #a0a0b0; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
        tr:hover { background-color: rgba(255, 255, 255, 0.02); }
        
        /* DURUM ROZETLERİ */
        .badge { padding: 5px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-aktif { background: rgba(46, 213, 115, 0.1); color: #2ed573; border: 1px solid #2ed573; }
        .badge-gecmis { background: rgba(160, 160, 176, 0.1); color: #a0a0b0; border: 1px solid #a0a0b0; }
        .badge-iptal { background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d; }
        .badge-otomatik { background: rgba(255, 165, 2, 0.1); color: #ffa502; border: 1px solid #ffa502; } 
        .badge-admin { background: rgba(155, 89, 182, 0.1); color: #9b59b6; border: 1px solid #9b59b6; } 
        
        .btn-cancel { color: #ff4d4d; text-decoration: none; font-weight: bold; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 4px; transition: 0.3s; font-size: 12px; }
        .btn-cancel:hover { background: #ff4d4d; color: white; }
        .empty-msg { color: #555; font-style: italic; padding: 20px; text-align: center; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span> - Admin Panel</div>
        <div style="display: flex; gap: 20px; align-items: center;">
            <a href="index.php" style="color: #a0a0b0; text-decoration: none; font-weight: bold;">Siteye Dön</a>
            <a href="cikis.php" style="color:#ff4d4d; text-decoration:none; font-weight:bold; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 6px;" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">Çıkış Yap</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="header-container">
            <h1>Rezervasyon Yönetimi</h1>
            <input type="text" id="aramaKutusu" class="search-box" placeholder="Öğrenci, Masa (A5) veya Salon Ara...">
        </div>

        <div class="section-title" style="color: #2ed573;">Aktif Rezervasyonlar</div>
        <table>
            <thead><tr><th>Öğrenci</th><th>Salon</th><th>Masa</th><th>Tarih</th><th>Saat Aralığı</th><th>Durum</th><th>İşlem</th></tr></thead>
            <tbody>
                <?php foreach ($aktif_rez as $rez): ?>
                <tr class="veri-satiri">
                    <td><strong><?php echo htmlspecialchars($rez['ad_soyad']); ?></strong><br><small style="color:#666;"><?php echo $rez['email']; ?></small></td>
                    <td><?php echo $rez['salon_adi']; ?></td>
                    <td><span style="color:#b89c88; font-weight:bold;"><?php echo $rez['masa_kodu']; ?></span></td>
                    <td><?php echo date('d.m.Y', strtotime($rez['tarih'])); ?></td>
                    <td><?php echo substr($rez['baslangic_saati'], 0, 5) . " - " . substr($rez['bitis_saati'], 0, 5); ?></td>
                    <td>
                        <?php if($rez['onaylandi'] == 1): ?>
                            <span class="badge badge-aktif">MASADA</span>
                        <?php else: ?>
                            <span class="badge" style="color:#ffa502; border: 1px solid #ffa502;">BEKLİYOR</span>
                        <?php endif; ?>
                    </td>
                    <td><a href="rezervasyon_iptal.php?id=<?php echo $rez['id']; ?>" class="btn-cancel" onclick="return confirm('İptal etmek istediğinizden emin misiniz?');">İPTAL ET</a></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($aktif_rez)) echo "<tr><td colspan='7' class='empty-msg'>Aktif randevu bulunmuyor.</td></tr>"; ?>
            </tbody>
        </table>

        <div class="section-title" style="color: #a0a0b0;">Geçmiş Rezervasyonlar</div>
        <table>
            <thead><tr><th>Öğrenci</th><th>Salon</th><th>Masa</th><th>Tarih</th><th>Saat Aralığı</th><th>Durum</th></tr></thead>
            <tbody>
                <?php foreach ($gecmis_rez as $rez): ?>
                <tr class="veri-satiri">
                    <td><strong><?php echo htmlspecialchars($rez['ad_soyad']); ?></strong><br><small style="color:#666;"><?php echo $rez['email']; ?></small></td>
                    <td><?php echo $rez['salon_adi']; ?></td>
                    <td><span style="color:#b89c88; font-weight:bold;"><?php echo $rez['masa_kodu']; ?></span></td>
                    <td><?php echo date('d.m.Y', strtotime($rez['tarih'])); ?></td>
                    <td><?php echo substr($rez['baslangic_saati'], 0, 5) . " - " . substr($rez['bitis_saati'], 0, 5); ?></td>
                    <td><span class="badge badge-gecmis">TAMAMLANDI</span></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($gecmis_rez)) echo "<tr><td colspan='6' class='empty-msg'>Geçmiş randevu bulunmuyor.</td></tr>"; ?>
            </tbody>
        </table>

        <div class="section-title" style="color: #ff4d4d;">İptal Edilen Rezervasyonlar</div>
        <table>
            <thead><tr><th>Öğrenci</th><th>Salon</th><th>Masa</th><th>Tarih</th><th>Saat Aralığı</th><th>Durum</th></tr></thead>
            <tbody>
                <?php foreach ($iptal_rez as $rez): ?>
                <tr class="veri-satiri">
                    <td><strong><?php echo htmlspecialchars($rez['ad_soyad']); ?></strong><br><small style="color:#666;"><?php echo $rez['email']; ?></small></td>
                    <td><?php echo $rez['salon_adi']; ?></td>
                    <td><span style="color:#b89c88; font-weight:bold;"><?php echo $rez['masa_kodu']; ?></span></td>
                    <td><?php echo date('d.m.Y', strtotime($rez['tarih'])); ?></td>
                    <td><?php echo substr($rez['baslangic_saati'], 0, 5) . " - " . substr($rez['bitis_saati'], 0, 5); ?></td>
                    <td>
                        <?php if($rez['iptal_edildi'] == 2): ?>
                            <span class="badge badge-otomatik">ZAMAN AŞIMINDAN İPTAL</span>
                        <?php elseif($rez['iptal_edildi'] == 3): ?>
                            <span class="badge badge-admin">ADMİN İPTALİ</span>
                        <?php else: ?>
                            <span class="badge badge-iptal">İPTAL EDİLDİ</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($iptal_rez)) echo "<tr><td colspan='6' class='empty-msg'>İptal edilen randevu bulunmuyor.</td></tr>"; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.getElementById('aramaKutusu').addEventListener('input', function() {
            let arananDeger = this.value.toLocaleLowerCase('tr-TR');
            let tabloSatirlari = document.querySelectorAll('.veri-satiri');

            tabloSatirlari.forEach(function(satir) {
                let satirMetni = satir.textContent.toLocaleLowerCase('tr-TR');
                if(satirMetni.includes(arananDeger)) {
                    satir.style.display = '';
                } else {
                    satir.style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>