<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
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
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        Danh mục
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="products.php?category=football">Bóng đá</a></li>
                        <li><a class="dropdown-item" href="products.php?category=basketball">Bóng rổ</a></li>
                        <li><a class="dropdown-item" href="products.php?category=fitness">Thể hình</a></li>
                        <li><a class="dropdown-item" href="products.php?category=tennis">Tennis</a></li>
                        <li><a class="dropdown-item" href="products.php?category=running">Chạy bộ</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">Giới thiệu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Liên hệ</a>
                </li>
            </ul>
            <div class="d-flex">
                <a href="cart.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge bg-danger"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Tài khoản</a></li>
                            <li><a class="dropdown-item" href="orders.php">Đơn hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light me-2">Đăng nhập</a>
                    <a href="register.php" class="btn btn-light">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
