<?php
session_start();
require_once 'config.php';
date_default_timezone_set('Europe/Istanbul');

if (!isset($_SESSION["kullanici_id"])) {
    header("location: login.php");
    exit;
}

$kullanici_id = $_SESSION["kullanici_id"];
$bugun = date('Y-m-d');
$suan_saat = date('H:i:s');

$sorgu = "SELECT r.*, m.masa_kodu, m.salon_adi FROM rezervasyonlar r 
          JOIN masalar m ON r.masa_id = m.id 
          WHERE r.kullanici_id = ? 
          ORDER BY r.id DESC";

$stmt = mysqli_prepare($db, $sorgu);
mysqli_stmt_bind_param($stmt, "i", $kullanici_id);
mysqli_stmt_execute($stmt);
$sonuc = mysqli_stmt_get_result($stmt);

$aktif_rez = [];
$gecmis_rez = [];
$iptal_rez = [];

while ($row = mysqli_fetch_assoc($sonuc)) {
    $is_iptal = isset($row['iptal_edildi']) ? $row['iptal_edildi'] : 0;
    $bitis_zamani = strtotime($row['tarih'] . ' ' . $row['bitis_saati']);
    
    if ($is_iptal > 0) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LibReserve - Rezervasyonlarım</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect x='15' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='35' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><rect x='55' y='20' width='12' height='60' fill='%23b89c88' rx='2'/><polygon points='75,20 87,20 97,80 85,80' fill='%23b89c88'/></svg>">
    <style>
        body { margin: 0; background-color: #0d0d12; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #1a1a24; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #2d2d3a; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 22px; font-weight: bold; }
        .logo span { color: #58493e; }
        .nav-links a { color: #a0a0b0; text-decoration: none; margin: 0 15px; font-size: 14px; font-weight: bold; transition: 0.3s; }
        .nav-links a.active, .nav-links a:hover { color: #ffffff; }

        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        h1 { text-align: center; color: #b89c88; margin-bottom: 50px; font-size: 32px; }
        .section-title { border-left: 4px solid #58493e; padding-left: 15px; margin: 40px 0 20px; font-size: 20px; font-weight: bold; }
        
        .rez-card { background-color: #1a1a24; border: 1px solid #2d2d3a; border-radius: 12px; padding: 25px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; transition: 0.3s; }
        .rez-card:hover { border-color: #58493e; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .rez-info h3 { margin: 0 0 8px 0; font-size: 20px; }
        .rez-info p { margin: 0; color: #a0a0b0; font-size: 14px; line-height: 1.6; }
        
        .rez-actions { display: flex; flex-direction: column; gap: 10px; min-width: 150px; }
        .btn-here { background-color: #58493e; color: white; border: none; padding: 12px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; text-align: center; }
        .btn-here:hover:not(:disabled) { background-color: #2ed573; color: #0d0d12; }
        .btn-here:disabled { background-color: #2d2d3a; color: #666; cursor: not-allowed; border: 1px solid #3d3d4a; }
        .btn-cancel { background-color: transparent; color: #ff4d4d; border: 1px solid #ff4d4d; padding: 10px; border-radius: 8px; cursor: pointer; text-decoration: none; font-size: 13px; font-weight: bold; text-align: center; transition: 0.3s; }
        .btn-cancel:hover { background-color: #ff4d4d; color: white; }
        
        .status-tag { font-weight: bold; font-size: 14px; text-align: center; padding: 10px; border-radius: 8px; }
        .onayli { color: #2ed573; background: rgba(46, 213, 115, 0.1); }
        .beklemede { color: #ffa502; font-size: 12px; margin-bottom: 2px; text-align: center; }
        .empty-msg { color: #555; font-style: italic; margin-left: 20px; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo">|||\ Lib<span>Reserve</span></div>
        <div class="nav-links">
            <a href="index.php">Ana Sayfa</a>
            <a href="salonlar.php">Salonlar</a>
            <a href="rezervasyonlarim.php" class="active">Rezervasyonlarım</a>
            <?php if(isset($_SESSION["rol"]) && $_SESSION["rol"] == 'admin'): ?>
                <a href="admin_panel.php" style="color: #ff4d4d !important; border: 1px solid #ff4d4d; padding: 5px 12px; border-radius: 6px;">Yönetim Paneli</a>
            <?php endif; ?>
        </div>
        <div class="user-menu">
            <span style="color:#a0a0b0; font-size:14px; margin-right: 15px;">Merhaba, <?php echo htmlspecialchars($_SESSION["ad_soyad"]); ?></span>
            <a href="cikis.php" style="color:#ff4d4d; text-decoration:none; font-weight:bold; border: 1px solid #ff4d4d; padding: 5px 10px; border-radius: 6px;" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?');">Çıkış Yap</a>
        </div>
    </nav>

    <div class="container">
        <h1>Rezervasyonlarım</h1>

        <div class="section-title">Aktif Rezervasyonlar</div>
        <?php if(empty($aktif_rez)): ?>
            <p class="empty-msg">Şu an aktif bir rezervasyonunuz bulunmuyor.</p>
        <?php endif; ?>
        
        <?php foreach ($aktif_rez as $rez): 
            $vakit_geldi_mi = ($rez['tarih'] == $bugun && $suan_saat >= $rez['baslangic_saati']);
        ?>
            <div class="rez-card" style="border-left: 5px solid #58493e;">
                <div class="rez-info">
                    <h3><?php echo htmlspecialchars($rez['salon_adi']); ?> - Masa <?php echo htmlspecialchars($rez['masa_kodu']); ?></h3>
                    <p><strong>Tarih:</strong> <?php echo date('d.m.Y', strtotime($rez['tarih'])); ?></p>
                    <p><strong>Saat:</strong> <?php echo substr($rez['baslangic_saati'], 0, 5); ?> - <?php echo substr($rez['bitis_saati'], 0, 5); ?></p>
                </div>
                <div class="rez-actions">
                    <?php if ($rez['onaylandi'] == 0): ?>
                        <?php if ($vakit_geldi_mi): ?>
                            <button class="btn-here" onclick="location.href='rezervasyon_onayla.php?id=<?php echo $rez['id']; ?>'">Yerimdeyim</button>
                        <?php else: ?>
                            <div class="beklemede">Vakti Bekleniyor...</div>
                            <button class="btn-here" disabled>Yerimdeyim</button>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="status-tag onayli">✓ Onaylandı</div>
                    <?php endif; ?>
                    <a href="rezervasyon_iptal.php?id=<?php echo $rez['id']; ?>" class="btn-cancel" onclick="return confirm('Bu rezervasyonu iptal etmek istediğinizden emin misiniz?');">İptal Et</a>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="section-title">Geçmiş Rezervasyonlar</div>
        <?php if(empty($gecmis_rez)): ?>
            <p class="empty-msg">Geçmişe ait bir kayıt bulunamadı.</p>
        <?php endif; ?>
        
        <?php foreach ($gecmis_rez as $rez): ?>
            <div class="rez-card" style="opacity: 0.7; border-left: 5px solid #333;">
                <div class="rez-info">
                    <h3><?php echo htmlspecialchars($rez['salon_adi']); ?> - Masa <?php echo htmlspecialchars($rez['masa_kodu']); ?></h3>
                    <p><?php echo date('d.m.Y', strtotime($rez['tarih'])); ?> | <?php echo substr($rez['baslangic_saati'], 0, 5); ?> - <?php echo substr($rez['bitis_saati'], 0, 5); ?></p>
                </div>
                <div style="color: #666; font-weight: bold;">Tamamlandı</div>
            </div>
        <?php endforeach; ?>

        <div class="section-title">İptal Edilen Rezervasyonlar</div>
        <?php if(empty($iptal_rez)): ?>
            <p class="empty-msg">İptal edilen rezervasyonunuz bulunmuyor.</p>
        <?php endif; ?>
        
        <?php foreach ($iptal_rez as $rez): ?>
            <div class="rez-card" style="opacity: 0.6; border-left: 5px solid #ff4d4d;">
                <div class="rez-info">
                    <h3><?php echo htmlspecialchars($rez['salon_adi']); ?> - Masa <?php echo htmlspecialchars($rez['masa_kodu']); ?></h3>
                    <p><?php echo date('d.m.Y', strtotime($rez['tarih'])); ?> | <?php echo substr($rez['baslangic_saati'], 0, 5); ?> - <?php echo substr($rez['bitis_saati'], 0, 5); ?></p>
                </div>
                <div style="color: #ff4d4d; font-weight: bold;">
                    <?php 
                        if ($rez['iptal_edildi'] == 2) {
                            echo 'Zaman Aşımından Otomatik İptal';
                        } elseif ($rez['iptal_edildi'] == 3) {
                            echo '<span style="color: #9b59b6;">Admin İptali</span>';
                        } else {
                            echo 'İptal Edildi';
                        }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>