<?php
// Start output buffering
ob_start();

session_start();

// Database connection
$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Check if admin user exists
$admin_check = $con->prepare("SELECT * FROM users WHERE role = 'admin'");
$admin_check->execute();
$result = $admin_check->get_result();

if ($result->num_rows === 0) {
    // Create a default admin account
    $default_admin_username = "admin";
    $default_admin_email = "admin@example.com";
    $default_admin_password = password_hash("AdminPassword123", PASSWORD_DEFAULT);
    $default_admin_age = 30; // Default age
    $default_admin_phone = "1234567890"; // Default phone number
    $default_admin_role = "admin";
    $default_admin_sex = "male"; // Default sex

    $insert_admin_sql = "INSERT INTO users (username, email, password, age, phone_number, sex, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $con->prepare($insert_admin_sql);
    $insert_stmt->bind_param("sssssss", $default_admin_username, $default_admin_email, $default_admin_password, $default_admin_age, $default_admin_phone, $default_admin_sex, $default_admin_role);
    $insert_stmt->execute();
    $insert_stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $age = $_POST['age'];
    $phone_number = $_POST['phone_number'];
    $sex = $_POST['sex'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users (username, email, password, age, phone_number, sex, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssssss", $username, $email, $password, $age, $phone_number, $sex, $role);
    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("Location: Dashboard.php");
        exit();
    } else {
        $error_msg = 'Error: ' . $stmt->error;
    }
    $stmt->close();
}
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <?php include 'Header.php'; ?>
</head>

<body class="bg-gray-900 py-10">

<section class="relative py-10 bg-gray-900 sm:py-16 lg:py-24">
    <div class="relative max-w-md px-4 mx-auto sm:px-0">
        <div class="overflow-hidden bg-white rounded-md shadow-lg">
            <div class="px-6 py-6 sm:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900">Create an Account</h2>
                </div>

                <form method="POST" class="mt-6">
                    <div class="space-y-4">
                        <div>
                            <label for="username" class="text-base font-medium text-gray-900">Username</label>
                            <div class="mt-1">
                                <input type="text" class="block w-full p-2 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600" id="username" name="username" required placeholder="Enter your username">
                            </div>
                        </div>

                        <div>
                            <label for="email" class="text-base font-medium text-gray-900">Email</label>
                            <div class="mt-1">
                                <input type="email" class="block w-full p-2 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600" id="email" name="email" required placeholder="Enter your email">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="text-base font-medium text-gray-900">Password</label>
                            <div class="mt-1">
                                <input type="password" class="block w-full p-2 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600" id="password" name="password" required placeholder="Enter your password">
                            </div>
                        </div>

                        <div>
                            <label for="age" class="text-base font-medium text-gray-900">Age</label>
                            <div class="mt-1">
                                <input type="number" class="block w-full p-2 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600" id="age" name="age" required placeholder="Enter your age">
                            </div>
                        </div>

                        <div>
                            <label for="phone_number" class="text-base font-medium text-gray-900">Phone Number</label>
                            <div class="mt-1">
                                <input type="text" class="block w-full p-2 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600" id="phone_number" name="phone_number" required placeholder="Enter your phone number">
                            </div>
                        </div>

                        <div>
                            <label for="sex" class="text-base font-medium text-gray-900">Sex</label>
                            <select class="block w-full p-2 mt-1 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-blue-600" id="sex" name="sex" required>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="role" class="text-base font-medium text-gray-900">Role</label>
                            <select class="block w-full p-2 mt-1 bg-white border border-gray-300 rounded-md focus:outline-none focus:border-blue-600" id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 text-base font-semibold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md focus:outline-none hover:bg-blue-700 focus:bg-blue-700">Register</button>
                    </div>
                </form>

                <?php if (isset($error_msg)): ?>
                    <div class="mt-4 text-center text-red-600"><?php echo htmlspecialchars($error_msg); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

</body>
</html>