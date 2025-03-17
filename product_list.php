<?php
session_start();
$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

// Fetch products
$sql = "SELECT * FROM products";
$result = $con->query($sql);

// Initialize variables for notification
$notification = null;

// Handle order submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $customerName = htmlspecialchars($_POST['customer_name']);
    $productId = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $email = htmlspecialchars($_POST['email']);

    // Validate product ID
    $productCheck = $con->prepare("SELECT * FROM products WHERE id = ?");
    $productCheck->bind_param("i", $productId);
    $productCheck->execute();
    $productResult = $productCheck->get_result();

    if ($productResult->num_rows === 0) {
        $notification = "<div class='toast align-items-center text-bg-danger border-0' role='alert' aria-live='assertive' aria-atomic='true'>
                            <div class='d-flex'>
                                <div class='toast-body'>
                                    The selected product does not exist.
                                </div>
                                <button type='button' class='btn-close btn-close-white' data-bs-dismiss='toast' aria-label='Close'></button>
                            </div>
                        </div>";
    } else {
        // Insert order into the database
        $sql = "INSERT INTO orders (customer_name, product_id, quantity, email) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("siis", $customerName, $productId, $quantity, $email);

        if ($stmt->execute()) {
            // Prepare success notification
            $notification = "<div class='toast align-items-center text-bg-success border-0' role='alert' aria-live='assertive' aria-atomic='true'>
                                <div class='d-flex'>
                                    <div class='toast-body'>
                                        Thank you, $customerName! Your order has been placed successfully.
                                    </div>
                                    <button type='button' class='btn-close btn-close-white' data-bs-dismiss='toast' aria-label='Close'></button>
                                </div>
                            </div>";
        } else {
            $notification = "<div class='toast align-items-center text-bg-danger border-0' role='alert' aria-live='assertive' aria-atomic='true'>
                                <div class='d-flex'>
                                    <div class='toast-body'>
                                        There was an issue placing your order: " . htmlspecialchars($stmt->error) . "
                                    </div>
                                    <button type='button' class='btn-close btn-close-white' data-bs-dismiss='toast' aria-label='Close'></button>
                                </div>
                            </div>";
        }

        $stmt->close();
    }
}
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
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 1.25rem;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 1.25rem;
            border-top-right-radius: 1.25rem;
        }
        .page-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
        }
        .toast-container {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1050; /* Ensure it is above other elements */
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

    <!-- Toast Notification Container -->
    <div class="toast-container">
        <?php if ($notification): ?>
            <?php echo $notification; ?>
        <?php endif; ?>
    </div>

    <!-- Order Modal -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="POST">
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
        document.getElementById('orderModal').addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var id = button.getAttribute('data-id'); // Extract info from data-* attributes

            // Update the modal's content.
            var modal = this;
            modal.querySelector('#modalProductId').value = id; // Set the hidden input for product_id
            modal.querySelector('#customer_name').value = ''; // Reset customer name
            modal.querySelector('#quantity').value = 1; // Reset quantity
            modal.querySelector('#email').value = ''; // Reset email
        });

        // Show toast notifications
        var toastElements = document.querySelectorAll('.toast');
        toastElements.forEach(function (toastEl) {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    </script>
</body>
</html>