<?php
require_once 'config/database.php';
session_start();
if(isset($_POST['email'])&&isset($_POST['fullname'])&&isset($_POST['phone'])&&isset($_POST['address'])) {
    $email = $_POST['email'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    try {
        $stmt = $conn->prepare("UPDATE users SET full_name = :fullname, phone = :phone, address = :address WHERE email = :email");
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':email', $email);
        if($stmt->execute()) {
            echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='profile.php';</script>";
        } else {
            echo "<script>alert('Cập nhật thông tin thất bại!');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Có lỗi xảy ra: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}
else {
    echo "<script>alert('Vui lòng điền đầy đủ thông tin!');</script>";
}

?>