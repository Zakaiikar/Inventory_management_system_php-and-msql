<?php 
session_start();
ob_start();
// Start output buffering

// Database connection
$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

// Fetch users from the database
$sql = "SELECT id, username, email, age, phone_number, sex, role, active FROM users";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
       body {
    background-color: #f8f9fa; /* Light gray background */
    margin-left: 250px; /* Space for sidebar */
    font-family: 'Arial', sans-serif; /* Change font family */
}



    </style>
</head>

<body>
    <div class="sidebar">
        <?php include 'Header.php'; ?>
    </div>

    <div class="container my-5">
        <h1 class="mb-4 text-center">User Management</h1>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Phone Number</th>
                        <th>Sex</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['age']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['sex']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td><?php echo $row['active'] ? 'Active' : 'Inactive'; ?></td>
                            <td>
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                    data-email="<?php echo htmlspecialchars($row['email']); ?>"
                                    data-age="<?php echo htmlspecialchars($row['age']); ?>"
                                    data-phone="<?php echo htmlspecialchars($row['phone_number']); ?>"
                                    data-sex="<?php echo htmlspecialchars($row['sex']); ?>"
                                    data-role="<?php echo htmlspecialchars($row['role']); ?>"
                                    data-active="<?php echo $row['active']; ?>">
                                    Update
                                </button>
                                <a href="user_management.php?did=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='9'>No users found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="updateId" id="updateId">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <input type="text" name="username" id="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="age" class="form-label">Age:</label>
                            <input type="number" name="age" id="age" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number:</label>
                            <input type="text" name="phone" id="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="sex" class="form-label">Sex:</label>
                            <select name="sex" id="sex" class="form-select" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role:</label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="active" class="form-label">Status:</label>
                            <select name="active" id="active" class="form-select" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="updateData">Update User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Populate modal with user data
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            button.addEventListener('click', function () {
                const modal = document.querySelector('#updateModal');
                modal.querySelector('#updateId').value = this.getAttribute('data-id');
                modal.querySelector('#username').value = this.getAttribute('data-username');
                modal.querySelector('#email').value = this.getAttribute('data-email');
                modal.querySelector('#age').value = this.getAttribute('data-age');
                modal.querySelector('#phone').value = this.getAttribute('data-phone');
                modal.querySelector('#sex').value = this.getAttribute('data-sex');
                modal.querySelector('#role').value = this.getAttribute('data-role');
                modal.querySelector('#active').value = this.getAttribute('data-active');
            });
        });
    </script>

    <?php
    // Handle deletion
    if (isset($_GET['did'])) {
        $uid = $_GET['did'];
        $sql = "DELETE FROM users WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $uid);
        if ($stmt->execute()) {
            echo "<script>alert('User with ID $uid was deleted successfully'); window.location.href='user_management.php';</script>";
        }
    }

    // Handle update
    if (isset($_POST['updateData'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $age = (int)$_POST['age'];
        $phone = $_POST['phone'];
        $sex = $_POST['sex'];
        $role = $_POST['role'];
        $active = (int)$_POST['active'];
        $uid = (int)$_POST['updateId'];

        $sqlUpdate = "UPDATE users SET username=?, email=?, age=?, phone_number=?, sex=?, role=?, active=? WHERE id=?";
        $stmt = $con->prepare($sqlUpdate);
        if ($stmt === false) {
            die("MySQL prepare failed: " . htmlspecialchars($con->error));
        }

        if (!$stmt->bind_param("ssisssii", $username, $email, $age, $phone, $sex, $role, $active, $uid)) {
            die("Bind failed: " . htmlspecialchars($stmt->error));
        }

        if ($stmt->execute()) {
            header("Location: user_management.php");
            exit;
        } else {
            echo "<script>alert('Error updating user: " . htmlspecialchars($stmt->error) . "');</script>";
        }
    }

    ob_end_flush(); // Send output to browser
    ?>
</body>
</html>