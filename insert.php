<?php session_start();
ob_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <?php include 'Header.php'; ?>
</head>

<body class="bg-gray-900 py-20"> <!-- Increase padding-top to move content down -->

<div class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8 mt-20"> <!-- Added mt-20 to move form down -->
        <h3 class="text-2xl font-bold text-center mb-6 text-gray-900">Add New Product</h3>
        
        <!-- Back Button -->
        <!-- You can add a back button here if needed -->

        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name:</label>
                <input type="text" name="name" id="name" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                <textarea name="description" id="description" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" required></textarea>
            </div>
            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700">Price:</label>
                <input type="text" name="price" id="price" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" required>
            </div>
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700">Upload Image:</label>
                <input type="file" name="image" id="image" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600" accept="image/*" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded-md hover:bg-blue-700 transition duration-200" name="saveData">Save Product</button>
        </form>
    </div>
</div>

<?php 
// Enable error reporting

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

if (isset($_POST['saveData'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle file upload
    $image = $_FILES['image']['name'];
    $target = "uploads/" . basename($image);

    // Move the uploaded file to the designated directory
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $sql = "INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssds", $name, $description, $price, $target);
        if ($stmt->execute()) {
            echo "<script>alert('Product inserted successfully'); window.location.href='select.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Failed to upload image.";
    }
} 
?>

</body>
</html>
