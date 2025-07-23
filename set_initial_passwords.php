<?php
$server = "127.0.0.1:3308";
$username = "root";
$password = "";
$database = "agent_manage";

$con = new mysqli($server, $username, $password, $database);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Hash the default password "password"
$defaultPassword = "password";
$hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

// Update all rows where password is NULL
$sql = "UPDATE agent_details SET password = ? WHERE password IS NULL";

$stmt = $con->prepare($sql);
$stmt->bind_param("s", $hashedPassword);

if ($stmt->execute()) {
    echo "All NULL passwords successfully updated.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$con->close();
