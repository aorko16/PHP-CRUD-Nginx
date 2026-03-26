<?php
$host = "db";
$user = "user1";
$pass = "pass123";
$db   = "mydb";

$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) die("Connection failed: " . $mysqli->connect_error);

// Create
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mysqli->query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mysqli->query("UPDATE users SET name='$name', email='$email' WHERE id=$id");
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $mysqli->query("DELETE FROM users WHERE id=$id");
}

// Fetch all users
$result = $mysqli->query("SELECT * FROM users");
?>

<h2>PHP CRUD Application</h2>

<?php if(isset($_GET['edit'])):
$id = $_GET['edit'];
$row = $mysqli->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
?>
<form method="post">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    Name: <input type="text" name="name" value="<?= $row['name'] ?>" required>
    Email: <input type="email" name="email" value="<?= $row['email'] ?>" required>
    <input type="submit" name="update" value="Update User">
</form>
<a href="index.php">Back</a>
<?php else: ?>
<form method="post">
    Name: <input type="text" name="name" required>
    Email: <input type="email" name="email" required>
    <input type="submit" name="add" value="Add User">
</form>
<?php endif; ?>

<table border="1" cellpadding="5" cellspacing="0">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['email'] ?></td>
    <td>
        <a href="?edit=<?= $row['id'] ?>">Edit</a>
        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
