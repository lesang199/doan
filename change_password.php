<!-- filepath: c:\xampp\htdocs\doan\change_password.php -->
<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đổi mật khẩu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
          <h3>Đổi mật khẩu</h3>
        </div>
        <div class="card-body">
          <form action="process_change_password.php" method="POST">
            <div class="mb-3">
              <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
              <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại" required>
            </div>
            <div class="mb-3">
              <label for="new_password" class="form-label">Mật khẩu mới</label>
              <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới" required>
            </div>
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="Xác nhận mật khẩu mới" required>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-success">Đổi mật khẩu</button>
              <a href="index.php" class="btn btn-secondary">Quay lại</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>