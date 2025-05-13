<?php
session_start();
if (empty($_SESSION['user'])) {
    header('Location: ../signin.php');
    exit;
}
include __DIR__.'/../template/head.php';
include __DIR__.'/../template/header.php';
?>

<h2>Dashboard</h2>
<p>Chào, <?= htmlspecialchars($_SESSION['user']['firstname']) ?>!</p>
<p>Lần đăng nhập cuối: 
  <?= $_SESSION['user']['last_login']
        ? date('H:i d/m/Y', strtotime($_SESSION['user']['last_login']))
        : 'chưa có' ?>
</p>

<?php include __DIR__.'/../template/footer.php'; ?>
