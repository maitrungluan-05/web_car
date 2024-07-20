<?php
include '../components/connect.php';

// Xử lý xóa đánh giá
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM reviews WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $message[] = "The review has been successfully deleted!";
    } else {
        $message[] = "Error: " . $stmt->errorInfo()[2];
    }
}

// Lấy danh sách đánh giá từ cơ sở dữ liệu
$sql = "SELECT * FROM reviews";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review management</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f8f8;
        margin: 0;
        padding: 0;
    }

    nav {
        margin-top: 20px;
    }

    nav a {
        text-decoration: none;
        color: #333;
        padding: 10px 20px;
        display: inline-block;
    }

    nav a:hover {
        background-color: #f0f0f0;
    }

    section {
        padding: 20px;
    }

    h1.heading {
        font-size: 22px;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    table th, table td {
        padding: 10px;
        text-align: left;
    }

    table th {
        background-color: #f0f0f0;
    }

    .action-buttons button {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        margin: 0 5px;
    }

    .delete-icon {
        font-size: 18px;
        color: #ff7070;
    }

    .delete-icon:hover {
        color: #ff5050;
    }
</style>
<body>
    <header>
        <?php include '../components/admin_header.php'; ?>
    </header>
    <section class="show-reviews">
        <h1 class="heading">List Reviews</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Image</th>
                    <th>Review Text</th>
                    <th>Rating</th>
                    <th>Date</th>
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($stmt->rowCount() > 0) {
                    foreach ($result as $row) {
                        echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . htmlspecialchars($row["customer_name"]) . "</td>
                            <td><img src='../images/" . htmlspecialchars($row["customer_image"]) . "' alt='" . htmlspecialchars($row["customer_name"]) . "' width='50'></td>
                            <td>" . htmlspecialchars($row["review_text"]) . "</td>
                            <td>" . $row["rating"] . "</td>
                            <td>" . $row["review_date"] . "</td>
                            <td class='action-buttons'>
                                <button onclick='deleteReview(" . $row["id"] . ")'><span class='delete-icon'>🗑️</span></button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No data</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <script>
    function deleteReview(id) {
        if (confirm('Are you sure you want to delete the review ' + id + '?')) {
            window.location.href = 'about.php?delete=' + id;
        }
    }
    </script>
</body>
</html>
