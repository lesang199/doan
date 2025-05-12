<?php
require_once 'config/database.php';
session_start();

// Kiểm tra session email
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['email'];

try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Chỉnh sửa thông tin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-warning text-dark text-center">
          <h3>Chỉnh sửa thông tin</h3>
        </div>
        <?php if (!empty($user)): ?>
        <div class="card-body">
          <form action="update_profile.php" method="POST">
            <div class="mb-3">
              <label for="fullname" class="form-label">Họ và tên</label>
              <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email (không thể thay đổi)</label>
              <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
              <input type="hidden" name="email" value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Số điện thoại</label>
              <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="mb-3">
              <label for="address" class="form-label">Địa chỉ</label>
              <textarea name="address" class="form-control"><?= htmlspecialchars($user['address']) ?></textarea>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-success">Lưu thay đổi</button>
              <a href="profile.php" class="btn btn-secondary">Quay lại</a>
            </div>
          </form>
        </div>
        <?php else: ?>
        <div class="card-body text-center">
          <p class="text-danger">Không tìm thấy thông tin người dùng.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>