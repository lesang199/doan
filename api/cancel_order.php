<?php
session_start();
include '../config/database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit();
}

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
    exit();
}

// Lấy dữ liệu từ request body
$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'] ?? null;

if (!$order_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
    exit();
}

try {
    // Bắt đầu transaction
    $conn->beginTransaction();

    // Kiểm tra đơn hàng có thuộc về người dùng và có thể hủy không
    $query = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Đơn hàng không tồn tại hoặc không thuộc về bạn');
    }

    if ($order['status'] !== 'pending') {
        throw new Exception('Chỉ có thể hủy đơn hàng đang chờ xử lý');
    }

    // Cập nhật trạng thái đơn hàng
    $query = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$order_id]);

    // Lấy danh sách sản phẩm trong đơn hàng
    $query = "SELECT product_id, quantity FROM order_items WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cập nhật lại số lượng tồn kho
    foreach ($items as $item) {
        $query = "UPDATE products SET stock = stock + ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$item['quantity'], $item['product_id']]);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Đã hủy đơn hàng thành công']);

} catch (Exception $e) {
    // Rollback transaction
    $conn->rollBack();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 