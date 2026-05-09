<?php
session_start();
// Tüm oturum değişkenlerini temizle
$_SESSION = array();
// Oturumu tamamen yok et
session_destroy();
// Login sayfasına yönlendir
header("location: login.php");
exit;
?>