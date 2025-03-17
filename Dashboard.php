<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit();
}

// Database connection
$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

// Fetch user details (assuming these are stored in the session or database)
$user_id = $_SESSION['user_id'];
$userSql = "SELECT username, role, active FROM users WHERE id = $user_id";
$userResult = $con->query($userSql);
if ($userResult->num_rows > 0) {
    $userData = $userResult->fetch_assoc();
    $username = $userData['username'];
    $role = $userData['role'];
    $isActive = $userData['active'];
} else {
    die("User not found.");
}

// Fetch inventory data
$inventorySql = "SELECT COUNT(*) AS total_products FROM products";
$inventoryResult = $con->query($inventorySql);
$inventoryData = $inventoryResult->fetch_assoc();

// Fetch user growth data
$userGrowthSql = "
    SELECT  
        DATE(created_at) AS date, 
        COUNT(CASE WHEN active = 1 THEN 1 END) AS active_count,
        COUNT(CASE WHEN active = 0 THEN 1 END) AS inactive_count
    FROM users 
    GROUP BY DATE(created_at)
";
$userGrowthResult = $con->query($userGrowthSql);

// Fetch total orders data
$ordersSql = "
    SELECT 
        COUNT(*) AS total_orders, 
        SUM(quantity) AS total_quantity 
    FROM orders
";
$ordersResult = $con->query($ordersSql);
$ordersData = $ordersResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">

<div class="flex h-screen">
<!-- Sidebar -->
<div class="w-48 bg-gray-800 text-white shadow-lg p-5 fixed h-full">
    <h2 class="text-lg font-bold mb-5">Dashboard</h2>
    <div class="mb-5">
        <p class="text-sm">Logged in as: <span class="font-semibold"><?php echo $username; ?></span></p>
        <p class="text-sm">
            Status: 
            <span class="<?php echo $isActive ? 'text-green-400' : 'text-red-400'; ?>">
                <?php echo $isActive ? 'Active' : 'Inactive'; ?>
            </span>
        </p>
    </div>
    <ul>
        <li class="mb-4">
            <a href="dashboard.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                <i class="fas fa-chart-line mr-2"></i> 
                <span class="text-sm">Dashboard Overview</span>
            </a>
        </li>
        <?php if ($role === 'admin'): ?>
            <li class="mb-4">
                <a href="sales.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-chart-pie mr-2"></i>
                    <span class="text-sm">Sales Reports</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="insert.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-plus mr-2"></i>
                    <span class="text-sm">Insert Product</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="product_view_user.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-eye mr-2"></i>
                    <span class="text-sm">View User Products</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="register.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-user-plus mr-2"></i>
                    <span class="text-sm">Add User</span>
                </a>
            </li>
            <li class="mb-4">
                <a href="user_management.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-users mr-2"></i>
                    <span class="text-sm">User Management</span>
                </a>
            </li>
        <?php else: ?>
            <li class="mb-4">
                <a href="product_list.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                    <i class="fas fa-eye mr-2"></i>
                    <span class="text-sm">View Products</span>
                </a>
            </li>
        <?php endif; ?>
        <li class="mb-4">
            <a href="contact.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                <i class="fas fa-envelope mr-2"></i>
                <span class="text-sm">Contact Us</span>
            </a>
        </li>
        <li class="mb-4">
            <a href="logout.php" class="flex items-center p-2 rounded hover:bg-gray-700 transition">
                <i class="fas fa-sign-out-alt mr-2"></i>
                <span class="text-sm">Logout</span>
            </a>
        </li>
    </ul>
</div>

<!-- Main Content -->
<div class="flex-1 ml-48 p-5 overflow-y-auto">
    <h1 class="text-3xl font-bold mb-5">Dashboard Overview</h1>

    <!-- Add Product Button -->
    <?php if ($role === 'admin'): ?>
        <div class="mb-5">
            <a href="insert.php" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600 transition">
                <i class="fas fa-plus mr-2"></i> Add New Product
            </a>
        </div>
    <?php endif; ?>

    <!-- Updated Two Containers -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Total Orders Overview -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-5 rounded-lg shadow-lg text-white">
            <h2 class="text-xl font-semibold mb-3">Total Orders Overview</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg">Total Orders</p>
                    <p class="text-4xl font-bold"><?php echo $ordersData['total_orders']; ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-lg">Total Quantity Sold</p>
                <p class="text-3xl font-bold"><?php echo $ordersData['total_quantity']; ?></p>
            </div>
        </div>

        <!-- Inventory Overview -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 p-5 rounded-lg shadow-lg text-white">
            <h2 class="text-xl font-semibold mb-3">Inventory Overview</h2>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg">Total Products</p>
                    <p class="text-4xl font-bold"><?php echo $inventoryData['total_products']; ?></p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-full">
                    <i class="fas fa-boxes text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- User Growth Chart -->
    <div class="bg-white p-5 rounded shadow mb-5 mt-5">
        <h2 class="text-xl font-semibold mb-3">User Growth</h2>
        <div class="h-40">
            <canvas id="userGrowthChart"></canvas>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-5">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-5 md:mb-0">
                    <h3 class="text-lg font-semibold mb-2">Contact Us</h3>
                    <p>Email: Zakariyeikar@gmail.com</p>
                    <p>Phone: (252) 61-7712723</p>
                </div>
                <div class="mb-5 md:mb-0">
                    <h3 class="text-lg font-semibold mb-2">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-gray-400"><i class="fab fa-facebook-square"></i></a>
                        <a href="#" class="hover:text-gray-400"><i class="fab fa-twitter-square"></i></a>
                        <a href="#" class="hover:text-gray-400"><i class="fab fa-instagram-square"></i></a>
                        <a href="#" class="hover:text-gray-400"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="mb-5 md:mb-0">
                    <h3 class="text-lg font-semibold mb-2">Quick Links</h3>
                    <ul>
                        <li><a href="dashboard.php" class="hover:text-gray-400">Dashboard</a></li>
                        <li><a href="product_list.php" class="hover:text-gray-400">View Products</a></li>
                        <li><a href="sales.php" class="hover:text-gray-400">Sales Reports</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-4">
                <p>&copy; <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>
</div>

<script>
// Prepare data for the user growth chart
const userGrowthData = {
    labels: [],
    datasets: [
        {
            label: 'Active Users',
            data: [],
            backgroundColor: 'rgba(75, 85, 99, 0.8)',
            borderWidth: 1,
            borderColor: 'rgba(30, 41, 59, 1)',
        },
        {
            label: 'Inactive Users',
            data: [],
            backgroundColor: 'rgba(255, 99, 132, 0.8)',
            borderWidth: 1,
            borderColor: 'rgba(255, 99, 132, 1)',
        }
    ]
};

<?php
while ($row = $userGrowthResult->fetch_assoc()) {
    echo "userGrowthData.labels.push('" . $row['date'] . "');";
    echo "userGrowthData.datasets[0].data.push(" . $row['active_count'] . ");"; // Active count
    echo "userGrowthData.datasets[1].data.push(" . $row['inactive_count'] . ");"; // Inactive count
}
?>

const userGrowthChartConfig = {
    type: 'line',
    data: userGrowthData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    color: 'rgba(30, 41, 59, 1)'
                }
            },
            title: {
                display: true,
                text: 'User Growth Over Time',
                font: {
                    size: 20,
                    weight: 'bold',
                    family: 'Arial, sans-serif',
                },
                color: 'rgba(30, 41, 59, 1)'
            }
        },
    },
};

const userGrowthChart = new Chart(
    document.getElementById('userGrowthChart'),
    userGrowthChartConfig
);
</script>

</body>
</html>