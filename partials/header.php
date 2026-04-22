<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <title>PO Manager</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'super'): ?>
            <a href="create_user.php">Create User</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="create_po.php">Create PO</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['super', 'admin'])): ?>
            <a href="pending_items.php">Pending Items</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Logged in as: <?php echo $_SESSION['name']; ?> (<?php echo $_SESSION['role']; ?>)</span>

            <a href="logout.php" style="float:right;">Logout</a>
        <?php endif; ?>
    </div>
    <div class="container">