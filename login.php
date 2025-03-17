<?php
session_start();

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $con = new mysqli("localhost", "root", "", "inv_db");
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // Prepare and execute the query
    $stmt = $con->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify user credentials
    if ($user && password_verify($password, $user['password'])) {
        if ($user['active'] == 1) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: Dashboard.php"); // Redirect to the dashboard
            exit();
        } else {
            $error_msg = "Your account is inactive.";
        }
    } else {
        $error_msg = "Invalid credentials!";
    }

    $stmt->close();
    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 py-10">

<section class="relative py-10 bg-gray-900 sm:py-16 lg:py-24">
    <div class="relative max-w-md px-4 mx-auto sm:px-0">
        <div class="overflow-hidden bg-white rounded-md shadow-lg">
            <div class="px-6 py-6 sm:px-8">
                <h2 class="text-3xl font-bold text-gray-900 text-center">User Login</h2>

                <form method="POST" class="mt-6">
                    <div class="space-y-4">
                        <div>
                            <label for="email" class="text-base font-medium text-gray-900">Email</label>
                            <input type="email" id="email" name="email" required class="block w-full p-2 text-black" placeholder="Enter your email">
                        </div>

                        <div>
                            <label for="password" class="text-base font-medium text-gray-900">Password</label>
                            <input type="password" id="password" name="password" required class="block w-full p-2 text-black" placeholder="Enter your password">
                        </div>
                        
                        <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 text-base font-semibold text-white bg-blue-600">Login</button>
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