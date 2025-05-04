<?php
session_start();
include 'config/database.php';

// Kiểm tra phương thức POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit();
}

// Kiểm tra ID sản phẩm
if (!isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID sản phẩm']);
    exit();
}

$product_id = $_POST['product_id'];
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Lấy thông tin sản phẩm
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
    exit();
}

// Kiểm tra số lượng tồn kho
if ($product['stock'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Số lượng không đủ']);
    exit();
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Kiểm tra sản phẩm đã có trong giỏ hàng chưa
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $product_id) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}

// Nếu chưa có thì thêm mới
if (!$found) {
    $_SESSION['cart'][] = [
        'id' => $product_id,
        'name' => $product['name'],
        'price' => $product['price'],
        'image' => $product['image'],
        'quantity' => $quantity
    ];
}

// Trả về kết quả
echo json_encode([
    'success' => true,
    'message' => 'Đã thêm vào giỏ hàng',
    'cart_count' => count($_SESSION['cart'])
]);
?>
