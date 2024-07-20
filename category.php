<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
   

</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.heading {
    text-align: center;
    font-size: 2.5em;
    margin: 20px 0;
    color: #333;
}

.box-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    padding: 20px;
}

.swiper-slide.slide {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: calc(100% - 20px); /* Adjust this value for responsiveness */
    box-sizing: border-box;
    text-align: center;
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background-color: #fff;
}

.swiper-slide.slide:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.swiper-slide.slide img {
    width: 100%;
    height: auto;
    border-radius: 10px;
    margin-bottom: 10px;
}

.swiper-slide.slide h3 {
    font-size: 1.4em;
    color: #333;
    margin: 10px 0;
}

/* Additional styling for buttons and other elements */
.btn {
    display: inline-block;
    padding: 10px 20px;
    margin-top: 10px;
    background-color: #333;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #555;
}

.empty {
    text-align: center;
    font-size: 1.2em;
    color: #888;
    margin: 20px 0;
}

/* Media Queries for Responsiveness */
@media (max-width: 1200px) {
    .swiper-slide.slide {
        width: calc(33.333% - 20px);
    }
}

@media (max-width: 768px) {
    .swiper-slide.slide {
        width: calc(50% - 20px);
    }
}

@media (max-width: 480px) {
    .swiper-slide.slide {
        width: calc(100% - 20px);
    }
}

</style>
<body>

<?php include 'components/user_header.php'; ?>

<section class="products">

    <h1 class="heading">Category</h1>

    <div class="box-container">

    <?php
    // Initialize the seen_ids array
    $seen_ids = array();

    // Fetch categories
    $select_categories = $conn->prepare("SELECT * FROM `categories`");
    $select_categories->execute();

    if ($select_categories->rowCount() > 0) {
        while ($fetch_category = $select_categories->fetch(PDO::FETCH_ASSOC)) {
            // Check if the category ID has not been seen before
            if (!in_array($fetch_category['id'], $seen_ids)) {
                $seen_ids[] = $fetch_category['id']; // Add ID to seen IDs array
    ?>
   
    <a href="category.php?category=<?= htmlspecialchars($fetch_category['name']); ?>" class="swiper-slide slide">
        <img src="images/<?= htmlspecialchars($fetch_category['image']); ?>" alt="">
        <h3><?= htmlspecialchars($fetch_category['name']); ?></h3>
    </a>
    <?php
            }
        }
    } else {
        echo '<p class="empty">No categories found!</p>';
    }
    ?>

    </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
