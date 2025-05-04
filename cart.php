<?php
session_start();
include 'config/database.php';

// Xử lý cập nhật giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $id => $quantity) {
            if ($quantity <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        $id = $_POST['remove_item'];
        unset($_SESSION['cart'][$id]);
    }
}

// Tính tổng tiền
$total = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4">Giỏ hàng</h1>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="text-center py-5">
                <h3>Giỏ hàng trống</h3>
                <p>Hãy thêm sản phẩm vào giỏ hàng của bạn</p>
                <a href="products.php" class="btn btn-primary">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Giá</th>
                                            <th>Số lượng</th>
                                            <th>Tổng</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-thumbnail me-3" style="width: 80px;">
                                                    <div>
                                                        <h5 class="mb-0"><?php echo $item['name']; ?></h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                            <td>
                                                <input type="number" name="quantity[<?php echo $id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" class="form-control" style="width: 80px;">
                                            </td>
                                            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VNĐ</td>
                                            <td>
                                                <button type="submit" name="remove_item" value="<?php echo $id; ?>" class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between">
                                    <a href="products.php" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                                    </a>
                                    <button type="submit" name="update_cart" class="btn btn-primary">
                                        <i class="fas fa-sync"></i> Cập nhật giỏ hàng
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng đơn hàng</h5>
                            <table class="table">
                                <tr>
                                    <td>Tạm tính:</td>
                                    <td class="text-end"><?php echo number_format($total, 0, ',', '.'); ?> VNĐ</td>
                                </tr>
                                <tr>
                                    <td>Phí vận chuyển:</td>
                                    <td class="text-end">
                                        <?php if ($total >= 1000000): ?>
                                            <span class="text-success">Miễn phí</span>
                                        <?php else: ?>
                                            30.000 VNĐ
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tổng cộng:</strong></td>
                                    <td class="text-end">
                                        <strong>
                                            <?php 
                                            $shipping = $total >= 1000000 ? 0 : 30000;
                                            echo number_format($total + $shipping, 0, ',', '.'); 
                                            ?> VNĐ
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                            <a href="checkout.php" class="btn btn-primary w-100">
                                Thanh toán
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
