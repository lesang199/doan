-- Tạo cơ sở dữ liệu
CREATE DATABASE IF NOT EXISTS sportisa;
USE sportisa;

-- Bảng người dùng
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng danh mục sản phẩm
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Bảng thương hiệu
CREATE TABLE brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255),
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng sản phẩm
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    stock INT NOT NULL DEFAULT 0,
    image VARCHAR(255),
    category_id INT,
    brand_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL
);

-- Bảng hình ảnh sản phẩm
CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Bảng đơn hàng
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_name VARCHAR(100) NOT NULL,
    payment_method ENUM('cod', 'bank_transfer', 'credit_card') NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Bảng chi tiết đơn hàng
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Bảng giỏ hàng
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
);

-- Bảng đánh giá sản phẩm
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Bảng tin tức
CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    image VARCHAR(255),
    author_id INT NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Bảng liên hệ
CREATE TABLE contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Bảng cấu hình
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    `key` VARCHAR(50) NOT NULL UNIQUE,
    value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Chèn dữ liệu mẫu cho admin
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@sportisa.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Chèn dữ liệu mẫu cho danh mục
INSERT INTO categories (name, slug, description) VALUES
('Giày thể thao', 'giay-the-thao', 'Các loại giày thể thao chính hãng'),
('Quần áo thể thao', 'quan-ao-the-thao', 'Quần áo thể thao chất lượng cao'),
('Phụ kiện thể thao', 'phu-kien-the-thao', 'Các phụ kiện thể thao đa dạng');

-- Chèn dữ liệu mẫu cho thương hiệu
INSERT INTO brands (name, slug, description) VALUES
('Nike', 'nike', 'Thương hiệu thể thao hàng đầu thế giới'),
('Adidas', 'adidas', 'Thương hiệu thể thao nổi tiếng'),
('Puma', 'puma', 'Thương hiệu thể thao đẳng cấp');

-- Chèn dữ liệu mẫu cho cấu hình
INSERT INTO settings (`key`, value) VALUES
('site_name', 'SPORTISA'),
('site_description', 'Cửa hàng thể thao chính hãng'),
('contact_email', 'contact@sportisa.com'),
('contact_phone', '0123456789'),
('address', '123 Đường ABC, Quận XYZ, TP.HCM'),
('shipping_fee', '30000');

-- Thêm dữ liệu mẫu cho danh mục con
INSERT INTO categories (name, slug, description, parent_id) VALUES
('Giày bóng đá', 'giay-bong-da', 'Giày bóng đá chính hãng', 1),
('Giày chạy bộ', 'giay-chay-bo', 'Giày chạy bộ chất lượng cao', 1),
('Giày bóng rổ', 'giay-bong-ro', 'Giày bóng rổ chuyên nghiệp', 1),
('Áo thể thao', 'ao-the-thao', 'Áo thể thao thời trang', 2),
('Quần thể thao', 'quan-the-thao', 'Quần thể thao thoải mái', 2),
('Balo thể thao', 'balo-the-thao', 'Balo thể thao đa năng', 3),
('Túi đựng giày', 'tui-dung-giay', 'Túi đựng giày tiện lợi', 3);

-- Thêm dữ liệu mẫu cho thương hiệu
INSERT INTO brands (name, slug, description) VALUES
('Under Armour', 'under-armour', 'Thương hiệu thể thao chuyên nghiệp'),
('New Balance', 'new-balance', 'Thương hiệu giày thể thao nổi tiếng'),
('Asics', 'asics', 'Thương hiệu giày chạy bộ hàng đầu'),
('Reebok', 'reebok', 'Thương hiệu thể thao đẳng cấp');

-- Thêm dữ liệu mẫu cho sản phẩm
INSERT INTO products (name, slug, description, price, sale_price, stock, category_id, brand_id, featured) VALUES
('Nike Mercurial Vapor 14', 'nike-mercurial-vapor-14', 'Giày bóng đá cao cấp với công nghệ Flyknit', 3500000, 3200000, 50, 4, 1, 1),
('Adidas Ultraboost 21', 'adidas-ultraboost-21', 'Giày chạy bộ với công nghệ Boost', 4200000, 4000000, 30, 5, 2, 1),
('Nike Air Jordan 1', 'nike-air-jordan-1', 'Giày bóng rổ cổ điển', 4500000, NULL, 20, 6, 1, 1),
('Adidas Tiro 21', 'adidas-tiro-21', 'Quần tập luyện thoải mái', 1200000, 1000000, 100, 8, 2, 0),
('Nike Dri-FIT', 'nike-dri-fit', 'Áo thể thao thấm hút mồ hôi', 800000, NULL, 80, 7, 1, 0),
('Puma Backpack', 'puma-backpack', 'Balo thể thao đa năng', 900000, 800000, 40, 9, 3, 0),
('Under Armour Curry 8', 'under-armour-curry-8', 'Giày bóng rổ chuyên nghiệp', 3800000, 3500000, 25, 6, 4, 1),
('New Balance 990v5', 'new-balance-990v5', 'Giày chạy bộ cao cấp', 4000000, NULL, 35, 5, 5, 0),
('Asics Gel-Kayano 28', 'asics-gel-kayano-28', 'Giày chạy bộ hỗ trợ', 3600000, 3400000, 45, 5, 6, 0),
('Reebok Nano X1', 'reebok-nano-x1', 'Giày tập luyện đa năng', 2800000, 2500000, 30, 4, 7, 0);

-- Thêm dữ liệu mẫu cho hình ảnh sản phẩm
INSERT INTO product_images (product_id, image, is_primary) VALUES
(1, 'mercurial-vapor-14-1.jpg', 1),
(1, 'mercurial-vapor-14-2.jpg', 0),
(1, 'mercurial-vapor-14-3.jpg', 0),
(2, 'ultraboost-21-1.jpg', 1),
(2, 'ultraboost-21-2.jpg', 0),
(3, 'air-jordan-1-1.jpg', 1),
(3, 'air-jordan-1-2.jpg', 0),
(4, 'tiro-21-1.jpg', 1),
(5, 'dri-fit-1.jpg', 1),
(6, 'puma-backpack-1.jpg', 1),
(7, 'curry-8-1.jpg', 1),
(8, '990v5-1.jpg', 1),
(9, 'gel-kayano-28-1.jpg', 1),
(10, 'nano-x1-1.jpg', 1);

-- Thêm dữ liệu mẫu cho người dùng
INSERT INTO users (username, email, password, full_name, phone, address, role) VALUES
('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', '0123456789', '123 Đường ABC, Quận 1, TP.HCM', 'user'),
('user2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', '0987654321', '456 Đường XYZ, Quận 2, TP.HCM', 'user'),
('user3', 'user3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn C', '0369852147', '789 Đường DEF, Quận 3, TP.HCM', 'user');

-- Thêm dữ liệu mẫu cho đơn hàng
INSERT INTO orders (user_id, total_amount, shipping_address, shipping_phone, shipping_name, payment_method, status) VALUES
(2, 8000000, '456 Đường XYZ, Quận 2, TP.HCM', '0987654321', 'Trần Thị B', 'bank_transfer', 'delivered'),
(3, 4500000, '789 Đường DEF, Quận 3, TP.HCM', '0369852147', 'Lê Văn C', 'cod', 'processing'),
(2, 3600000, '456 Đường XYZ, Quận 2, TP.HCM', '0987654321', 'Trần Thị B', 'credit_card', 'shipped');

-- Thêm dữ liệu mẫu cho chi tiết đơn hàng
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 2, 3500000),
(1, 4, 1, 1000000),
(2, 3, 1, 4500000),
(3, 9, 1, 3600000);

-- Thêm dữ liệu mẫu cho giỏ hàng
INSERT INTO cart (user_id, product_id, quantity) VALUES
(1, 2, 1),
(1, 5, 2),
(2, 7, 1),
(3, 10, 1);

-- Thêm dữ liệu mẫu cho đánh giá
INSERT INTO reviews (user_id, product_id, rating, comment, status) VALUES
(2, 1, 5, 'Giày rất đẹp và thoải mái, chất lượng tốt', 'approved'),
(3, 2, 4, 'Giày chạy bộ rất êm, nhưng giá hơi cao', 'approved'),
(2, 3, 5, 'Thiết kế cổ điển, chất liệu tốt', 'approved'),
(1, 4, 3, 'Quần hơi dài so với size', 'approved'),
(3, 5, 4, 'Áo thấm hút mồ hôi tốt', 'approved');

-- Thêm dữ liệu mẫu cho tin tức
INSERT INTO news (title, slug, content, image, author_id, status) VALUES
('Giới thiệu dòng giày mới Nike Air Max', 'gioi-thieu-dong-giay-moi-nike-air-max', 'Nike vừa ra mắt dòng giày Air Max mới với nhiều cải tiến...', 'nike-air-max.jpg', 1, 'published'),
('Cách chọn giày chạy bộ phù hợp', 'cach-chon-giay-chay-bo-phu-hop', 'Hướng dẫn chi tiết cách chọn giày chạy bộ phù hợp với nhu cầu...', 'chon-giay-chay-bo.jpg', 1, 'published'),
('Xu hướng thời trang thể thao 2023', 'xu-huong-thoi-trang-the-thao-2023', 'Cập nhật những xu hướng thời trang thể thao mới nhất năm 2023...', 'thoi-trang-the-thao-2023.jpg', 1, 'published');

-- Thêm dữ liệu mẫu cho liên hệ
INSERT INTO contacts (name, email, phone, subject, message, status) VALUES
('Nguyễn Văn D', 'nguyenvand@example.com', '0123456789', 'Hỏi về chính sách đổi trả', 'Tôi muốn biết thêm về chính sách đổi trả sản phẩm', 'replied'),
('Trần Thị E', 'tranthie@example.com', '0987654321', 'Tư vấn sản phẩm', 'Cần tư vấn về giày chạy bộ', 'read'),
('Lê Văn F', 'levanf@example.com', '0369852147', 'Khiếu nại', 'Sản phẩm nhận được không đúng với mô tả', 'new');

-- Thêm dữ liệu mẫu cho cấu hình
INSERT INTO settings (`key`, value) VALUES
('facebook_url', 'https://facebook.com/sportisa'),
('instagram_url', 'https://instagram.com/sportisa'),
('twitter_url', 'https://twitter.com/sportisa'),
('youtube_url', 'https://youtube.com/sportisa'),
('return_policy', 'Chấp nhận đổi trả trong vòng 7 ngày'),
('shipping_policy', 'Miễn phí vận chuyển cho đơn hàng trên 1.000.000đ'),
('payment_policy', 'Chấp nhận thanh toán qua thẻ tín dụng, chuyển khoản và COD'),
('about_us', 'SPORTISA - Cửa hàng thể thao chính hãng hàng đầu Việt Nam');