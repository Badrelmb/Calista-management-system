<?php
session_start();
require 'config.php'; // Include the configuration file

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "sales_management");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

function getProfit($mysqli, $interval, $category = null)
{
    $category_filter = $category ? "AND category = '$category'" : "";
    $query = "SELECT SUM(selling_price - price_of_purchase) AS profit 
              FROM items 
              WHERE selling_price IS NOT NULL 
              $category_filter 
              AND date_of_sale >= DATE_SUB(NOW(), INTERVAL $interval)";
    $result = $mysqli->query($query);

    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }

    $row = $result->fetch_assoc();
    return $row['profit'] ? number_format($row['profit'], 2) : '0.00';
}

function getItemsSold($mysqli, $interval, $category = null)
{
    $category_filter = $category ? "AND category = '$category'" : "";
    $query = "SELECT category, name_brand, photos, date_of_purchase, price_of_purchase, selling_price, 
              (selling_price - price_of_purchase) AS profit 
              FROM items 
              WHERE selling_price IS NOT NULL 
              $category_filter 
              AND date_of_sale >= DATE_SUB(NOW(), INTERVAL $interval)";
    $result = $mysqli->query($query);

    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getCategories($mysqli)
{
    $query = "SELECT DISTINCT category FROM items";
    $result = $mysqli->query($query);

    if (!$result) {
        die("Query failed: " . $mysqli->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

$total_profit = getProfit($mysqli, '100 YEAR');
$yearly_profit = getProfit($mysqli, '1 YEAR');
$monthly_profit = getProfit($mysqli, '1 MONTH');
$weekly_profit = getProfit($mysqli, '1 WEEK');

$total_items = getItemsSold($mysqli, '100 YEAR');
$yearly_items = getItemsSold($mysqli, '1 YEAR');
$monthly_items = getItemsSold($mysqli, '1 MONTH');
$weekly_items = getItemsSold($mysqli, '1 WEEK');

$categories = getCategories($mysqli);

// Close the connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stats</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <div class="header">
        <h1><a href="index.php">초아's Management System</a></h1>
        <img src="images/heart2.png" alt="Heart">
    </div>
    <div class="container">
        <h2>Statistics</h2>
        <div class="form-group">
            <label for="profit-select">Choose Profit Period:</label>
            <select class="form-control" id="profit-select" onchange="updateProfit()">
                <option value="total">Total Profit</option>
                <option value="yearly">Yearly Profit</option>
                <option value="monthly">Monthly Profit</option>
                <option value="weekly">Weekly Profit</option>
            </select>
        </div>
        <div class="form-group">
            <label for="category-select">Choose Category:</label>
            <select class="form-control" id="category-select" onchange="updateProfit()">
                <option value="all">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['category'] ?>"><?= $category['category'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="profit-display">
            <h3>Total Profit: ₩<span id="total-profit"><?= $total_profit ?></span></h3>
            <h3 style="display:none;">Yearly Profit: ₩<span id="yearly-profit"><?= $yearly_profit ?></span></h3>
            <h3 style="display:none;">Monthly Profit: ₩<span id="monthly-profit"><?= $monthly_profit ?></span></h3>
            <h3 style="display:none;">Weekly Profit: ₩<span id="weekly-profit"><?= $weekly_profit ?></span></h3>
        </div>
        <div id="items-display">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Name/Brand</th>
                        <th>Photos</th>
                        <th>Date of Purchase</th>
                        <th>Price of Purchase</th>
                        <th>Selling Price</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody id="total-items">
                    <?php foreach ($total_items as $item):
                        $photos = json_decode($item['photos'], true); // Decode the JSON data
                        $first_photo = isset($photos[0]) ? $photos[0] : 'uploads/default_photo.png'; // Use the path as it is
                        ?>
                        <tr>
                            <td><?= $item['category'] ?></td>
                            <td><?= $item['name_brand'] ?></td>
                            <td><img src="<?= $first_photo ?>" alt="<?= $item['name_brand'] ?>" style="width: 50px;"></td>
                            <td><?= $item['date_of_purchase'] ?></td>
                            <td>₩<?= number_format($item['price_of_purchase'], 2) ?></td>
                            <td>₩<?= number_format($item['selling_price'], 2) ?></td>
                            <td>₩<?= number_format($item['profit'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>




                </tbody>
                <tbody id="yearly-items" style="display:none;">
                    <?php foreach ($yearly_items as $item): ?>
                        <tr>
                            <td><?= $item['category'] ?></td>
                            <td><?= $item['name_brand'] ?></td>
                            <td><img src="<?= $item['photos'] ?>" alt="<?= $item['name_brand'] ?>" style="width: 50px;">
                            </td>
                            <td><?= $item['date_of_purchase'] ?></td>
                            <td>₩<?= number_format($item['price_of_purchase'], 2) ?></td>
                            <td>₩<?= number_format($item['selling_price'], 2) ?></td>
                            <td>₩<?= number_format($item['profit'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tbody id="monthly-items" style="display:none;">
                    <?php foreach ($monthly_items as $item): ?>
                        <tr>
                            <td><?= $item['category'] ?></td>
                            <td><?= $item['name_brand'] ?></td>
                            <td><img src="<?= $item['photos'] ?>" alt="<?= $item['name_brand'] ?>" style="width: 50px;">
                            </td>
                            <td><?= $item['date_of_purchase'] ?></td>
                            <td>₩<?= number_format($item['price_of_purchase'], 2) ?></td>
                            <td>₩<?= number_format($item['selling_price'], 2) ?></td>
                            <td>₩<?= number_format($item['profit'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tbody id="weekly-items" style="display:none;">
                    <?php foreach ($weekly_items as $item): ?>
                        <tr>
                            <td><?= $item['category'] ?></td>
                            <td><?= $item['name_brand'] ?></td>
                            <td><img src="<?= $item['photos'] ?>" alt="<?= $item['name_brand'] ?>" style="width: 50px;">
                            </td>
                            <td><?= $item['date_of_purchase'] ?></td>
                            <td>₩<?= number_format($item['price_of_purchase'], 2) ?></td>
                            <td>₩<?= number_format($item['selling_price'], 2) ?></td>
                            <td>₩<?= number_format($item['profit'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="footer">
        Made by the love of your life "Badr"
    </div>
    <script src="js/index.js"></script>
    <script>
        function updateProfit() {
            var periodSelect = document.getElementById('profit-select');
            var categorySelect = document.getElementById('category-select');
            var period = periodSelect.value;
            var category = categorySelect.value;
            var profitDisplay = document.getElementById('profit-display');
            var itemsDisplay = document.getElementById('items-display');

            var totalProfit = profitDisplay.querySelector('h3:nth-of-type(1)');
            var yearlyProfit = profitDisplay.querySelector('h3:nth-of-type(2)');
            var monthlyProfit = profitDisplay.querySelector('h3:nth-of-type(3)');
            var weeklyProfit = profitDisplay.querySelector('h3:nth-of-type(4)');

            var totalItems = itemsDisplay.querySelector('#total-items');
            var yearlyItems = itemsDisplay.querySelector('#yearly-items');
            var monthlyItems = itemsDisplay.querySelector('#monthly-items');
            var weeklyItems = itemsDisplay.querySelector('#weekly-items');

            totalProfit.style.display = 'none';
            yearlyProfit.style.display = 'none';
            monthlyProfit.style.display = 'none';
            weeklyProfit.style.display = 'none';
            totalItems.style.display = 'none';
            yearlyItems.style.display = 'none';
            monthlyItems.style.display = 'none';
            weeklyItems.style.display = 'none';

            if (period === 'total') {
                totalProfit.style.display = 'block';
                totalItems.style.display = 'table-row-group';
            } else if (period === 'yearly') {
                yearlyProfit.style.display = 'block';
                yearlyItems.style.display = 'table-row-group';
            } else if (period === 'monthly') {
                monthlyProfit.style.display = 'block';
                monthlyItems.style.display = 'table-row-group';
            } else if (period === 'weekly') {
                weeklyProfit.style.display = 'block';
                weeklyItems.style.display = 'table-row-group';
            }

            // Filter items by category if category is not 'all'
            var items = document.querySelectorAll('#items-display tbody tr');
            items.forEach(function (item) {
                var itemCategory = item.querySelector('td:nth-of-type(1)').textContent.trim();
                if (category === 'all' || itemCategory === category) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>