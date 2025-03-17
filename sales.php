<?php
session_start();
ob_start();
$con = new mysqli("localhost", "root", "", "inv_db");
if ($con->connect_error) {
    die("Couldn't connect to the server: " . $con->connect_error);
}

$sql = "SELECT s.id, p.name AS product_name, s.quantity, s.sale_date, s.total_price 
        FROM sales s 
        JOIN products p ON s.product_id = p.id";
$result = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Records</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include 'Header.php'; ?>
</head>
<body class="bg-gray-900 text-gray-200">

    <div class="container mx-auto my-10 p-6 bg-white rounded-lg shadow-lg">
        <h1 class="mb-6 text-3xl font-bold text-center text-gray-800">Sales Records</h1>

        <!-- Back Button -->
        <div class="mb-3 text-center">
            <a href="dashboard.php" class="btn bg-gray-600 text-white hover:bg-gray-700 py-2 px-4 rounded">Back to Dashboard</a>
        </div>

        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md mb-6">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="py-3 px-4 border-b">ID</th>
                    <th class="py-3 px-4 border-b">Product Name</th>
                    <th class="py-3 px-4 border-b">Quantity</th>
                    <th class="py-3 px-4 border-b">Sale Date</th>
                    <th class="py-3 px-4 border-b">Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='hover:bg-gray-100'>
                                <td class='py-2 px-4 border-b text-black'>{$row['id']}</td>
                                <td class='py-2 px-4 border-b text-black'>{$row['product_name']}</td>
                                <td class='py-2 px-4 border-b text-black'>{$row['quantity']}</td>
                                <td class='py-2 px-4 border-b text-black'>{$row['sale_date']}</td>
                                <td class='py-2 px-4 border-b text-black'>\$" . htmlspecialchars($row['total_price']) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center py-4'>No sales recorded.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Chart Section -->
        <div class="mb-6">
            <canvas id="salesChart" class="w-full h-64"></canvas>
        </div>
    </div>

    <script>
        // Prepare data for the chart
        const salesData = {
            labels: [],
            datasets: [{
                label: 'Total Sales',
                data: [],
                backgroundColor: [
                    'rgba(45, 55, 72, 0.8)',
                    'rgba(75, 85, 99, 0.8)',
                    'rgba(100, 116, 139, 0.8)',
                    'rgba(156, 163, 175, 0.8)',
                    'rgba(209, 213, 219, 0.8)',
                    'rgba(229, 231, 235, 0.8)'
                ],
                borderWidth: 1,
                borderColor: 'rgba(30, 41, 59, 1)'
            }]
        };

        <?php
        // Reset the result pointer to fetch data for the chart
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            echo "salesData.labels.push('" . addslashes($row['product_name']) . "');";
            echo "salesData.datasets[0].data.push(" . $row['total_price'] . ");";
        }
        ?>

        // Chart configuration
        const config = {
            type: 'doughnut',
            data: salesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: 'rgba(30, 41, 59, 1)'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Sales by Product',
                        font: {
                            size: 20,
                            weight: 'bold',
                            family: 'Arial, sans-serif',
                        },
                        color: 'rgba(30, 41, 59, 1)'
                    }
                }
            },
        };

        // Render the chart
        const salesChart = new Chart(
            document.getElementById('salesChart'),
            config
        );
    </script>

</body>
</html>