<?php
session_start();
require_once 'config.php';

if (isset($_GET['id'])) {
    $rez_id = intval($_GET['id']);
    mysqli_query($db, "UPDATE rezervasyonlar SET onaylandi = 1 WHERE id = $rez_id");
    header("location: rezervasyonlarim.php?onay=basarili");
}
?>