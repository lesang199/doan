<?php
session_start();
include 'config/database.php';

// Kiểm tra ID sản phẩm
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];

// Lấy thông tin sản phẩm
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit();
}

// Lấy sản phẩm liên quan
$query = "SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4";
$stmt = $conn->prepare($query);
$stmt->execute([$product['category'], $product_id]);
$related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <!-- Hình ảnh sản phẩm -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                </div>
                <div class="row">
                    <div class="col-3">
                        <img src="<?php echo $product['image']; ?>" class="img-thumbnail" alt="Thumbnail">
                    </div>
                    <div class="col-3">
                        <img src="<?php echo $product['image']; ?>" class="img-thumbnail" alt="Thumbnail">
                    </div>
                    <div class="col-3">
                        <img src="<?php echo $product['image']; ?>" class="img-thumbnail" alt="Thumbnail">
                    </div>
                    <div class="col-3">
                        <img src="<?php echo $product['image']; ?>" class="img-thumbnail" alt="Thumbnail">
                    </div>
                </div>
            </div>

            <!-- Thông tin sản phẩm -->
            <div class="col-md-6">
                <h1 class="mb-3"><?php echo $product['name']; ?></h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-muted">(4.5/5 - 100 đánh giá)</span>
                </div>
                <h2 class="text-primary mb-4">
                    <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ
                </h2>
                <p class="mb-4"><?php echo $product['description']; ?></p>

                <div class="mb-4">
                    <h5>Thông số kỹ thuật:</h5>
                    <ul class="list-unstyled">
                        <li><strong>Danh mục:</strong> <?php echo ucfirst($product['category']); ?></li>
                        <li><strong>Thương hiệu:</strong> <?php echo $product['brand']; ?></li>
                        <li><strong>Màu sắc:</strong> <?php echo $product['color']; ?></li>
                        <li><strong>Kích thước:</strong> <?php echo $product['size']; ?></li>
                        <li><strong>Tình trạng:</strong> 
                            <?php if ($product['stock'] > 0): ?>
                                <span class="text-success">Còn hàng</span>
                            <?php else: ?>
                                <span class="text-danger">Hết hàng</span>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center mb-4">
                    <div class="input-group me-3" style="width: 150px;">
                        <button class="btn btn-outline-secondary" type="button" id="decrease">-</button>
                        <input type="number" class="form-control text-center" value="1" min="1" max="<?php echo $product['stock']; ?>" id="quantity">
                        <button class="btn btn-outline-secondary" type="button" id="increase">+</button>
                    </div>
                    <button class="btn btn-primary me-2 add-to-cart" data-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-shopping-cart"></i> Thêm vào giỏ hàng
                    </button>
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-heart"></i> Yêu thích
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Chính sách bán hàng</h5>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i> Miễn phí vận chuyển cho đơn hàng trên 1.000.000 VNĐ</li>
                            <li><i class="fas fa-check text-success me-2"></i> Đổi trả trong 7 ngày</li>
                            <li><i class="fas fa-check text-success me-2"></i> Bảo hành 12 tháng</li>
                            <li><i class="fas fa-check text-success me-2"></i> Hỗ trợ 24/7</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sản phẩm liên quan -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Sản phẩm liên quan</h3>
                <div class="row">
                    <?php foreach ($related_products as $related): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card product-card">
                            <img src="<?php echo $related['image']; ?>" class="card-img-top" alt="<?php echo $related['name']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $related['name']; ?></h5>
                                <p class="card-text text-primary fw-bold">
                                    <?php echo number_format($related['price'], 0, ',', '.'); ?> VNĐ
                                </p>
                                <div class="d-flex justify-content-between">
                                    <a href="product_detail.php?id=<?php echo $related['id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                                    <button class="btn btn-outline-primary add-to-cart" data-id="<?php echo $related['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Xử lý tăng/giảm số lượng
        document.getElementById('increase').addEventListener('click', function() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.getAttribute('max'));
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        });

        document.getElementById('decrease').addEventListener('click', function() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        });

        // Xử lý thêm vào giỏ hàng
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                const quantity = document.getElementById('quantity').value;
                
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId + '&quantity=' + quantity
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã thêm vào giỏ hàng');
                        // Cập nhật số lượng trong giỏ hàng
                        const cartCount = document.querySelector('.badge');
                        cartCount.textContent = data.cart_count;
                    } else {
                        alert('Có lỗi xảy ra');
                    }
                });
            });
        });
    </script>
</body>
</html> 