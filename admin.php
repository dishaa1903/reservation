<?php
// Start session and include database connection
session_start();
include('db.php');

// Redirect non-logged-in users to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle user role update
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>User role updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating user role.</div>";
    }
    $stmt->close();
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>User deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting user.</div>";
    }
    $stmt->close();
}

// Handle product addition
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $created_at = date('Y-m-d H:i:s');

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO products (name, image, price, description, quantity, created_at) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssi", $name, $image, $price, $description, $quantity, $created_at);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Product added successfully!</div>";
            header("Location: admin.php"); // Prevent form resubmission
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error adding product.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Error uploading image.</div>";
    }
}

// Fetch all users and products
$users = $conn->query("SELECT id, username, role FROM users");
$products = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="user-info">
                    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                </li>
                <li><a href="index.php">Home</a></li>                                  
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container py-5">
    <h1 class="mb-4">Admin Panel</h1>

    <!-- User Role Management Section -->
    <section class="mb-5">
        <h2>Manage User Roles</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id']; ?></td>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                        <td><?= htmlspecialchars($user['role']); ?></td>
                        <td>
                            <form method="POST" class="d-flex align-items-center">
                                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                <select name="role" class="form-select me-2">
                                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <button type="submit" name="update_role" class="btn btn-primary btn-sm me-2">Update</button>
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Product Management Section -->
    <section>
        <h2>Add New Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
        </form>
    </section>

    <!-- Product List Section -->
    <section class="mt-5">
        <h2>Existing Products</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?= $product['id']; ?></td>
                        <td><?= htmlspecialchars($product['name']); ?></td>
                        <td><img src="uploads/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" width="50"></td>
                        <td>$<?= htmlspecialchars($product['price']); ?></td>
                        <td><?= htmlspecialchars($product['description']); ?></td>
                        <td><?= htmlspecialchars($product['quantity']); ?></td>
                        <td><?= htmlspecialchars($product['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
