<?php
session_start(); // Start the session

// Initialize variables for form submission
$name = $email = $message = "";
$name_err = $email_err = $message_err = "";

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate message
    if (empty(trim($_POST["message"]))) {
        $message_err = "Please enter your message.";
    } else {
        $message = trim($_POST["message"]);
    }

    // Check for errors before sending the message
    if (empty($name_err) && empty($email_err) && empty($message_err)) {
        // Here, you would typically send the email or store the message in a database.
        // Simulate a successful submission by saving a message in the session
        $_SESSION['success_msg'] = "Thank you for contacting us, $name. We will get back to you soon!";
        
        // Redirect to dashboard.php
        header("Location: dashboard.php");
        exit(); // Make sure to exit after the redirect
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
</head>
<body class="bg-gray-100 py-10">

<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold mb-6 text-center">Contact Us</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="bg-white p-6 rounded shadow-md">
        <div class="mb-4">
            <label for="name" class="block text-sm font-bold mb-2">Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" class="border border-gray-300 p-2 w-full rounded" required>
            <span class="text-red-500 text-sm"><?php echo $name_err; ?></span>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-bold mb-2">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" class="border border-gray-300 p-2 w-full rounded" required>
            <span class="text-red-500 text-sm"><?php echo $email_err; ?></span>
        </div>
        <div class="mb-4">
            <label for="message" class="block text-sm font-bold mb-2">Message</label>
            <textarea name="message" id="message" class="border border-gray-300 p-2 w-full rounded" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
            <span class="text-red-500 text-sm"><?php echo $message_err; ?></span>
        </div>
        <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded">Send Message</button>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>