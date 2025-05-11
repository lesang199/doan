<!-- filepath: c:\xampp\htdocs\doan\process_change_password.php -->
<?php
require_once 'config/database.php';
session_start();

// Kiểm tra session email
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Kiểm tra dữ liệu từ form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email'];
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Kiểm tra các trường không được để trống
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.location.href='change_password.php';</script>";
        exit;
    }

    // Kiểm tra độ dài mật khẩu mới
    if (strlen($new_password) < 8) {
        echo "<script>alert('Mật khẩu mới phải có ít nhất 8 ký tự!'); window.location.href='change_password.php';</script>";
        exit;
    }

    // Kiểm tra mật khẩu mới và xác nhận mật khẩu mới có khớp không
    if ($new_password !== $confirm_password) {
        echo "<script>alert('Mật khẩu mới và xác nhận mật khẩu không khớp!'); window.location.href='change_password.php';</script>";
        exit;
    }

    try {
        // Lấy mật khẩu hiện tại từ cơ sở dữ liệu
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['password'])) {
            // Mã hóa mật khẩu mới
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu mới vào cơ sở dữ liệu
            $update_stmt = $conn->prepare("UPDATE users SET password = :new_password WHERE email = :email");
            $update_stmt->bindParam(':new_password', $hashed_password, PDO::PARAM_STR);
            $update_stmt->bindParam(':email', $email, PDO::PARAM_STR);

            if ($update_stmt->execute()) {
                echo "<script>alert('Đổi mật khẩu thành công!'); window.location.href='index.php';</script>";
                exit;
            } else {
                echo "<script>alert('Đổi mật khẩu thất bại!'); window.location.href='change_password.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('Mật khẩu hiện tại không đúng!'); window.location.href='change_password.php';</script>";
            exit;
        }
    } catch (PDOException $e) {
        echo "<script>alert('Có lỗi xảy ra: " . htmlspecialchars($e->getMessage()) . "'); window.location.href='change_password.php';</script>";
        exit;
    }
} else {
    header("Location: change_password.php");
    exit;
}
?>