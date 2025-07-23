<?php
session_start();

$transactionInsert = false;
$agent_name = "";

// Redirect to login if not logged in
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php");
    exit();
}

$agent_id = $_SESSION['agent_id'];

// Fetch agent name
$server = "127.0.0.1:3308";
$username = "root";
$password = "";
$database = "agent_manage";

$con = new mysqli($server, $username, $password, $database);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$query = $con->prepare("SELECT agent_name FROM agent_details WHERE agent_id = ?");
$query->bind_param("i", $agent_id);
$query->execute();
$query->bind_result($agent_name);
$query->fetch();
$query->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['property_id'])) {
        $property_id = $_POST['property_id'];
        $transaction_type = $_POST['transaction_type'];
        $final_price = $_POST['final_price'];
        $date_of_transaction = $_POST['date_of_transaction'];
        $customer_name = $_POST['customer_name'];
        $customer_contact = $_POST['customer_contact'];
        $address = $_POST['address'];
        $status = $_POST['status'];

        $stmt = $con->prepare("INSERT INTO transactions (property_id, handling_agent_id, transaction_type, final_price, date_of_transaction, customer_name, customer_contact, address, status) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisisssss", $property_id, $agent_id, $transaction_type, $final_price, $date_of_transaction, $customer_name, $customer_contact, $address, $status);


        if ($stmt->execute()) {
            $transactionInsert = true;
        } else {
            echo "ERROR: " . $stmt->error;
        }

        $stmt->close();
    }
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transaction Entry</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Sriracha&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            display: flex;
        }

        .sidebar {
            width: 70px;
            background-color: #343a40;
            height: 100vh;
            position: fixed;
            transition: width 0.3s;
            overflow-x: hidden;
            z-index: 1000;
        }

        .sidebar:hover {
            width: 200px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 16px;
            color: #f1f1f1;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.3s;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar svg {
            margin-right: 10px;
            min-width: 24px;
            fill: white;
        }

        .sidebar span {
            display: none;
        }

        .sidebar:hover span {
            display: inline;
        }

        .main-content {
            margin-left: 70px;
            padding: 40px 20px;
            width: 100%;
            transition: margin-left 0.3s;
        }

        .sidebar:hover~.main-content {
            margin-left: 200px;
        }

        .container {
            max-width: 600px;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        h1 {
            margin-bottom: 10px;
            color: #333;
            font-size: 26px;
        }

        p {
            margin-bottom: 20px;
            color: #555;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .submitMsg {
            color: green;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            font-size: 16px;
            color: #007bff;
            font-weight: bold;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <a href="index.php">
            <svg width="24" height="24" viewBox="0 0 24 24">
                <path d="M3 12l9-9 9 9h-3v9h-12v-9h-3z" />
            </svg>
            <span>Home</span>
        </a>
        <a href="change_password.php">
            <svg width="24" height="24" viewBox="0 0 24 24">
                <path d="M12 17a2 2 0 100-4 2 2 0 000 4zm6-9v-1c0-2.757-2.243-5-5-5s-5 2.243-5 5v1h-1c-.553 0-1 .447-1 1v11c0 .553.447 1 1 1h12c.553 0 1-.447 1-1v-11c0-.553-.447-1-1-1h-1zm-8-1c0-1.654 1.346-3 3-3s3 1.346 3 3v1h-6v-1z" />
            </svg>
            <span>Change Password</span>
        </a>
        <a href="logout.php">
            <svg width="24" height="24" viewBox="0 0 24 24">
                <path d="M16 13v-2h-8v-3l-5 4 5 4v-3h8zm3-10h-14c-1.104 0-2 .896-2 2v6h2v-6h14v14h-14v-6h-2v6c0 1.104.896 2 2 2h14c1.104 0 2-.896 2-2v-14c0-1.104-.896-2-2-2z" />
            </svg>
            <span>Logout</span>
        </a>
    </div>


    <div class="main-content">
        <div class="container">
            <h1>Welcome <?php echo htmlspecialchars($agent_name); ?> (Agent ID: <?php echo $agent_id; ?>)</h1>
            <h2>Transaction Entry</h2>
            <p>Enter transaction details</p>

            <?php if ($transactionInsert): ?>
                <p class="submitMsg">Transaction details submitted successfully</p>
            <?php endif; ?>

            <form action="index.php" method="post">
                <input type="text" name="property_id" id="property_id" placeholder="Enter Property ID" required>
                <input type="hidden" name="handling_agent_id" value="<?php echo $agent_id; ?>">

                <select name="transaction_type" id="transaction_type" required>
                    <option value="">Select Transaction Type</option>
                    <option value="Rent">Rent</option>
                    <option value="Sale">Sale</option>
                </select>

                <input type="number" name="final_price" id="final_price" placeholder="Enter Final Price" required>
                <input type="date" name="date_of_transaction" id="date_of_transaction" required>
                <input type="text" name="customer_name" id="customer_name" placeholder="Enter Customer Name" required>
                <input type="text" name="customer_contact" id="customer_contact" placeholder="Enter Customer Contact">

                <textarea name="address" id="address" rows="3" placeholder="Enter Customer Address" required></textarea>

                <select name="status" id="status" required>
                    <option value="Available" selected>Available</option>
                    <option value="Rented">Rented</option>
                    <option value="Sold">Sold</option>
                </select>

                <button class="btn" type="submit">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>