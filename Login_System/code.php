<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Ayet Login System</title>
<style>
body { font-family: Arial; background: #f4f4f4; }
.box { width: 350px; background: lightgray; padding: 20px; margin: 80px auto; border-radius: 5px; }
input { width: 95%; padding: 8px; margin-top: 10px; }
button { display: block; width: 50%; padding: 8px; margin: 15px auto; }
h2 { text-align: center;  }
</style>
</head>
<body>

<div class="box">
<h2>Login</h2>

<form method="post">
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = trim($_POST["username"]);
    $pass = hash("sha256", $_POST["password"]);

    if (file_exists("users.txt")) {
        $users = file("users.txt", FILE_IGNORE_NEW_LINES);
        foreach ($users as $line) {
            list($u, $p) = explode(":", $line);
            if ($u === $user && $p === $pass) {
                $_SESSION["user"] = $user;
                header("Location: dashboard.php");
                exit;
            }
        }
    }
    echo "<p style='color:red;'>Invalid login.</p>";
}
?>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="signup.php">Sign up</a></p>

</div>
</body>
</html>
