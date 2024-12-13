<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if the request is a POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cardNumber = $_POST['cardNumber'];
    $expiryDate = $_POST['expiryDate'];
    $cvv = $_POST['cvv'];
    $cardName = $_POST['cardName'];

    // Simulate payment processing
    $paymentSuccessful = true; // In real-world, integrate a payment gateway here.

    if ($paymentSuccessful) {
        // Clear the cart stored in the session
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }

      // Send a response to the client to clear the cart in localStorage
echo "<script>
localStorage.removeItem('cart'); // Clear cart in the browser
</script>";

echo "Payment successful! Thank you for your purchase.";
echo "<br><a href='index.php'>Click here</a> or you will be redirected to the home page in 10 seconds...";
header("refresh:10;url=index.php");

    } else {
        echo "<script>
            alert('Payment failed. Please try again.');
            window.location.href = 'checkout.php'; // Redirect to checkout page
        </script>";
    }
} else {
    echo "Invalid request.";
}
?>
