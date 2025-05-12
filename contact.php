<!-- filepath: c:\xampp\htdocs\doan\contact.php -->

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
  <title>Liên hệ</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
          <h3>Thông tin liên hệ</h3>
        </div>
        <div class="card-body">
          <h5>Thông tin liên hệ</h5>
          <p><strong>Địa chỉ:</strong> 123 Đường ABC, Quận 1, TP.HCM</p>
          <p><strong>Email:</strong> contact@example.com</p>
          <p><strong>Số điện thoại:</strong> 0123 456 789</p>
          <hr>
          <h5>Gửi tin nhắn cho chúng tôi</h5>
          <form action="process_contact.php" method="POST">
            <div class="mb-3">
              <label for="name" class="form-label">Họ và tên</label>
              <input type="text" name="name" class="form-control" placeholder="Nhập họ và tên" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="Nhập email" required>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Số điện thoại</label>
              <input type="text" name="phone" class="form-control" placeholder="Nhập Số điện thoại" required>
            </div>
            <div class="mb-3">
              <label for="message" class="form-label">Nội dung</label>
              <textarea name="message" class="form-control" rows="5" placeholder="Nhập nội dung tin nhắn" required></textarea>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-success">Gửi tin nhắn</button>
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