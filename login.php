<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="./src/output.css">
</head>

<body class="h-screen flex justify-center items-center bg-gray-100">
    <div class="flex flex-col items-center justify-center px-10 py-12 text-[14px] border border-gray-200 rounded-lg gap-5 shadow-2xl">
        <h2 class="text-[25px]">Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST" class="flex flex-col gap-3 ">
            <label class="text-[19px]">Username</label>
            <input type="text" class="border px-5 py-2 rounded-lg" name="username" required>

            <label class="text-[19px]">Password</label>
            <input type="password" class="border px-5 py-2 rounded-lg" name="password" required>

            <button type="submit" class="mt-5 border rounded-lg px-3 py-2 bg-black text-white transform transition-transform active:translate-y-1 cursor-pointer">Login</button>
        </form>
    </div>
</body>

</html>