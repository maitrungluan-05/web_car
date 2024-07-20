<?php

include '../components/connect.php';
include '../components/admin_header.php'; 

// session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_POST['add_product'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_SPECIAL_CHARS);

   $price_input = $_POST['price'];
   $price = str_replace(',', '.', $price_input); // Thay thế dấu ',' bằng '.'
   $price = floatval($price); // Chuyển đổi sang kiểu số float

   // Giới hạn số lượng chữ số sau dấu thập phân
   $price = number_format($price, 2, '.', '');

   $details = $_POST['details'];
   $details = filter_var($details, FILTER_SANITIZE_SPECIAL_CHARS);

   $category = $_POST['category'];
   $category = filter_var($category, FILTER_SANITIZE_SPECIAL_CHARS);

   $image_01 = $_FILES['image_01']['name'];
   $image_01 = filter_var($image_01, FILTER_SANITIZE_SPECIAL_CHARS);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/' . $image_01;

   $image_02 = $_FILES['image_02']['name'];
   $image_02 = filter_var($image_02, FILTER_SANITIZE_SPECIAL_CHARS);
   $image_size_02 = $_FILES['image_02']['size'];
   $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
   $image_folder_02 = '../uploaded_img/' . $image_02;

   $image_03 = $_FILES['image_03']['name'];
   $image_03 = filter_var($image_03, FILTER_SANITIZE_SPECIAL_CHARS);
   $image_size_03 = $_FILES['image_03']['size'];
   $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
   $image_folder_03 = '../uploaded_img/' . $image_03;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if ($select_products->rowCount() > 0) {
      $message[] = 'Product name already exists!';
   } else {

      $insert_products = $conn->prepare("INSERT INTO `products` (name, details, price, category, image_01, image_02, image_03) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $insert_products->bindParam(1, $name);
      $insert_products->bindParam(2, $details);
      $insert_products->bindParam(3, $price, PDO::PARAM_STR); // Đảm bảo price là kiểu dữ liệu DECIMAL
      $insert_products->bindParam(4, $category);
      $insert_products->bindParam(5, $image_01);
      $insert_products->bindParam(6, $image_02);
      $insert_products->bindParam(7, $image_03);

      $insert_products->execute();

      if ($insert_products) {
         if ($image_size_01 > 2000000 or $image_size_02 > 2000000 or $image_size_03 > 2000000) {
            $message[] = 'Image size is too large!';
         } else {
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            move_uploaded_file($image_tmp_name_02, $image_folder_02);
            move_uploaded_file($image_tmp_name_03, $image_folder_03);
            $message[] = 'New product added!';
         }
      }
   }
}

if (isset($_GET['delete'])) {

   $delete_id = $_GET['delete'];
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_img/' . $fetch_delete_image['image_01']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_02']);
   unlink('../uploaded_img/' . $fetch_delete_image['image_03']);

   $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
   $delete_product->execute([$delete_id]);

   $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
   $delete_cart->execute([$delete_id]);

   $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");
   $delete_wishlist->execute([$delete_id]);

   header('location:products.php');
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

   

   <section class="add-products">

      <h1 class="heading">Add Product</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <div class="flex">
            <div class="inputBox">
               <span>Product Name (required)</span>
               <input type="text" class="box" required maxlength="100" placeholder="Enter product name" name="name">
            </div>
            <div class="inputBox">
               <span>Product Price (required)</span>
               <input type="text" class="box" required pattern="[0-9,]+" maxlength="13" placeholder="Enter product price" name="price">
            </div>


            <div class="inputBox">
               <span>Product Category (required)</span>
               <select name="category" class="box" required>
                  <option value="">Select category</option>
                  <?php
                  $select_categories = $conn->prepare("SELECT * FROM `categories`");
                  $select_categories->execute();
                  while ($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
                     echo '<option value="' . $fetch_category['name'] . '">' . $fetch_category['name'] . '</option>';
                  }
                  ?>
               </select>
            </div>
            <div class="inputBox">
               <span>Image 1 (required)</span>
               <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
            </div>
            <div class="inputBox">
               <span>Image 2</span>
               <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
            </div>
            <div class="inputBox">
               <span>Image 3</span>
               <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box">
            </div>
            <div class="inputBox">
               <span>Product Details (required)</span>
               <textarea name="details" placeholder="Enter product details" class="box" required maxlength="500" cols="30" rows="10"></textarea>
            </div>
         </div>
         <input type="submit" value="Add Product" class="btn" name="add_product">
      </form>

   </section>

   <section class="show-products">

      <h1 class="heading">Products Added</h1>

      <div class="box-container">
         <?php
         $show_products = $conn->prepare("SELECT * FROM `products`");
         $show_products->execute();
         if ($show_products->rowCount() > 0) {
            while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
         ?>
               <div class="box">
                  <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
                  <div class="name"><?= $fetch_products['name']; ?></div>
                  <div class="price">$<span><?= number_format($fetch_products['price']); ?></span></div>
                  <div class="details"><span><?= $fetch_products['details']; ?></span></div>
                  <div class="flex-btn">
                     <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Update</a>
                     <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">No products added yet!</p>';
         }
         ?>
      </div>

   </section>

   <script src="../js/admin_script.js"></script>

</body>

</html>