<?php
session_start();
include('config.php');

// Check if the admin is logged in
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['staff_id'])) {
    $staff_id = $_GET['staff_id'];

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $password = $_POST['password'];
        $role = $_POST['role'];  // Role added

        $sql_update = "UPDATE staff SET name = ?, email = ?, phone = ?, address = ?, password = ?, role = ? WHERE staff_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssssi", $name, $email, $phone, $address, $password, $role, $staff_id);
        $stmt_update->execute();
        $stmt_update->close();

        header("Location: view_staff.php");
        exit;
    }

    // Fetch staff data
    $sql_fetch = "SELECT * FROM staff WHERE staff_id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $staff_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    if ($result->num_rows === 1) {
        $staff = $result->fetch_assoc();
    }
    $stmt_fetch->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>
<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h2 class="text-center mb-4">Edit Staff</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $staff['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $staff['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $staff['phone']; ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea class="form-control" id="address" name="address"><?php echo $staff['address']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" value="<?php echo $staff['password']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="staff" <?php echo $staff['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                        <option value="carer" <?php echo $staff['role'] == 'carer' ? 'selected' : ''; ?>>Carer</option>
                        <option value="manager" <?php echo $staff['role'] == 'manager' ? 'selected' : ''; ?>>Manager</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Update Staff</button>
                    <a href="view_staff.php" class="btn btn-outline-dark">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
