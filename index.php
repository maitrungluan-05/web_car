<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>


<body>

   <?php include 'components/user_header.php'; ?>

   <div class="home-bg">   

      <section class="home">

         <div class="swiper home-slider">

            <div class="swiper-wrapper">

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/LuxurySportsCars.jpg" alt="Sports Car Banner">
                  </div>
                  <div class="content">
                     <span>Discover the thrill</span>
                     <h3>Luxury Sports Cars</h3>
                     <a href="shop.php" class="btn">Explore Now</a>
                  </div>
               </div>

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/banner2.jpg" alt="Luxury Car Banner">
                  </div>
                  <div class="content">
                     <span>Experience elegance</span>
                     <h3>Luxury Sedans</h3>
                     <a href="shop.php" class="btn">Explore Now</a>
                  </div>
               </div>

               <div class="swiper-slide slide">
                  <div class="image">
                     <img src="images/banner3.jpg" alt="Supercar Banner">
                  </div>
                  <div class="content">
                     <span>Unleash the power</span>
                     <h3>Supercars Collection</h3>
                     <a href="shop.php" class="btn">Explore Now</a>
                  </div>
               </div>

            </div>

            <div class="swiper-pagination"></div>

         </div>

      </section>


   </div>

   <section class="category">

      <h1 class="heading">Shop by category</h1>

      <div class="    category-slider">
         <div class="swiper-wrapper">
            <?php
            $select_categories = $conn->prepare("SELECT * FROM `categories`");
            $select_categories->execute();
            $seen_ids = []; // Mảng để lưu các id đã xuất hiện

            while ($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
               // Kiểm tra xem id đã xuất hiện chưa
               if (!in_array($fetch_category['id'], $seen_ids)) {
                  $seen_ids[] = $fetch_category['id']; // Thêm id vào mảng đã xuất hiện
            ?>
                  <a href="category.php?category=<?= $fetch_category['name']; ?>" class="swiper-slide slide">
                      <img src="images/<?= $fetch_category['image']; ?>" alt="">
                     <h3><?= $fetch_category['name']; ?></h3>
                  </a>
            <?php
               }
            }

            if (empty($seen_ids)) {
               echo '<p class="empty">No categories found!</p>';
            }
            ?>
         </div>
         
      </div>
      <!-- <div class="swiper-pagination"></div> -->

   </section>



   <section class="home-products">

      <h1 class="heading">Latest products</h1>

      <div class="swiper products-slider">

         <div class="swiper-wrapper">

            <?php
            $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 6");
            $select_products->execute();
            if ($select_products->rowCount() > 0) {
               while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
                  <form action="" method="post" class="swiper-slide slide">
                     <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
                     <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
                     <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
                     <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
                     <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
                     <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
                     <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
                     <div class="name"><?= $fetch_product['name']; ?></div>
                     <div class="flex">
                        <div class="price"><span>$</span><?= $fetch_product['price']; ?><span>/-</span></div>
                        <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
                     </div>
                     <input type="submit" value="add to cart" class="btn" name="add_to_cart">
                  </form>
            <?php
               }
            } else {
               echo '<p class="empty">no products added yet!</p>';
            }
            ?>

         </div>

         <div class="swiper-pagination"></div>

      </div>

   </section>


   <?php include 'components/footer.php'; ?>

   <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

   <script src="js/script.js"></script>
  <script>
      var swiper = new Swiper('.home-slider', {
         direction: 'horizontal', // Hướng trượt ngang
         loop: true, // Lặp lại các slide
         autoplay: {
            delay: 5000, // Tự động chuyển slide sau 5 giây
            disableOnInteraction: false, // Không tắt autoplay khi người dùng tương tác với Swiper
         },
         pagination: {
            el: '.swiper-pagination', // Thanh pagination
            clickable: true, // Cho phép bấm vào pagination để chuyển slide
         },
         navigation: {
            nextEl: '.swiper-button-next', // Nút chuyển slide tiếp theo
            prevEl: '.swiper-button-prev', // Nút chuyển slide trước đó
         },
      });
      var swiper = new Swiper('.category-slider', {
         slidesPerView: 'auto',
         loop: true,
         autoplay: {
            delay: 10000,
            disableOnInteraction: false,
         },
         pagination: {
            el: '.swiper-pagination',
            clickable: true,
         },
      });
      var swiper = new Swiper(".home-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
      });

      var swiper = new Swiper(".category-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
         breakpoints: {
            0: {
               slidesPerView: 2,
            },
            650: {
               slidesPerView: 3,
            },
            768: {
               slidesPerView: 4,
            },
            1024: {
               slidesPerView: 5,
            },
         },
      });

      var swiper = new Swiper(".products-slider", {
         loop: true,
         spaceBetween: 20,
         pagination: {
            el: ".swiper-pagination",
            clickable: true,
         },
         breakpoints: {
            550: {
               slidesPerView: 2,
            },
            768: {
               slidesPerView: 2,
            },
            1024: {
               slidesPerView: 3,
            },
         },
      });
   </script>

</body>

</html>