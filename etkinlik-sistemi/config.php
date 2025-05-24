<?php
// Veritabanı Bağlantı Ayarları
$host = '127.0.0.1';
$db   = 'etkinlikDB'; // Veritabanı adı
$user = 'root';       // MySQL kullanıcı adı
$pass = '';           // MySQL şifresi
$charset = 'utf8mb4';

// PDO ile bağlantı kuruyoruz
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // PDO nesnesini oluşturuyoruz
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Bağlantı hatası durumunda hata mesajını gösteriyoruz
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
