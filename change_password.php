<?php
session_start();

// Check if agent is logged in
if (!isset($_SESSION['agent_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$passwordChange = false;
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $server = "127.0.0.1:3308";
    $username = "root";
    $password = "";
    $database = "agent_manage";

    $con = new mysqli($server, $username, $password, $database);

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    $agent_id = $_SESSION['agent_id']; // Get agent ID from session
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $errorMsg = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $con->prepare("UPDATE agent_details SET password = ? WHERE agent_id = ?");
        $stmt->bind_param("si", $hashed_password, $agent_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $passwordChange = true;
            } else {
                $errorMsg = "No update occurred. Please verify your account.";
            }
        } else {
            $errorMsg = "Failed to update password. Try again.";
        }

        $stmt->close();
    }

    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Change Password</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto|Sriracha&display=swap" rel="stylesheet">
    <style>
        /* (same CSS as before — unchanged) */
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

        input {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s ease;
        }

        input:focus {
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

        .errorMsg {
            color: red;
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
            <h1>Change Password</h1>
            <p>Update your account password below</p>

            <p><strong>Agent ID:</strong> <?= htmlspecialchars($_SESSION['agent_id']) ?></p>

            <?php if ($passwordChange): ?>
                <p class="submitMsg">Password changed successfully!</p>
            <?php elseif (!empty($errorMsg)): ?>
                <p class="errorMsg"><?= $errorMsg ?></p>
            <?php endif; ?>

            <form method="POST" action="change_password.php">
                <input type="password" name="new_password" placeholder="Enter New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                <button class="btn" type="submit">Update Password</button>
            </form>

        </div>
    </div>
</body>

</html>