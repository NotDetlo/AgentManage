<?php
session_start();

// Set connection variables
$server = "127.0.0.1:3308";
$username = "root";
$password = "";
$database = "agent_manage";

// Create a database connection
$con = new mysqli($server, $username, $password, $database);

// Check for connection success
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $agent_id = $_POST['agent_id'];
    $input_password = $_POST['password'];

    $stmt = $con->prepare("SELECT agent_id, agent_name, password FROM agent_details WHERE agent_id = ?");
    $stmt->bind_param("i", $agent_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($agent_id_result, $agent_name, $db_password);
        $stmt->fetch();

        if (password_verify($input_password, $db_password)) {
            $_SESSION['agent_id'] = $agent_id;
            header("Location: index.php");
            exit;
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error = "Invalid Agent ID.";
    }

    $stmt->close();
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css?family=Fira+Sans" rel="stylesheet">
    <style>
        @import url("https://fonts.googleapis.com/css?family=Fira+Sans");

        html,
        body {
            position: relative;
            min-height: 100vh;
            background-color: #E1E8EE;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Fira Sans", Helvetica, Arial, sans-serif;
        }

        .form-structor {
            background-color: #222;
            border-radius: 15px;
            height: 550px;
            width: 350px;
            position: relative;
            overflow: hidden;
        }

        .form-structor::after {
            content: '';
            opacity: 0.8;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-repeat: no-repeat;
            background-position: left bottom;
            background-size: 500px;
            background-image: url('https://images.unsplash.com/photo-1503602642458-232111445657?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=bf884ad570b50659c5fa2dc2cfb20ecf&auto=format&fit=crop&w=1000&q=100');
        }

        .signup {
            position: absolute;
            top: 50%;
            left: 46%;
            transform: translate(-50%, -50%);
            width: 65%;
            z-index: 5;
            transition: all 0.3s ease;
        }

        .signup.slide-up {
            top: 5%;
            transform: translate(-50%, 0%);
        }

        .signup.slide-up .form-holder,
        .signup.slide-up .proceed-btn {
            opacity: 0;
            visibility: hidden;
        }

        .signup.slide-up .form-title {
            font-size: 1em;
            cursor: pointer;
        }

        .signup.slide-up .form-title span {
            margin-right: 5px;
            opacity: 1;
            visibility: visible;
        }

        .signup .form-title {
            color: #fff;
            font-size: 1.7em;
            text-align: center;
        }

        .signup .form-title span {
            color: rgba(0, 0, 0, 0.4);
            opacity: 0;
            visibility: hidden;
        }

        .signup .form-holder {
            margin-top: 50px;
        }

        .proceed-btn {
            background-color: rgba(0, 0, 0, 0.4);
            color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 25px;
            display: block;
            margin: 15px auto;
            padding: 15px 20px;
            width: 100%;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .proceed-btn:hover {
            background-color: rgba(0, 0, 0, 0.8);
            cursor: pointer;
        }

        .login {
            position: absolute;
            top: 20%;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #fff;
            z-index: 5;
            transition: all 0.3s ease;
        }

        .login::before {
            content: '';
            position: absolute;
            left: 50%;
            top: -20px;
            transform: translate(-50%, 0);
            background-color: #fff;
            width: 200%;
            height: 250px;
            border-radius: 50%;
            z-index: 4;
        }

        .login .center {
            position: absolute;
            top: calc(50% - 10%);
            left: 50%;
            transform: translate(-50%, -50%);
            width: 65%;
            z-index: 5;
            transition: all 0.3s ease;
        }

        .login .form-title {
            color: #000;
            font-size: 1.7em;
            text-align: center;
        }

        .login .form-title span {
            color: rgba(0, 0, 0, 0.4);
            opacity: 0;
            visibility: hidden;
        }

        .login .form-holder {
            border-radius: 15px;
            background-color: #fff;
            border: 1px solid #eee;
            overflow: hidden;
            margin-top: 50px;
        }

        .login .input {
            border: 0;
            outline: none;
            box-shadow: none;
            display: block;
            height: 30px;
            line-height: 30px;
            padding: 8px 15px;
            border-bottom: 1px solid #eee;
            width: 100%;
            font-size: 12px;
        }

        .login .input:last-child {
            border-bottom: 0;
        }

        .login .input::placeholder {
            color: rgba(0, 0, 0, 0.4);
        }

        .login .submit-btn {
            background-color: #6B92A4;
            color: rgba(255, 255, 255, 0.9);
            border: 0;
            border-radius: 15px;
            display: block;
            margin: 15px auto;
            padding: 15px 45px;
            width: 100%;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login .submit-btn:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .login.slide-up {
            top: 90%;
        }

        .login.slide-up .center {
            top: 10%;
            transform: translate(-50%, 0%);
        }

        .login.slide-up .form-holder,
        .login.slide-up .submit-btn {
            opacity: 0;
            visibility: hidden;
        }

        .login.slide-up .form-title {
            font-size: 1em;
            margin: 0;
            padding: 0;
            cursor: pointer;
        }

        .login.slide-up .form-title span {
            margin-right: 5px;
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>

<body>
    <div class="form-structor">
        <!-- Sign Up Section (OFFICER/ADMIN) -->
        <div class="signup">
            <h2 class="form-title" id="signup"><span>or</span>Real Estate .Co</h2>
            <div class="form-holder">
                <a href="reports.php" class="proceed-btn">Reports</a>
                <a href="admin_panel.php" class="proceed-btn">Admin</a>
            </div>
        </div>

        <!-- Login Section (Agent Login) -->
        <div class="login slide-up">
            <div class="center">
                <h2 class="form-title" id="login"><span>or</span>Agent Login</h2>
                <?php if (isset($error) && $error): ?>
                    <p style="color: red; font-size: 0.9em; text-align: center;"><?php echo $error; ?></p>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="form-holder">
                        <input type="text" class="input" name="agent_id" placeholder="Enter Agent ID" required />
                        <input type="password" class="input" name="password" placeholder="Enter Password" required />
                    </div>
                    <button class="submit-btn" type="submit">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const loginBtn = document.getElementById('login');
        const signupBtn = document.getElementById('signup');

        loginBtn.addEventListener('click', (e) => {
            let parent = e.target.closest('.login');
            if (!parent.classList.contains("slide-up")) {
                parent.classList.add('slide-up');
            } else {
                signupBtn.closest('.signup').classList.add('slide-up');
                parent.classList.remove('slide-up');
            }
        });

        signupBtn.addEventListener('click', (e) => {
            let parent = e.target.closest('.signup');
            if (!parent.classList.contains("slide-up")) {
                parent.classList.add('slide-up');
            } else {
                loginBtn.closest('.login').classList.add('slide-up');
                parent.classList.remove('slide-up');
            }
        });
    </script>
</body>

</html>