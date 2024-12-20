<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Get child_id from URL
if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];
} else {
    header("location: home.php");
    exit;
}

// Fetch attendance records for the specific child
$sql = "SELECT * FROM attendance WHERE child_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $child_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch child data for display
$child_sql = "SELECT * FROM children WHERE child_id = ?";
$child_stmt = $conn->prepare($child_sql);
$child_stmt->bind_param("i", $child_id);
$child_stmt->execute();
$child_data = $child_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Attendance</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Attendance Records for <?php echo htmlspecialchars($child_data['name']); ?></h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check-in Time</th>
                    <th>Check-out Time</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['check_in_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['check_out_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['staff_id']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="home.php" class="btn btn-outline-dark">Back</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
