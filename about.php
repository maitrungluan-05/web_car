<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
}

if (isset($_POST['submit_review'])) {

   $customer_name = $_POST['customer_name'];
   $customer_name = filter_var($customer_name, FILTER_SANITIZE_SPECIAL_CHARS);
   $review_text = $_POST['review_text'];
   $review_text = filter_var($review_text, FILTER_SANITIZE_SPECIAL_CHARS);
   $rating = $_POST['rating'];
   $rating = filter_var($rating, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

   $customer_image = $_FILES['customer_image']['name'];
   $customer_image_tmp_name = $_FILES['customer_image']['tmp_name'];
   $customer_image_folder = 'images/' . $customer_image;

   if (move_uploaded_file($customer_image_tmp_name, $customer_image_folder)) {
      $insert_review = $conn->prepare("INSERT INTO `reviews` (customer_name, customer_image, review_text, rating) VALUES (?, ?, ?, ?)");
      if ($insert_review->execute([$customer_name, $customer_image, $review_text, $rating])) {
         $message[] = 'Review submitted successfully!';
      } else {
         $message[] = 'Failed to submit review!';
      }
   } else {
      $message[] = 'Failed to upload image!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<style>
   /* General styling */
/* body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
} */

.container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.heading {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 20px;
}

section {
    padding: 40px 0;
}

form {
    max-width: 600px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.box {
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 100%;
}

.btn {
    padding: 10px;
    font-size: 1rem;
    background-color: #ff5722;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #e64a19;
}

.submit-review form .box {
    margin-bottom: 15px;
}

.submit-review form textarea {
    resize: vertical;
}

.reviews {
    background-color: #f9f9f9;
    padding: 40px 0;
}

.reviews .swiper-slide {
    background-color: white;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border-radius: 10px;
    text-align: center;
}

.reviews .swiper-slide img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
}

.reviews .swiper-slide .stars {
    margin: 10px 0;
}

.reviews .swiper-slide .stars i {
    color: #ff5722;
}

.reviews .swiper-slide h3 {
    margin: 10px 0 0;
    font-size: 1.2rem;
    color: #333;
}

.reviews .swiper-slide p {
    font-size: 1rem;
    color: #777;
}

</style>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="about">
   <div class="row">
      <div class="image">
         <img src="images/about.jpg" alt="">
      </div>
      <div class="content">
         <h3>Why choose us?</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam veritatis minus et similique doloribus? Harum molestias tenetur eaque illum quas? Obcaecati nulla in itaque modi magnam ipsa molestiae ullam consequuntur.</p>
         <a href="contact.php" class="btn">Contact us</a>
      </div>
   </div>
</section>
   <section class="submit-review">
      <h1 class="heading">Submit your review</h1>
      <form action="" method="post" enctype="multipart/form-data">
         <input type="text" name="customer_name" placeholder="Enter your name" required maxlength="100" class="box">
         <input type="file" name="customer_image" accept="image/*" required class="box">
         <textarea name="review_text" class="box" placeholder="Enter your review" required cols="30" rows="10"></textarea>
         <input type="number" name="rating" placeholder="Enter your rating (0-5)" required step="0.1" min="0" max="5" class="box">
         <input type="submit" value="Submit review" name="submit_review" class="btn">
      </form>
   </section>

<?php
// if (isset($message)) {
//    foreach ($message as $msg) {
//       echo '<div class="message"><span>' . $msg . '</span> <i class="fas fa-times" onclick="this.parentElement.style.display=`none`;"></i></div>';
//    }
// }
?>

<section class="reviews">
   <h1 class="heading">Client's reviews</h1>
   <div class="swiper reviews-slider">
      <div class="swiper-wrapper">
         <?php
         $select_reviews = $conn->prepare("SELECT * FROM `reviews`");
         $select_reviews->execute();
         $reviews = $select_reviews->fetchAll(PDO::FETCH_ASSOC);

         if ($reviews) {
            foreach ($reviews as $review) {
               echo '<div class="swiper-slide slide">';
               echo '<img src="images/' . htmlspecialchars($review['customer_image']) . '" alt="">';
               echo '<p>' . htmlspecialchars($review['review_text']) . '</p>';
               echo '<div class="stars">';

               for ($i = 0; $i < floor($review['rating']); $i++) {
                  echo '<i class="fas fa-star"></i>';
               }

               if ($review['rating'] - floor($review['rating']) >= 0.5) {
                  echo '<i class="fas fa-star-half-alt"></i>';
               }

               echo '</div>';
               echo '<h3>' . htmlspecialchars($review['customer_name']) . '</h3>';
               echo '</div>';
            }
         } else {
            echo '<p>No reviews available.</p>';
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
var swiper = new Swiper(".reviews-slider", {
   loop: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable: true,
   },
   breakpoints: {
      0: {
         slidesPerView: 1,
      },
      768: {
         slidesPerView: 2,
      },
      991: {
         slidesPerView: 3,
      },
   },
});
</script>

</body>
</html>
