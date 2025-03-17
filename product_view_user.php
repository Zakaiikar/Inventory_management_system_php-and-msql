<?php
session_start();
$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

$productId = $_GET['id'] ?? null;
if ($productId) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
} else {
    header("Location: select.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light gray background */
        }
        .card {
            border: none; /* Remove default border */
            border-radius: 1.25rem; /* Rounded corners */
            overflow: hidden; /* Clip overflow */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        .card img {
            height: 300px; /* Fixed height for images */
            object-fit: cover; /* Keep aspect ratio */
        }
        .btn {
            transition: all 0.3s ease; /* Smooth transition */
        }
        .btn:hover {
            filter: brightness(90%); /* Darken button on hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }
        .page-title {
            font-size: 2.5rem; /* Larger title */
            font-weight: bold;
            color: #333;
        }
    </style>
     <?php include 'Header.php'; ?>
</head>
<body>
    
    <div class="container my-5">
        <h1 class="mb-4 text-center page-title"><?php echo htmlspecialchars($product['name']); ?></h1>
        <div class="card mx-auto" style="width: 18rem;">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top" alt="Product Image">
            <div class="card-body">
                <h5 class="card-title text-center"><?php echo htmlspecialchars($product['name']); ?></h5>
                <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
                <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($product['price']); ?></p>
                <a href="select.php" class="btn btn-secondary w-100">Back to Product Lists</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>