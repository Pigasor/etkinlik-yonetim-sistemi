<?php
session_start();
require 'config.php';

// Basit yetki kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Bu sayfaya erişim yetkiniz yok.";
    exit();
}

// Kullanıcıyı onayla işlemi
if (isset($_GET['onayla'])) {
    $user_id = intval($_GET['onayla']);
    $stmt = $pdo->prepare("UPDATE users SET is_approved = 1 WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: admin-onay.php");
    exit();
}

// Onay bekleyen kullanıcıları çek
$stmt = $pdo->query("SELECT id, email FROM users WHERE is_approved = 0");
$bekleyenler = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kullanıcı Onayı</title>
    <link rel="stylesheet" href="etkinlik.css">
</head>
<body>
    <div class="container">
        <h2>Onay Bekleyen Kullanıcılar</h2>
        <?php if (empty($bekleyenler)): ?>
            <p>Şu anda onay bekleyen kullanıcı yok.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>E-mail</th>
                    <th>İşlem</th>
                </tr>
                <?php foreach ($bekleyenler as $kullanici): ?>
                    <tr>
                        <td><?= htmlspecialchars($kullanici['email']) ?></td>
                        <td><a href="admin-onay.php?onayla=<?= $kullanici['id'] ?>">Onayla</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
