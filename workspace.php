<?php
session_start();
require __DIR__ . '/template/header.php';
// * Bỏ include sidebar để không hiển thị
?>
<div class="main-content no-sidebar-page">
  <!-- CSS riêng cho workspace -->
  <link rel="stylesheet" href="public/css/workspace.css">

  <div class="workspace-container">
    <div class="welcome-card">
      <h1>Hi, welcome back!</h1>
    </div>
  </div>

  <script src="public/js/workspace.js"></script>
</div>
<?php require __DIR__ . '/template/footer.php'; ?>
