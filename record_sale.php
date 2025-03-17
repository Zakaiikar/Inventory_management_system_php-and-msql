<?php
session_start();
$con = new mysqli("localhost", "root", "", "inv_db"); // Ensure the database name is correct
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

// Check if the form is submitted
if (isset($_POST['recordSale'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Validate quantity
    if ($quantity < 1) {
        echo "<script>alert('Quantity must be at least 1!');</script>";
    } else {
        // Fetch product price
        $sql = "SELECT price FROM products WHERE id=?";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $con->error);
        }
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $totalPrice = $product['price'] * $quantity;

            // Insert sale record
            $sqlInsert = "INSERT INTO sales (product_id, quantity, total_price) VALUES (?, ?, ?)";
            $stmtInsert = $con->prepare($sqlInsert);
            if (!$stmtInsert) {
                die("Prepare failed: " . $con->error);
            }
            $stmtInsert->bind_param("iid", $productId, $quantity, $totalPrice);
            if ($stmtInsert->execute()) {
                echo "<script>alert('Sale recorded successfully!'); window.location.href='sales.php';</script>";
            } else {
                echo "<script>alert('Error recording sale: " . $stmtInsert->error . "');</script>";
            }
        } else {
            echo "<script>alert('Product not found!');</script>";
        }
    }
}

// Fetch products for the sale form
$sqlProducts = "SELECT id, name FROM products";
$resultProducts = $con->query($sqlProducts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Sale</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">

<section class="max-w-lg w-full bg-white rounded-lg shadow-md p-6">
    <h1 class="mb-4 text-2xl font-bold text-center text-gray-800">Record Sale</h1>
    <form method="POST">
        <div class="mb-4">
            <label for="product_id" class="block text-gray-700">Select Product:</label>
            <select name="product_id" id="product_id" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" required>
                <option value="">-- Select Product --</option>
                <?php while ($product = $resultProducts->fetch_assoc()) { ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-4">
            <label for="quantity" class="block text-gray-700">Quantity:</label>
            <input type="number" name="quantity" id="quantity" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500" min="1" required>
        </div>
        <button type="submit" name="recordSale" class="w-full px-4 py-2 font-semibold text-white bg-purple-600 rounded-md hover:bg-purple-700 focus:outline-none">Record Sale</button>
    </form>
</section>

</body>
</html>