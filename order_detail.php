<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra ID đơn hàng
if (!isset($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];

// Lấy thông tin đơn hàng
$query = "SELECT o.*, u.username, u.email, u.phone 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Lấy chi tiết đơn hàng
$query = "SELECT oi.*, p.name, p.image, p.slug 
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
    <title>Chi tiết đơn hàng #<?php echo $order_id; ?> - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Chi tiết đơn hàng #<?php echo $order_id; ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đơn giá</th>
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
                                                    <div>
                                                        <a href="product.php?slug=<?php echo $item['slug']; ?>" class="text-decoration-none">
                                                            <?php echo $item['name']; ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tạm tính:</strong></td>
                                        <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Phí vận chuyển:</strong></td>
                                        <td>
                                            <?php if ($order['total_amount'] >= 1000000): ?>
                                                <span class="text-success">Miễn phí</span>
                                            <?php else: ?>
                                                30,000 VNĐ
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                                        <td><strong><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Thông tin đơn hàng</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Trạng thái:</strong> 
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
                        </p>
                        <p><strong>Ngày đặt hàng:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                        <p><strong>Phương thức thanh toán:</strong> 
                            <?php 
                            echo $order['payment_method'] == 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng';
                            ?>
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Thông tin giao hàng</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                        <?php if (!empty($order['notes'])): ?>
                            <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($order['status'] == 'pending'): ?>
                    <div class="mt-3">
                        <button type="button" class="btn btn-danger w-100" onclick="cancelOrder(<?php echo $order_id; ?>)">
                            <i class="fas fa-times"></i> Hủy đơn hàng
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
