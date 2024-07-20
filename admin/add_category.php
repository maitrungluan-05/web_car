<?php
include '../components/connect.php';
// include '../components/admin_header.php';

// Xử lý thêm danh mục mới
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $category_image = $_FILES['category_image']['name'];
    $image_tmp_name = $_FILES['category_image']['tmp_name'];
    $image_size = $_FILES['category_image']['size'];
    $image_error = $_FILES['category_image']['error'];
    $image_folder = '../uploaded_img/' . $category_image;

    if ($image_error === UPLOAD_ERR_OK) {
        // Kiểm tra kích thước ảnh (max 2MB)
        if ($image_size > 2000000) {
            $message[] = "Ảnh quá lớn!";
        } else {
            // Di chuyển ảnh từ tạm tới thư mục đích
            if (move_uploaded_file($image_tmp_name, $image_folder)) {
                $sql = "INSERT INTO categories (name, image) VALUES (:name, :image)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':name', $category_name);
                $stmt->bindParam(':image', $category_image);

                if ($stmt->execute()) {
                    $message[] = "New category has been added successfully!";
                } else {
                    $message[] = "Error: " . $stmt->errorInfo()[2];
                }
            } else {
                $message[] = "Error uploading photo: " . print_r(error_get_last(), true);
            }
        }
    } else {
        $message[] = "Image upload error: Error code" . $image_error;
    }
}

// Xử lý xóa danh mục
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM categories WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        $message[] = "The category has been successfully deleted!";
    } else {
        $message[] = "Error: " . $stmt->errorInfo()[2];
    }
}

// Xử lý cập nhật danh mục
if (isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $category_image = $_FILES['category_image']['name'];
    $image_tmp_name = $_FILES['category_image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $category_image;

    if ($category_image) {
        // Nếu có ảnh mới được tải lên
        if (move_uploaded_file($image_tmp_name, $image_folder)) {
            $sql = "UPDATE categories SET name = :name, image = :image WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':image', $category_image);
        } else {
            $message[] = "Error uploading photo: " . print_r(error_get_last(), true);
            return;
        }
    } else {
        // Nếu không có ảnh mới được tải lên
        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        $stmt = $conn->prepare($sql);
    }
    
    $stmt->bindParam(':name', $category_name);
    $stmt->bindParam(':id', $category_id);

    if ($stmt->execute()) {
        $message[] = "The directory has been updated successfully!";
    } else {
        $message[] = "Lỗi: " . $stmt->errorInfo()[2];
    }
}

// Lấy danh sách danh mục từ cơ sở dữ liệu
$sql = "SELECT * FROM categories";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category management</title>
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f8f8;
        margin: 0;
        padding: 0;
    }

    /* header {
        background-color: #fff;
        padding: 10px 20px;
        border-bottom: 1px solid #ddd;
    }

    header h1 {
        margin: 0;
        font-size: 24px;
        color: #333;
    } */

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

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    form input[type="text"], form input[type="file"] {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    form button {
        padding: 10px 20px;
        background-color: #ff7070;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    form button:hover {
        background-color: #ff5050;
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

    .edit-icon, .delete-icon {
        font-size: 18px;
        color: #ff7070;
    }

    .edit-icon:hover, .delete-icon:hover {
        color: #ff5050;
    }

</style>
<body>
    <header>
    <?php include '../components/admin_header.php'; ?>
    </header>
    <section class="add-category">
        <h1 class="heading">Add Category</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="text" name="category_name" placeholder="Name Category" required> <br>
            <input type="file" name="category_image" accept="image/*" required> <br>
            <button type="submit" name="add_category">Submit</button>
        </form>
    </section>
    <section class="edit-category" id="edit-category" style="display: none;">
        <h1 class="heading">Edit Category</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="category_id" id="edit_category_id">
            <input type="text" name="category_name" id="edit_category_name" placeholder="Name Category" required> <br>
            <input type="file" name="category_image" accept="image/*"> <br>
            <button type="submit" name="update_category">Update</button>
            <button type="button" onclick="cancelEdit()">Cancel</button>
        </form>
    </section>
    <section class="show-categories">
        <h1 class="heading">List Category</h1>
        <table>
            <thead>
                <tr>
                    <th>ID Number</th>
                    <th>Name Category</th>
                    <th>Image</th>
                    <th>Operation</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($stmt->rowCount() > 0) {
                    foreach ($result as $row) {
                        echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . htmlspecialchars($row["name"]) . "</td>
                            <td><img src='../uploaded_img/" . htmlspecialchars($row["image"]) . "' alt='" . htmlspecialchars($row["name"]) . "' width='50'></td>
                            <td class='action-buttons'>
                                <button onclick='editCategory(" . $row["id"] . ")'><span class='edit-icon'>✏️</span></button>
                                <button onclick='deleteCategory(" . $row["id"] . ")'><span class='delete-icon'>🗑️</span></button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No data</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>

    <script>
    function editCategory(id) {
        fetch('get_category.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    alert(data.error);
                } else {
                    document.querySelector('.add-category').style.display = 'none';
                    document.querySelector('.edit-category').style.display = 'block';
                    document.getElementById('edit_category_id').value = data.id;
                    document.getElementById('edit_category_name').value = data.name;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error! An error occurred. Please try again later.');
            });
    }

    function deleteCategory(id) {
        if (confirm('Are you sure you want to delete the category' + id + '?')) {
            window.location.href = 'add_category.php?delete=' + id;
        }
    }

    function cancelEdit() {
        document.querySelector('.edit-category').style.display = 'none';
        document.querySelector('.add-category').style.display = 'block';
    }
    </script>
</body>
</html>
