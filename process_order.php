<?php
session_start();
$con = new mysqli("localhost", "root", "", "inv_db");

if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

$customerName = "";
$productId = 0;
$quantity = 0;
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting order data
    $customerName = htmlspecialchars($_POST['customer_name']);
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $email = htmlspecialchars($_POST['email']);

    // Log the received product ID for debugging
    error_log("Received product ID: " . $productId);

    // Validate product ID
    $productCheck = $con->prepare("SELECT * FROM products WHERE id = ?");
    if (!$productCheck) {
        die("Prepare failed: " . htmlspecialchars($con->error));
    }

    $productCheck->bind_param("i", $productId);
    $productCheck->execute();
    $productResult = $productCheck->get_result();

    if ($productResult->num_rows === 0) {
        // Product not found
        echo "<h1>Error</h1>";
        echo "<p>The selected product does not exist. Product ID: $productId</p>";
        echo "<a href='product_list.php'>Go back to the product list</a>";
        exit;
    }

    // Insert order into the database
    $sql = "INSERT INTO orders (customer_name, product_id, quantity, email) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    
    if (!$stmt) {
        die("Prepare failed: " . htmlspecialchars($con->error));
    }

    $stmt->bind_param("siis", $customerName, $productId, $quantity, $email);

    if ($stmt->execute()) {
        // Display confirmation page
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Order Confirmation</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        </head>
        <body class="bg-gray-200 flex items-center justify-center min-h-screen">
            <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
                <h1 class="text-3xl font-bold text-center text-blue-600 mb-4">Order Confirmation</h1>
                <div class="border border-blue-300 rounded-lg p-4 mb-6">
                    <p class="text-lg text-gray-700">Thank you, <span class="font-semibold text-gray-800"><?php echo $customerName; ?></span>! Your order has been placed successfully.</p>
                </div>
                <div class="space-y-2">
                    <p class="text-gray-800"><strong>Product ID:</strong> <span class="text-blue-500"><?php echo $productId; ?></span></p>
                    <p class="text-gray-800"><strong>Quantity:</strong> <span class="text-blue-500"><?php echo $quantity; ?></span></p>
                    <p class="text-gray-800"><strong>Email:</strong> <span class="text-blue-500"><?php echo $email; ?></span></p>
                </div>
                <div class="mt-6 text-center">
                    <a href="product_list.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-500 transition">Go back to the product list</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<h1>Error</h1>";
        echo "<p>There was an issue placing your order: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
} else {
    // Redirect back to the form if accessed directly
    header("Location: product_list.php");
    exit();
}

$con->close();
?>