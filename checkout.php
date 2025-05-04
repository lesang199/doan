<?php
session_start();
include 'config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Lấy thông tin người dùng
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Tính tổng tiền
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
$shipping = $total >= 1000000 ? 0 : 30000;
$grand_total = $total + $shipping;

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method'];
    $shipping_address = $_POST['shipping_address'];
    $notes = $_POST['notes'];

    try {
        // Bắt đầu transaction
        $conn->beginTransaction();

        // Tạo đơn hàng
        $query = "INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address, notes, created_at) 
                  VALUES (?, ?, 'pending', ?, ?, ?, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $grand_total, $payment_method, $shipping_address, $notes]);
        $order_id = $conn->lastInsertId();

        // Thêm chi tiết đơn hàng
        foreach ($_SESSION['cart'] as $item) {
            // Kiểm tra số lượng tồn kho
            $check_stock_query = "SELECT stock FROM products WHERE id = ?";
            $check_stmt = $conn->prepare($check_stock_query);
            $check_stmt->execute([$item['id']]);
            $stock = $check_stmt->fetchColumn();

            if ($stock < $item['quantity']) {
                throw new Exception("Sản phẩm " . $item['name'] . " không đủ số lượng tồn kho");
            }

            // Thêm chi tiết đơn hàng
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                      VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);

            // Cập nhật số lượng tồn kho
            $query = "UPDATE products SET stock = stock - ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->execute([$item['quantity'], $item['id']]);
        }

        // Commit transaction
        $conn->commit();

        // Xóa giỏ hàng
        unset($_SESSION['cart']);

        // Chuyển hướng đến trang cảm ơn
        header("Location: thank_you.php?order_id=" . $order_id);
        exit();

    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Thông tin thanh toán</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Họ tên</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Địa chỉ giao hàng</label>
                                <textarea class="form-control" name="shipping_address" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phương thức thanh toán</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                                    <option value="bank">Chuyển khoản ngân hàng</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea class="form-control" name="notes" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Đặt hàng</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Đơn hàng của bạn</h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Tạm tính:</strong></td>
                                    <td><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Phí vận chuyển:</strong></td>
                                    <td>
                                        <?php if ($shipping == 0): ?>
                                            <span class="text-success">Miễn phí</span>
                                        <?php else: ?>
                                            <?php echo number_format($shipping, 0, ',', '.'); ?> VNĐ
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td><strong><?php echo number_format($grand_total, 0, ',', '.'); ?> VNĐ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
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
