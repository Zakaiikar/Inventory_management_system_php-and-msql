<?php
session_start();
ob_start(); // Start output buffering

$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

// Fetch user role from session
$userRole = $_SESSION['role'] ?? 'user'; // Default to 'user' if not set

// Fetch products with search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM products WHERE name LIKE ? OR price LIKE ?";
$stmt = $con->prepare($sql);
$likeSearch = "%" . $search . "%";
$stmt->bind_param("ss", $likeSearch, $likeSearch);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Lists</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light gray background */
        }
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth hover effect */
            border: none; /* Remove default border */
            border-radius: 1.25rem; /* Slightly more rounded corners */
        }
        .card:hover {
            transform: translateY(-5px); /* Lift on hover */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4); /* Enhanced shadow effect */
        }
        .card-img-top {
            height: 200px; /* Fixed height for images */
            object-fit: cover; /* Keep aspect ratio */
            border-top-left-radius: 1.25rem; /* Matching rounded corners */
            border-top-right-radius: 1.25rem; /* Matching rounded corners */
        }
        .page-title {
            font-size: 2.5rem; /* Larger title */
            font-weight: bold;
            color: #333;
        }
        .btn-container {
            margin-top: 1rem; /* Space above buttons */
        }
        .btn {
            flex: 1; /* Equal button width */
            margin-right: 0.5rem; /* Space between buttons */
            transition: all 0.3s ease; /* Smooth transition for button color and scale */
        }
        .btn:hover {
            filter: brightness(90%); /* Darken button on hover */
            transform: scale(1.05); /* Slightly enlarge on hover */
        }
        .btn-primary {
            font-size: 1rem; /* Smaller text for add product button */
            padding: 8px 16px; /* Adjust padding for a more compact button */
            border-radius: 0.5rem; /* Rounded corners */
        }
        .btn:last-child {
            margin-right: 0; /* Remove margin for the last button */
        }
    </style>
     <?php include 'Header.php'; ?>
</head>
<body>
    <div class="container my-5">
        <h1 class="mb-4 text-center page-title">Product Lists</h1>

        <!-- Back Button -->
       
        <?php if ($userRole === 'admin') { ?>
            <div class="d-flex mb-3 justify-content-between">
                <a href="insert.php" class="btn btn-primary btn-sm">Add New Product</a> <!-- Changed to btn-sm -->
                <form method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by name or price..." aria-label="Search">
                    <button class="btn btn-outline-secondary btn-sm" type="submit">Search</button>
                </form>
            </div>
        <?php } ?>

        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array()) {
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card shadow-lg">
                        <a href="#">
                            <img class="card-img-top" src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" />
                        </a>
                        <div class="card-body">
                            <h5 class="card-title text-truncate"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text text-muted">$<?php echo htmlspecialchars($row['price']); ?></p>
                            <div class="d-flex btn-container">
                                <a href="product_view_user.php?id=<?php echo $row['id']; ?>" class="btn btn-info">View Details</a>
                                <?php if ($userRole === 'admin') { ?>
                                    <div class="d-flex">
                                        <a href="select.php?did=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                                        <button class="btn btn-warning ms-2" data-bs-toggle="modal" data-bs-target="#updateModal" 
                                            data-id="<?php echo $row['id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                            data-description="<?php echo htmlspecialchars($row['description']); ?>" 
                                            data-price="<?php echo htmlspecialchars($row['price']); ?>">
                                            Update
                                        </button>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                echo "<p>No products found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="updateId" id="updateId">
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name:</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description:</label>
                            <textarea name="description" id="description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price:</label>
                            <input type="text" name="price" id="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Upload New Image (optional):</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="updateData">Update Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate modal with product data
        $('#updateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id');
            var name = button.data('name');
            var description = button.data('description');
            var price = button.data('price');

            var modal = $(this);
            modal.find('#updateId').val(id);
            modal.find('#name').val(name);
            modal.find('#description').val(description);
            modal.find('#price').val(price);
        });
    </script>
</body>
</html>

<?php
// Handle deletion
if (isset($_GET['did'])) {
    $pid = $_GET['did'];
    $sql = "DELETE FROM products WHERE id=?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $pid);
    if ($stmt->execute()) {
        echo "<script>alert('Product with ID $pid was deleted successfully'); window.location.href='select.php';</script>";
    }
}

// Handle update
if (isset($_POST['updateData'])) {
    $fn = $_POST['name'];
    $add = $_POST['description'];
    $ph = $_POST['price'];
    $sd = $_POST['updateId'];
    $imgs = $_FILES['image']['name'];
    $target = "uploads/" . basename($imgs);

    if ($imgs) {
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        $sqlupdate = "UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?";
        $stmt = $con->prepare($sqlupdate);
        $stmt->bind_param("ssdsi", $fn, $add, $ph, $target, $sd);
    } else {
        $sqlupdate = "UPDATE products SET name=?, description=?, price=? WHERE id=?";
        $stmt = $con->prepare($sqlupdate);
        $stmt->bind_param("ssdi", $fn, $add, $ph, $sd);
    }

    if ($stmt->execute()) {
        header("location: select.php");
        exit;
    } else {
        echo "<script>alert('Error updating product: " . $stmt->error . "');</script>";
    }
}

ob_end_flush(); // Send output to browser
?>