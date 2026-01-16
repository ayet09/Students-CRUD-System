<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: code.php");
    exit;
}

$page = $_GET["page"] ?? "students";
$file = "students.txt";
$error = "";

$students = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

if (isset($_POST["add"])) {
    $name = trim($_POST['name']);
    $course = trim($_POST['course']);
    $year = trim($_POST['year']);

    foreach ($students as $line) {
        list($_, $existingName) = explode("|", $line);
        if (strtolower(trim($existingName)) === strtolower($name)) {
            $error = "Student already exists.";
            break;
        }
    }

    if (!$error) {
        do {
            $id = str_pad(rand(0, 99999), 5, "0", STR_PAD_LEFT);
            $exists = false;
            foreach ($students as $line) {
                list($existingId) = explode("|", $line);
                if ($existingId == $id) { $exists = true; break; }
            }
        } while ($exists);

        $data = "$id|$name|$course|$year\n";
        if (file_put_contents($file, $data, FILE_APPEND | LOCK_EX) !== false) {
            header("Location: dashboard.php?page=students");
            exit;
        } else {
            $error = "Failed to save student. Try again.";
        }
    }
}

if (isset($_POST["update"])) {
    $new = [];
    foreach ($students as $line) {
        list($id,$n,$c,$y) = explode("|", $line);
        if ($id == $_POST["id"]) {
            $line = "$id|{$_POST['name']}|{$_POST['course']}|{$_POST['year']}";
        }
        $new[] = $line;
    }
    file_put_contents($file, implode("\n", $new)."\n");
    header("Location: dashboard.php?page=students");
    exit;
}

if (isset($_GET["delete"])) {
    $new = [];
    foreach ($students as $line) {
        list($id) = explode("|", $line);
        if ($id != $_GET["delete"]) $new[] = $line;
    }
    file_put_contents($file, implode("\n", $new).($new? "\n" : ""));
    header("Location: dashboard.php?page=students");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>CRUD Activity</title>
<style>
body { margin:0; font-family: Arial; background: #e1dcdcff; }
.wrapper { display:flex; height:100vh; }
.sidebar { width:220px; background: #1e293b; color:white; padding:20px; }
.sidebar a { display:block; padding:10px; margin:8px 0; background: #334155; color:white; text-decoration:none; border-radius:5px; }
.sidebar a:hover { background: #475569; }
.content { flex:1; padding:25px; }

.profile { background: #475569; padding:15px; border-radius:8px; display:flex; align-items:center; margin-bottom:20px; }
.profile img { width:55px; height:55px; border-radius:50%; margin-right:15px; }

.card { background: #ece8e8ff; padding:20px; border-radius:8px; }
input, select { padding:8px; margin:5px; }
button { padding:8px 14px; background: #6788d0ff; color:white; border:none; border-radius:5px; cursor:pointer; }
button:hover { background: #6788d0ff; }

table { width:100%; border-collapse:collapse; margin-top:15px; }
th { background: #6788d0ff; color:white; padding:8px; }
td { padding:8px; border:1px solid #ddd; text-align:center; }

.delete { color:red; text-decoration:none; }
.error { color:red; font-weight:bold; }
</style>
</head>

<body>
<div class="wrapper">

<div class="sidebar">
<div class="profile">
<img src="https://i.imgur.com/6VBx3io.png">
<div>
<strong><?php echo htmlspecialchars($_SESSION["user"]); ?></strong><br>
Admin
</div>
</div>
<a href="dashboard.php?page=add">Add Student</a>
<a href="dashboard.php?page=students">View Students</a>
<a href="logout.php">Logout</a>
</div>

<div class="content">
<div class="card">

<?php if ($page === "add"): ?>
<h3>Add Student</h3>
<?php if ($error) echo "<p class='error'>$error</p>"; ?>

<form method="post" action="dashboard.php?page=add">
<input name="name" placeholder="Student Name" required>

<select name="course" required>
<option value="">Select Course</option>
<option>BSIT</option>
<option>BSBA</option>
<option>BSHM</option>
<option>BSED</option>
</select>

<select name="year" required>
<option value="">Year Level</option>
<option>1</option>
<option>2</option>
<option>3</option>
<option>4</option>
</select>

<br><br>
<button name="add">Add Student</button>
</form>

<?php else: ?>
<h3>Student Records</h3>
<table>
<tr>
<th>#</th>
<th>ID #</th>
<th>Name</th>
<th>Course</th>
<th>Year</th>
<th></th>
</tr>

<?php
$counter = 1;
foreach ($students as $line) {
    list($id,$name,$course,$year) = explode("|", $line);
    echo "
    <tr>
    <form method='post' action='dashboard.php?page=students'>
        <td>{$counter}</td>
        <td>$id</td>
        <td><input name='name' value='$name'></td>
        <td>
            <select name='course'>
                <option ".($course=="BSIT"?"selected":"").">BSIT</option>
                <option ".($course=="BSBA"?"selected":"").">BSBA</option>
                <option ".($course=="BSHM"?"selected":"").">BSHM</option>
                <option ".($course=="BSED"?"selected":"").">BSED</option>                
            </select>
        </td>
        <td>
            <select name='year'>
                <option ".($year=="1"?"selected":"").">1</option>
                <option ".($year=="2"?"selected":"").">2</option>
                <option ".($year=="3"?"selected":"").">3</option>
                <option ".($year=="4"?"selected":"").">4</option>
            </select>
        </td>
        <td>
            <input type='hidden' name='id' value='$id'>
            <button name='update'>Update</button>
            <a class='delete' href='dashboard.php?page=students&delete=$id' onclick=\"return confirm('Are you sure you want to delete student $name?');\">Delete</a>
        </td>
    </form>
    </tr>";
    $counter++;
}
?>
</table>
<?php endif; ?>

</div>
</div>
</div>
</body>
</html>
