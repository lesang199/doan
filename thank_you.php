<?php
session_start();
include 'config/database.php';

// Kiểm tra ID đơn hàng
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];

// Lấy thông tin đơn hàng
$query = "SELECT o.*, u.username, u.email 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: index.php");
    exit();
}

// Lấy chi tiết đơn hàng
$query = "SELECT oi.*, p.name, p.image 
          FROM order_items oi 
          JOIN products p ON oi.product_id = p.id 
          WHERE oi.order_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảm ơn - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="mb-4">Cảm ơn bạn đã đặt hàng!</h2>
                        <p class="mb-4">Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
                        <div class="mb-4">
                            <p>Mã đơn hàng: <strong>#<?php echo $order_id; ?></strong></p>
                            <p>Ngày đặt hàng: <strong><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></strong></p>
                            <p>Tổng tiền: <strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</strong></p>
                        </div>
                        <div class="mb-4">
                            <h5>Thông tin đơn hàng</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-thumbnail me-2" style="width: 50px;">
                                                <?php echo $item['name']; ?>
                                            </div>
                                        </td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-4">
                            <h5>Thông tin giao hàng</h5>
                            <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                            <p><strong>Phương thức thanh toán:</strong> 
                                <?php 
                                echo $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng';
                                ?>
                            </p>
                        </div>
                        <div class="d-flex justify-content-center">
                            <a href="index.php" class="btn btn-primary me-2">
                                <i class="fas fa-home"></i> Về trang chủ
                            </a>
                            <a href="orders.php" class="btn btn-outline-primary">
                                <i class="fas fa-list"></i> Xem đơn hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
