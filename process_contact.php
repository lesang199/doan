<?php
require_once 'config/database.php';
session_start();

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message']) && isset($_POST['phone'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    $phone = trim($_POST['phone']);

    // Kiểm tra các trường không được để trống
    if (empty($name) || empty($email) || empty($message) || empty($phone)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.location.href='contact.php';</script>";
        exit;
    }

    // Kiểm tra định dạng email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Email không hợp lệ!'); </script>";
        exit;
    }
    // Kiểm tra định dạng số điện thoại (chỉ cho phép số và có độ dài 10 kí tự)
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo "<script>alert('Số điện thoại không hợp lệ!'); </script>";
        exit;
    }

    // Kiểm tra độ dài nội dung tin nhắn
    if (strlen($message) < 10) {
        echo "<script>alert('Nội dung tin nhắn phải có ít nhất 10 ký tự!'); </script>";
        exit;
    }

    try {
        // Chuẩn bị câu lệnh SQL
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (:name, :email, :phone, :message)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            echo "<script>alert('Gửi thông tin thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Gửi thông tin thất bại!'); window.location.href='contact.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Có lỗi xảy ra: " . htmlspecialchars($e->getMessage()) . "'); window.location.href='contact.php';</script>";
    }
} else {
    echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.location.href='contact.php';</script>";
}
?>