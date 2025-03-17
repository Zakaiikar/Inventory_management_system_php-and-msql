<?php 

if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch logged-in user details
$userId = $_SESSION['user_id'];
$stmt = $con->prepare("SELECT username, active, role FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$userRow = $userResult->fetch_assoc();

$username = htmlspecialchars($userRow['username']);
$isActive = $userRow['active'] == 1; // Check if the user is active
$role = $userRow['role']; // Get user role
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f4f8; /* Light gray background */
            padding-top: 120px; /* Ensures content is below fixed header and nav */
        }

        /* Header Styles */
        .header {
            position: fixed; /* Fix the header at the top */
            top: 0;
            left: 0;
            right: 0;
            background-color: #ffffff; /* White background */
            color: #34495e; /* Blue-gray color for text */
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000; /* Ensure it stays above other content */
            width: 100%;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            border-radius: 5px;
        }

        /* User info styling */
        .header .user-info {
            text-align: right;
        }

        .header .user-info p {
            margin: 0;
            font-size: 0.9rem;
        }

        .header .user-info span {
            font-weight: bold;
        }

        /* Navigation Styles */
        nav {
            position: fixed; /* Fix the nav below the header */
            background-color: #2c3e50; /* Blue-gray background */
            padding: 8px 20px;
            top: 70px; /* Space below the header */
            width: 100%;
            z-index: 999; /* Ensure it stays below the header */
            border-radius: 5px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        nav a {
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: background-color 0.3s, transform 0.3s;
        }

        nav a:hover {
            background-color: #34495e; /* Darker blue-gray on hover */
            transform: translateY(-1px); /* Slight lift effect on hover */
        }

        nav a.active {
            background-color: #3498db; /* Active link highlight */
        }
    </style>
</head>
<body>

<div class="header">
    <div class="user-info">
        <p>Logged in as: <span><?php echo $username; ?></span></p>
        <p>Status: <span class="<?php echo $isActive ? 'text-success' : 'text-danger'; ?>">
            <?php echo $isActive ? 'Active' : 'Inactive'; ?>
        </span></p>
    </div>
</div>

<nav>
    <a href="dashboard.php" class="active">Dashboard Overview</a>
    <?php if ($role === 'admin'): ?>
        <a href="sales.php">Sales Reports</a>
        <a href="insert.php">Insert Product</a>
        <a href="product_view_user.php">View User Products</a>
        <a href="register.php">Add User</a>
        <a href="user_management.php">User Management</a>
    <?php else: ?>
        <a href="product_list.php">View Products</a>
    <?php endif; ?>
    <a href="contact.php">Contact Us</a>
    <a href="logout.php">Logout</a>
</nav>

<!-- Add your page content here -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
