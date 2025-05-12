<?php
session_start();
include 'config/database.php';

// Lấy danh mục từ URL
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Xây dựng câu truy vấn
$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}

if ($search) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Sắp xếp
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'name':
        $query .= " ORDER BY name ASC";
        break;
    default:
        $query .= " ORDER BY created_at DESC";
}

// Thực hiện truy vấn
$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm - SPORTISA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .input-group {
            width: 300px;
        }
        .input-group input {
            border-color: none;
            user-select: none;
           
        }
        .input-group input:focus {
            box-shadow: none;
            border-color:none ;
        }
        .input-group button {
            background-color: #007bff;
            border: none;
        }
        .input-group button:hover {
            transform: translate(0);
            background-color: #0056b3;
        }
    </style>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Danh mục</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="products.php" class="<?php echo !$category ? 'text-primary fw-bold' : ''; ?>">
                                    Tất cả sản phẩm
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="products.php?category=football" class="<?php echo $category == 'football' ? 'text-primary fw-bold' : ''; ?>">
                                    Bóng đá
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="products.php?category=basketball" class="<?php echo $category == 'basketball' ? 'text-primary fw-bold' : ''; ?>">
                                    Bóng rổ
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="products.php?category=fitness" class="<?php echo $category == 'fitness' ? 'text-primary fw-bold' : ''; ?>">
                                    Thể hình
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="products.php?category=tennis" class="<?php echo $category == 'tennis' ? 'text-primary fw-bold' : ''; ?>">
                                    Tennis
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="products.php?category=running" class="<?php echo $category == 'running' ? 'text-primary fw-bold' : ''; ?>">
                                    Chạy bộ
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Lọc theo giá</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="price" id="price1" value="0-500000">
                            <label class="form-check-label" for="price1">
                                Dưới 500.000 VNĐ
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="price" id="price2" value="500000-1000000">
                            <label class="form-check-label" for="price2">
                                500.000 - 1.000.000 VNĐ
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="price" id="price3" value="1000000-2000000">
                            <label class="form-check-label" for="price3">
                                1.000.000 - 2.000.000 VNĐ
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="price" id="price4" value="2000000-">
                            <label class="form-check-label" for="price4">
                                Trên 2.000.000 VNĐ
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products -->
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo $category ? ucfirst($category) : 'Tất cả sản phẩm'; ?></h2>
                    <div class="d-flex">
                        <form class="me-3" method="GET">
                            <input type="hidden" name="category" value="<?php echo $category; ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." value="<?php echo $search; ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        <select class="form-select" onchange="window.location.href=this.value">
                            <option value="products.php?sort=newest<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="products.php?sort=price_asc<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                            <option value="products.php?sort=price_desc<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                            <option value="products.php?sort=name<?php echo $category ? '&category='.$category : ''; ?>" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Tên A-Z</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <?php foreach ($products as $product): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card product-card">
                            <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                <p class="card-text text-primary fw-bold">
                                    <?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ
                                </p>
                                <div class="d-flex justify-content-between">
                                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Xem chi tiết</a>
                                    <button class="btn btn-outline-primary add-to-cart" data-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <h3>Không tìm thấy sản phẩm</h3>
                    <p>Vui lòng thử lại với từ khóa khác</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Xử lý thêm vào giỏ hàng
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'product_id=' + productId
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