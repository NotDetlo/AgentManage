<?php
$server = "127.0.0.1:3308";
$username = "root";
$password = "";
$database = "agent_manage";

$con = new mysqli($server, $username, $password, $database);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$result = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['query'])) {
    $query = trim($_POST['query']);

    $restricted_keywords = ["DROP TABLE", "DELETE FROM", "TRUNCATE", "ALTER TABLE", "DROP DATABASE"];
    foreach ($restricted_keywords as $keyword) {
        if (stripos($query, $keyword) !== false) {
            $error = "Error: Restricted SQL command detected!";
            $query = "";
            break;
        }
    }

    if (!empty($query)) {
        $result = $con->query($query);
        if (!$result) {
            $error = "SQL Error: " . $con->error;
        }
    }
}
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Database Admin Panel</title>
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans" rel="stylesheet">
    <style>
        body {
            background-color: #E1E8EE;
            font-family: 'Fira Sans', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            min-height: 100vh;
            margin: 0;
        }

        .panel-container {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 40px;
            max-width: 800px;
            width: 100%;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #444;
            margin-bottom: 10px;
        }

        textarea {
            width: 100%;
            max-width: 100%;
            min-width: 100%;
            height: 120px;
            border: 1px solid #ccc;
            border-radius: 15px;
            padding: 15px;
            font-size: 14px;
            background-color: #f9f9f9;
            resize: vertical;
            box-sizing: border-box;
        }

        button {
            background-color: rgba(0, 0, 0, 0.4);
            color: rgba(256, 256, 256, 0.7);
            border: none;
            border-radius: 15px;
            padding: 15px 45px;
            width: 100%;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .error {
            color: red;
            margin-top: 15px;
        }

        a {
            display: block;
            margin-top: 25px;
            text-align: center;
            text-decoration: none;
            color: #6B92A4;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .table-wrapper {
            overflow-x: auto;
            max-width: 100%;
            margin-top: 15px;
            border-radius: 10px;
        }
    </style>

</head>

<body>
    <div class="panel-container">
        <h2>Database Administrator Panel</h2>
        <form method="POST">
            <div class="form-group">
                <label for="query">Enter SQL Query:</label>
                <textarea name="query" id="query" required></textarea>
            </div>
            <button type="submit">Run Query</button>
        </form>

        <?php if ($error): ?>
            <p class="error"><strong><?php echo $error; ?></strong></p>
        <?php endif; ?>

        <?php if ($result && $result instanceof mysqli_result): ?>
            <h3>Query Results:</h3>
            <div class="table-wrapper">
                <table>
                    <tr>
                        <?php while ($column = $result->fetch_field()): ?>
                            <th><?php echo $column->name; ?></th>
                        <?php endwhile; ?>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <?php foreach ($row as $value): ?>
                                <td><?php echo htmlspecialchars($value); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        <?php endif; ?>


        <a href="login.php">⬅ Back to Login</a>
    </div>
</body>

</html>