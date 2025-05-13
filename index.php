<?php
session_start();
if (isset($_GET['logout'])) {
  session_destroy();
  header('Location: signin.php');
  exit;
}
if (empty($_SESSION['user_id'])) {
  header('Location: signin.php');
  exit;
}
?>
<?php require 'template/header.php'; ?>

<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
  <div class="text-center p-4" style="color: #f8f8f2;">
    <h1 style="font-size: 2.5rem; font-weight: bold; color: #8be9fd; text-shadow: 0 0 10px #50fa7b;">
      👋 Chào mừng bạn, user #<?= $_SESSION['user_id'] ?>
    </h1>
    <a href="index.php?logout=1" class="btn mt-4 px-4 py-2"
      style="background: linear-gradient(to right, #bd93f9, #ff79c6); border: none; color: white; font-weight: bold; border-radius: 6px; transition: transform 0.3s ease;">
      Đăng xuất
    </a>
  </div>
</div>

<style>
  a.btn:hover {
    transform: scale(1.05);
    box-shadow: 0 0 10px #ff79c6;
  }
</style>

<?php require 'template/footer.php'; ?>
