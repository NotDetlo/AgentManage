<?php
$server = "127.0.0.1:3308";
$username = "root";
$password = "";
$database = "agent_manage";

$con = new mysqli($server, $username, $password, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch sales data
$salesQuery = "SELECT 
    handling_agent_id, agent_name, date_of_transaction, property_id, locality, area, square_feet, 
    num_bedrooms, num_bathrooms, parking_space, date_of_construction, 
    security_feature, final_price 
FROM transactions 
NATURAL JOIN property_details 
JOIN agent_details ON agent_details.agent_id = transactions.handling_agent_id 
WHERE transaction_type = 'Sale'
ORDER BY handling_agent_id, date_of_transaction";
$salesResult = $con->query($salesQuery);

// Fetch rental data
$rentalQuery = "SELECT 
    handling_agent_id, agent_name, COUNT(*) AS properties_rented, 
    SUM(final_price) AS total_rent_earned, 
    GROUP_CONCAT(DISTINCT locality SEPARATOR ', ') AS areas_covered, 
    GROUP_CONCAT(DATE(date_of_transaction) ORDER BY date_of_transaction SEPARATOR ', ') AS rental_dates
FROM transactions
NATURAL JOIN property_details
JOIN agent_details ON agent_details.agent_id = transactions.handling_agent_id
WHERE transaction_type = 'Rent'
GROUP BY handling_agent_id, agent_name
ORDER BY handling_agent_id";
$rentalResult = $con->query($rentalQuery);

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Real Estate Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            color: #333;
            text-align: center;
            position: relative;
            z-index: 0;
            overflow: hidden;
        }

        /* Blurred Background Image */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: url('pexels-flodahm-699459.jpg') no-repeat center center/cover;

            z-index: -1;
            opacity: 0.5;
        }

        h2,
        h3 {
            color: #2c3e50;
        }

        button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1c5980;
        }

        .table-container {
            display: none;
            margin-top: 20px;
        }

        table {
            width: 100%;
            max-width: 1200px;
            margin: auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0px 0px 10px #ccc;
        }

        th,
        td {
            padding: 12px 16px;
            border: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .back-btn {
            margin-top: 30px;
        }

        .back-btn a {
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
        }
    </style>

    <script>
        function toggleTable(tableId) {
            document.getElementById('salesTable').style.display = 'none';
            document.getElementById('rentalTable').style.display = 'none';

            document.getElementById(tableId).style.display = 'block';
        }
    </script>
</head>

<body>

    <h2>Real Estate Sales and Rental Reports</h2>

    <button onclick="toggleTable('salesTable')">View Sales</button>
    <button onclick="toggleTable('rentalTable')">View Rentals</button>

    <!-- Sales Table -->
    <div id="salesTable" class="table-container">
        <h3>Sales Report</h3>
        <table>
            <tr>
                <th>Agent ID</th>
                <th>Agent Name</th>
                <th>Date</th>
                <th>Property ID</th>
                <th>Locality</th>
                <th>Area</th>
                <th>square feet</th>
                <th>Bedrooms</th>
                <th>Bathrooms</th>
                <th>Parking</th>
                <th>Construction Date</th>
                <th>Security</th>
                <th>Selling Price</th>
            </tr>
            <?php while ($row = $salesResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['handling_agent_id']; ?></td>
                    <td><?php echo $row['agent_name']; ?></td>
                    <td><?php echo $row['date_of_transaction']; ?></td>
                    <td><?php echo $row['property_id']; ?></td>
                    <td><?php echo $row['locality']; ?></td>
                    <td><?php echo $row['area']; ?></td>
                    <td><?php echo $row['square_feet']; ?></td>
                    <td><?php echo $row['num_bedrooms']; ?></td>
                    <td><?php echo $row['num_bathrooms']; ?></td>
                    <td><?php echo $row['parking_space']; ?></td>
                    <td><?php echo $row['date_of_construction']; ?></td>
                    <td><?php echo $row['security_feature']; ?></td>
                    <td>₹<?php echo number_format($row['final_price']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Rentals Table -->
    <div id="rentalTable" class="table-container">
        <h3>Rental Report</h3>
        <table>
            <tr>
                <th>Agent ID</th>
                <th>Agent Name</th>
                <th>Properties Rented</th>
                <th>Total Rent Earned</th>
                <th>Areas Covered</th>
                <th>Rental Dates</th>
            </tr>
            <?php while ($row = $rentalResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['handling_agent_id']; ?></td>
                    <td><?php echo $row['agent_name']; ?></td>
                    <td><?php echo $row['properties_rented']; ?></td>
                    <td>₹<?php echo number_format($row['total_rent_earned']); ?></td>
                    <td><?php echo $row['areas_covered']; ?></td>
                    <td><?php echo $row['rental_dates']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div class="back-btn">
        <a href="login.php">⬅ Back to Login</a>
    </div>

</body>

</html>