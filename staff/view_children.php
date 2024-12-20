<?php
include('config.php');
session_start();

// Check if the staff is logged in
if (!isset($_SESSION["staff_id"]) || empty($_SESSION["staff_id"])) {
    header("location: ../staff_login.php");
    exit;
}

// Fetch staff details
$staff_id = $_SESSION["staff_id"];
$sql = "SELECT * FROM staff WHERE staff_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();

// Fetch all children assigned to this staff
$sql_children = "SELECT * FROM Children";
$stmt_children = $conn->prepare($sql_children);
$stmt_children->execute();
$children_result = $stmt_children->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Children - Mark Attendance</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Children List</h2>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Child Name</th>
                <th>Date of Birth</th>
                <th>Age Group</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $children_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                    <td><?php echo htmlspecialchars($row['age_group']); ?></td>
                    <td>
                        <!-- Add Attendance Button -->
                        <a href="mark_attendance.php?child_id=<?php echo $row['child_id']; ?>" class="btn btn-info btn-sm">Mark Attendance</a>
                        <a href="activity_log.php?child_id=<?php echo $row['child_id']; ?>" class="btn btn-info btn-sm">View Activity Log</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS & dependencies -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
