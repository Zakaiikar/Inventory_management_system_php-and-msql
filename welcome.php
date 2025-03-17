<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(to bottom, #f3f4f6, white); /* light gray */
        }
    </style>
</head>
<body>
    <section class="relative py-10 overflow-hidden gradient-bg sm:py-16 lg:py-24 xl:py-32">
        <div class="absolute inset-0">
            <img class="object-cover w-full h-full md:object-left md:scale-150 md:origin-top-left" src="imgss/last.jpeg" alt="Welcome" />
        </div>

        <div class="absolute inset-0 hidden bg-gradient-to-r md:block from-transparent to-transparent"></div>

        <div class="absolute inset-0 block bg-black/60 md:hidden"></div>

        <div class="relative px-4 mx-auto sm:px-6 lg:px-8 max-w-7xl">
            <div class="text-center md:w-2/3 lg:w-1/2 xl:w-1/3 md:text-left">
                <h2 class="text-3xl font-bold leading-tight text-purple-500 sm:text-4xl lg:text-5xl">Welcome to the Inventory Management System</h2>
                <p class="mt-4 text-base text-gray-900">Manage your products efficiently and effectively. Get started with your account today!</p>

                <div class="mt-8 lg:mt-12 flex justify-center space-x-4">
                    <a href="login.php" class="inline-flex items-center justify-center px-6 py-4 font-semibold text-white transition-all duration-200 bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:bg-blue-700">Login</a>
                </div>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>