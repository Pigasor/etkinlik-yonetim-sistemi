<?php
session_start(); // Oturumu başlatıyoruz

// Tüm oturum verilerini temizliyoruz
session_unset();

// Oturumu sonlandırıyoruz
session_destroy();

// Giriş sayfasına yönlendiriyoruz
header("Location: login.php");
exit;
?>
