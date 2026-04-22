<?php
include 'partials/header.php';
checkLogin();
checkRole(['super']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $username, $password, $role);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>User created successfully</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<h2>Create User</h2>
<form method="POST">
    <label>Name</label>
    <input type="text" name="name" required>

    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="text" name="password" required>

    <label>Role</label>
    <select name="role" required>
        <option value="admin">Admin</option>
        <option value="user">User</option>
    </select>

    <button type="submit">Create User</button>
</form>

<?php include 'partials/footer.php'; ?>