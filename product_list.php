<?php
session_start();
$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

// Fetch products
$sql = "SELECT * FROM products";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
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
    </style>
</head>
<body>
<?php include 'Header.php'; ?>
    <div class="container my-5">
        <h1 class="mb-4 text-center page-title">Product List</h1>

      

        <div class="row">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_array()) {
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card shadow-lg">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" class="card-img-top" alt="Product Image" 
                             data-bs-toggle="modal" data-bs-target="#orderModal" 
                             data-id="<?php echo $row['id']; ?>" 
                             data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                             data-price="<?php echo htmlspecialchars($row['price']); ?>">
                        <div class="card-body">
                            <h5 class="card-title text-truncate"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($row['description']); ?></p>
                            <p class="card-text"><strong>Price:</strong> $<?php echo htmlspecialchars($row['price']); ?></p>
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

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="process_order.php" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel">Place Your Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="modalProductId">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Customer Name:</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit Order</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate modal with product data
        $('#orderModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var price = button.getAttribute('data-price');

            var modal = $(this);
            modal.find('#modalProductId').val(id);
            modal.find('#customer_name').val('');
            modal.find('#quantity').val(1);
            modal.find('#email').val('');
        });
    </script>
</body>
</html>