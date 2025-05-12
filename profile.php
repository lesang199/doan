<?php
require_once 'config/database.php';
session_start();

// Kiểm tra session email
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

try {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger text-center">Có lỗi xảy ra: ' . $e->getMessage() . '</div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thông tin cá nhân</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow position-relative">
        <!-- Header -->
        <div class="card-header bg-primary text-white text-center">
          <h3>Thông tin cá nhân</h3>
        </div>

        <!-- Avatar -->
        <div class="text-center mt-4 profile-avatar">
          <img src="./images/IMG_E5015.JPG" alt="Avatar" class="rounded-circle border border-3 border-white shadow" width="120" height="120">
        </div>

        <!-- Body -->
        <?php if (!empty($user)): ?>
          <?php foreach ($user as $userAct): ?>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><strong>Họ và tên:</strong> <?= htmlspecialchars($userAct['full_name']) ?></li>
              <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($userAct['email']) ?></li>
              <li class="list-group-item"><strong>Số điện thoại:</strong> <?= htmlspecialchars($userAct['phone']) ?></li>
              <li class="list-group-item"><strong>Địa chỉ:</strong> <?= htmlspecialchars($userAct['address']) ?></li>
            </ul>

            <!-- Buttons -->
            <div class="text-center mt-4">
              <a href="edit_profile.php" class="btn btn-warning me-2">Chỉnh sửa</a>
              <a href="logout.php" class="btn btn-danger me-2">Đăng xuất</a>
              <a href="index.php" class="btn btn-secondary">Quay lại trang chủ</a>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="card-body text-center">
            <p class="text-danger">Không tìm thấy thông tin người dùng.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>