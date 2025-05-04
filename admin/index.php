<?php
session_start();
include '../config/database.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Lấy thống kê
try {
    // Tổng số đơn hàng
    $stmt = $conn->query("SELECT COUNT(*) FROM orders");
    $total_orders = $stmt->fetchColumn();

    // Tổng số sản phẩm
    $stmt = $conn->query("SELECT COUNT(*) FROM products");
    $total_products = $stmt->fetchColumn();

    // Tổng số người dùng
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $total_users = $stmt->fetchColumn();

    // Tổng doanh thu
    $stmt = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'delivered'");
    $total_revenue = $stmt->fetchColumn();

    // Đơn hàng mới nhất
    $stmt = $conn->query("SELECT o.*, u.username, u.full_name 
                         FROM orders o 
                         JOIN users u ON o.user_id = u.id 
                         ORDER BY o.created_at DESC 
                         LIMIT 5");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sản phẩm bán chạy
    $stmt = $conn->query("SELECT p.name, p.image, COUNT(oi.id) as total_sold, SUM(oi.quantity) as total_quantity
                         FROM products p
                         JOIN order_items oi ON p.id = oi.product_id
                         JOIN orders o ON oi.order_id = o.id
                         WHERE o.status = 'delivered'
                         GROUP BY p.id
                         ORDER BY total_quantity DESC
                         LIMIT 5");
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Đánh giá mới nhất
    $stmt = $conn->query("SELECT r.*, u.username, p.name as product_name
                         FROM reviews r
                         JOIN users u ON r.user_id = u.id
                         JOIN products p ON r.product_id = p.id
                         ORDER BY r.created_at DESC
                         LIMIT 5");
    $recent_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = 'Có lỗi xảy ra: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">SPORTISA</h4>
                        <p class="text-muted">Quản lý</p>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="index.php">
                                <i class="fas fa-home me-2"></i> Tổng quan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="products.php">
                                <i class="fas fa-box me-2"></i> Sản phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i> Đơn hàng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="users.php">
                                <i class="fas fa-users me-2"></i> Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tổng quan</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Xuất báo cáo</button>
                        </div>
                    </div>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Thống kê -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng đơn hàng</h5>
                                <h2 class="card-text"><?php echo number_format($total_orders); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng sản phẩm</h5>
                                <h2 class="card-text"><?php echo number_format($total_products); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng người dùng</h5>
                                <h2 class="card-text"><?php echo number_format($total_users); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tổng doanh thu</h5>
                                <h2 class="card-text"><?php echo number_format($total_revenue, 0, ',', '.'); ?> VNĐ</h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Đơn hàng mới nhất -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Đơn hàng mới nhất</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn</th>
                                                <th>Khách hàng</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th>Ngày đặt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_orders as $order): ?>
                                                <tr>
                                                    <td>#<?php echo $order['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
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
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?php echo $status_class; ?>">
                                                            <?php echo $status_text; ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sản phẩm bán chạy -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Sản phẩm bán chạy</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Sản phẩm</th>
                                                <th>Số lượng bán</th>
                                                <th>Doanh thu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($top_products as $product): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php if ($product['image']): ?>
                                                                <img src="../uploads/products/<?php echo $product['image']; ?>" 
                                                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                                     class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                            <?php endif; ?>
                                                            <?php echo htmlspecialchars($product['name']); ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo number_format($product['total_quantity']); ?></td>
                                                    <td><?php echo number_format($product['total_sold'] * $product['total_quantity'], 0, ',', '.'); ?> VNĐ</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Đánh giá mới nhất -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Đánh giá mới nhất</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Người dùng</th>
                                        <th>Sản phẩm</th>
                                        <th>Đánh giá</th>
                                        <th>Nội dung</th>
                                        <th>Ngày đăng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_reviews as $review): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($review['username']); ?></td>
                                            <td><?php echo htmlspecialchars($review['product_name']); ?></td>
                                            <td>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                                <?php endfor; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($review['comment']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($review['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
