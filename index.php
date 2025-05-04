<?php
session_start();
include 'config/database.php';

// Lấy danh sách sản phẩm nổi bật
try {
    $stmt = $conn->query("SELECT p.*, c.name as category_name, b.name as brand_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         LEFT JOIN brands b ON p.brand_id = b.id 
                         WHERE p.featured = 1 AND p.status = 'active'
                         ORDER BY p.created_at DESC 
                         LIMIT 8");
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách danh mục
    $stmt = $conn->query("SELECT * FROM categories WHERE parent_id IS NULL AND status = 'active'");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách thương hiệu
    $stmt = $conn->query("SELECT * FROM brands WHERE status = 'active'");
    $brands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy tin tức mới nhất
    $stmt = $conn->query("SELECT * FROM news WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error = 'Có lỗi xảy ra: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPORTISA - Cửa hàng thể thao chính hãng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="index.php">SPORTISA</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Trang chủ</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Danh mục</a>
                            <ul class="dropdown-menu">
                                <?php foreach ($categories as $category): ?>
                                    <li>
                                        <a class="dropdown-item" href="category.php?slug=<?php echo $category['slug']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="news.php">Tin tức</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Liên hệ</a>
                        </li>
                    </ul>
                    <div class="d-flex">
                        <a href="cart.php" class="btn btn-outline-light me-2">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="badge bg-danger">0</span>
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="profile.php">Tài khoản</a></li>
                                    <li><a class="dropdown-item" href="orders.php">Đơn hàng</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-light">Đăng nhập</a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero bg-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4">Thể thao chính hãng</h1>
                    <p class="lead">Khám phá bộ sưu tập giày thể thao, quần áo và phụ kiện chất lượng cao từ các thương hiệu hàng đầu thế giới.</p>
                    <a href="products.php" class="btn btn-light btn-lg">Mua sắm ngay</a>
                </div>
                <div class="col-md-6">
                    <img src="images/hero.jpg" alt="Hero Image" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Sản phẩm nổi bật</h2>
            <div class="row">
                <?php foreach ($featured_products as $product): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <?php if ($product['image']): ?>
                                <img src="uploads/products/<?php echo $product['image']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($product['brand_name']); ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price">
                                        <?php if ($product['sale_price']): ?>
                                            <span class="text-danger"><?php echo number_format($product['sale_price'], 0, ',', '.'); ?> VNĐ</span>
                                            <small class="text-muted text-decoration-line-through"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</small>
                                        <?php else: ?>
                                            <span><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="product.php?slug=<?php echo $product['slug']; ?>" class="btn btn-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Danh mục sản phẩm</h2>
            <div class="row">
                <?php foreach ($categories as $category): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <h3 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h3>
                                <p class="card-text"><?php echo htmlspecialchars($category['description']); ?></p>
                                <a href="category.php?slug=<?php echo $category['slug']; ?>" class="btn btn-primary">Xem sản phẩm</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Brands -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Thương hiệu</h2>
            <div class="row">
                <?php foreach ($brands as $brand): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <?php if ($brand['logo']): ?>
                                <img src="uploads/brands/<?php echo $brand['logo']; ?>" 
                                     class="card-img-top p-3" 
                                     alt="<?php echo htmlspecialchars($brand['name']); ?>">
                            <?php endif; ?>
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($brand['name']); ?></h5>
                                <a href="brand.php?slug=<?php echo $brand['slug']; ?>" class="btn btn-outline-primary">Xem sản phẩm</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- News -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Tin tức</h2>
            <div class="row">
                <?php foreach ($news as $item): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <?php if ($item['image']): ?>
                                <img src="uploads/news/<?php echo $item['image']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h5>
                                <p class="card-text"><?php echo substr(strip_tags($item['content']), 0, 100) . '...'; ?></p>
                                <a href="news_detail.php?slug=<?php echo $item['slug']; ?>" class="btn btn-primary">Đọc thêm</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Về chúng tôi</h5>
                    <p>SPORTISA - Cửa hàng thể thao chính hãng hàng đầu Việt Nam.</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>
                        <i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận XYZ, TP.HCM<br>
                        <i class="fas fa-phone"></i> 0123456789<br>
                        <i class="fas fa-envelope"></i> contact@sportisa.com
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Theo dõi chúng tôi</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> SPORTISA. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
