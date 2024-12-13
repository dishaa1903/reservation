<?php
session_start();
include 'functions.php';

// if (!isset($_SESSION['username'])) {
//     header("Location: login.php");
//     exit();
//}

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grocery Store</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
    </style>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <nav>
            <ul>
    <div class="logo">
        <h1>R&R</h1>
        <p>Grocery</p>
    </div>
    <li>
        <form method="GET" action="index.php" class="search-bar">
            <input type="text" name="search" placeholder="Search for products...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </li>
    <?php if ($isLoggedIn): ?>
        <li class="user-info">
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </li>
        <li><a href="logout.php">Logout</a></li>
    <?php else: ?>
        <li><a href="login.php">Login</a></li>
    <?php endif; ?>
    <li><a href="#home">Home</a></li>
    <li><a href="#products">Products</a></li>
    <li><a href="admin.php">Admin</a></li>
    <li><a href="cart.php">Cart</a></li>
    <li><a href="tel:+1235678890"><i class="fas fa-phone"></i> +123 5678 890</a></li>
</ul>

            </nav>
        </div>
    </header>

    <main>
        <section class="promo">
            <div class="promo-text">
                <h2>"Click, reserve, pick—it’s that simple!"</h2>
                <p>your one-stop online wholesale market for bulk purchases </p>
                <h3>Smart savings for bulk buyers</h3>
                <p> <span class="price"></span></p>
                <a href="#products" class="shop-button">Shop Now!</a>
            </div>
        </section>

        <section class="products">
        <h2 class="product-title">Our Products</h2>
        <div class="product-grid">
        <?php
                    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                    $products = getProducts($searchTerm);
                    foreach ($products as $product) {
                        echo '<div class="product">';
                        echo '<div class="product-image">';
                        echo '<img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">';
                        echo '</div>';
                        echo '<div class="product-details">';
                        echo '<h3>' . htmlspecialchars($product['name']) . '</h3>';
                        echo '<p>Price: ₹' . htmlspecialchars($product['price']) . '</p>';
                        echo '<button onclick="addToCart(\'' . htmlspecialchars($product['name']) . '\', ' . htmlspecialchars($product['price']) . ')">Add to Cart</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="social-share">
            <ul>
                <li><i class="fab fa-facebook"></i></li>
                <li><i class="fab fa-instagram"></i></li>
                <li><i class="fab fa-twitter"></i></li>
                <li><i class="fab fa-linkedin-in"></i></li>
                <li><i class="fab fa-github"></i></li>
            </ul>
        </div>
        <div class="general-info">
            <div class="help">
                <h3>Help</h3>
                <ul>
                    <li>frequently asked questions</li>
                    <li>any information</li>
                  
                    <li>product recall</li>
                    <li>customer care</li>
                </ul>
            </div>
            <div class="grocery">
                <h3>our location</h3>
                <ul>
                    <li>Shop No. 12, Market Street, Downtown City</li>
                    <li>Plot 45, Green Avenue, Springfield</li>
                    <li>Block B, Sector 5, Urban Plaza, New Town</li>
                    <li>Lane 3, Parkview Colony, Eastside</li>
                    <li>Shop 23, Central Bazaar, Midtown</li>
                </ul>
            </div>
            <div class="legal">
                <h3>@2024 Reserve &Ready.All Rights Reserved. </h3>
                <ul>
                    <li>cookies & privacy policy</li>
                    <li>terms & conditions</li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="scripts.js"></script>
</body>
</html>
