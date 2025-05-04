<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Lấy danh sách đơn hàng của người dùng
$query = "SELECT o.*, 
          (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count,
          (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) as total_quantity
          FROM orders o 
          WHERE o.user_id = ? 
          ORDER BY o.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h2 class="mb-4">Đơn hàng của tôi</h2>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                Bạn chưa có đơn hàng nào. <a href="index.php">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Số sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <?php echo $order['item_count']; ?> sản phẩm
                                    (<?php echo $order['total_quantity']; ?> cái)
                                </td>
                                <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch ($order['status']) {
                                        case 'pending':
                                            $status_class = 'warning';
                                            $status_text = 'Chờ xử lý';
                                            break;
                                        case 'processing':
                                            $status_class = 'info';
                                            $status_text = 'Đang xử lý';
                                            break;
                                        case 'shipped':
                                            $status_class = 'primary';
                                            $status_text = 'Đang giao hàng';
                                            break;
                                        case 'delivered':
                                            $status_class = 'success';
                                            $status_text = 'Đã giao hàng';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'danger';
                                            $status_text = 'Đã hủy';
                                            break;
                                        default:
                                            $status_class = 'secondary';
                                            $status_text = 'Không xác định';
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </a>
                                    <?php if ($order['status'] == 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="cancelOrder(<?php echo $order['id']; ?>)">
                                            <i class="fas fa-times"></i> Hủy đơn
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
    function cancelOrder(orderId) {
        if (confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
            fetch('api/cancel_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đã hủy đơn hàng thành công');
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xử lý yêu cầu');
            });
        }
    }
    </script>
</body>
</html> 