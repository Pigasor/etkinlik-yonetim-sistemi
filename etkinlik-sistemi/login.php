<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kullanıcıyı bul
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            if (!$user['is_approved']) {
                echo "Hesabınız henüz onaylanmamış.";
                exit;
            }

            // Giriş başarılıysa session'a kullanıcı bilgilerini yaz
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['is_approved'] = $user['is_approved'];
            $_SESSION['must_change_password'] = $user['must_change_password'];

            // Şifre değiştirilmesi gerekiyorsa yönlendir
            if ($user['must_change_password']) {
                header("Location: change-password.php");
                exit;
            }

            // Yönlendirme
            if ($user['is_admin'] == 1) {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            echo "Yanlış şifre.";
        }
    } else {
        echo "Böyle bir kullanıcı bulunamadı.";
    }
}
?>

<!-- Giriş Formu -->
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="etkinlik.css">
</head>
<body>
    <div class="login-container">
        <h2>Giriş Yap</h2>
        <form method="POST" action="login.php">
            <label for="email">E-posta:</label>
            <input type="text" name="email" id="email" required><br><br>

            <label for="password">Şifre:</label>
            <input type="password" name="password" id="password" required><br><br>

            <button type="submit">Giriş Yap</button>
        </form>
    </div>
</body>
</html>
