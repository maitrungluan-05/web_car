<?php
include '../components/connect.php';

$id = $_GET['id'] ?? null;

if ($id) {
    $sql = "SELECT * FROM categories WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        echo json_encode($category);
    } else {
        echo json_encode(['error' => 'Danh mục không tìm thấy']);
    }
} else {
    echo json_encode(['error' => 'ID không hợp lệ']);
}
?>
