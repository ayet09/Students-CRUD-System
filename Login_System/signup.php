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
<h2>Sign Up</h2>

<form method="post">
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = trim($_POST["username"]);
    $pass = hash("sha256", $_POST["password"]);

    file_put_contents("users.txt", "$user:$pass\n", FILE_APPEND);
    echo "<p style='color:green;'>Registered successfully.</p>";
}
?>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Register</button>
</form>

<p><a href="code.php">Back to login</a></p>


</div>
</body>
</html>
