<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login and Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 py-10">

<div class="flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-900">User Login</h2>
        <form method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-3 rounded-md hover:bg-blue-700 transition duration-200">Login</button>
            <div class="text-center mt-4">
                <a href="#reset-password" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
            </div>
        </form>

        <?php
        session_start();
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['reset'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $con = new mysqli("localhost", "root", "", "inv_db");
            if ($con->connect_error) {
                die("Connection failed: " . $con->connect_error);
            }

            $sql = "SELECT * FROM users WHERE email=?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header("location: welcome.php");
                exit();
            } else {
                echo '<div class="mt-4 text-red-600 text-center">Invalid credentials!</div>';
            }
        }
        ?>
    </div>
</div>

<section id="reset-password" class="relative py-10 bg-gray-900 sm:py-16 lg:py-24">
    <div class="relative max-w-lg px-4 mx-auto sm:px-0">
        <div class="overflow-hidden bg-white rounded-md shadow-md">
            <div class="px-4 py-6 sm:px-8 sm:py-7">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900">Reset Password</h2>
                </div>

                <form method="POST" class="mt-8">
                    <input type="hidden" name="reset" value="1">
                    <div class="space-y-5">
                        <div>
                            <label for="reset_email" class="text-base font-medium text-gray-900">Email</label>
                            <div class="mt-2.5">
                                <input type="email" id="reset_email" name="reset_email" placeholder="Enter your email" class="block w-full p-4 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-200 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600" required />
                            </div>
                        </div>

                        <div>
                            <label for="new_password" class="text-base font-medium text-gray-900">New Password</label>
                            <div class="mt-2.5">
                                <input type="password" id="new_password" name="new_password" placeholder="Enter your new password" class="block w-full p-4 text-black placeholder-gray-500 transition-all duration-200 bg-white border border-gray-200 rounded-md focus:outline-none focus:border-blue-600 caret-blue-600" required />
                            </div>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-4 text-base font-semibold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md focus:outline-none hover:bg-blue-700 focus:bg-blue-700">Reset Password</button>
                    </div>
                </form>

                <?php
                if (isset($_POST['reset'])) {
                    $reset_email = $_POST['reset_email'];
                    $new_password = $_POST['new_password'];
                    $message = '';

                    // Database connection
                    $con = new mysqli("localhost", "root", "", "inv_db");
                    if ($con->connect_error) {
                        die("Connection failed: " . $con->connect_error);
                    }

                    // Check if the email exists in the database
                    $sql = "SELECT * FROM users WHERE email=?";
                    $stmt = $con->prepare($sql);
                    $stmt->bind_param("s", $reset_email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // User found, update password
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_sql = "UPDATE users SET password=? WHERE email=?";
                        $update_stmt = $con->prepare($update_sql);
                        $update_stmt->bind_param("ss", $hashed_password, $reset_email);
                        
                        if ($update_stmt->execute()) {
                            $message = '<div class="mt-4 text-center text-green-600">Your password has been reset successfully!</div>';
                        } else {
                            $message = '<div class="mt-4 text-center text-red-600">Error updating password!</div>';
                        }
                        $update_stmt->close();
                    } else {
                        $message = '<div class="mt-4 text-center text-red-600">Email not found!</div>';
                    }

                    $stmt->close();
                    $con->close();

                    // Display the message
                    echo $message;
                }
                ?>

                <div class="mt-6 text-center">
                    <a href="login.php" class="inline-flex items-center justify-center px-4 py-2 text-base font-semibold text-blue-600 transition-all duration-200 bg-gray-200 border border-transparent rounded-md hover:bg-gray-300 focus:outline-none">Back to Login</a>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>