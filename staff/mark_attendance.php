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

// Fetch child details
$child_id = $_GET['child_id'];
$sql_child = "SELECT * FROM Children WHERE child_id = ?";
$stmt_child = $conn->prepare($sql_child);
$stmt_child->bind_param("i", $child_id);
$stmt_child->execute();
$child_result = $stmt_child->get_result();
$child = $child_result->fetch_assoc();

// Handle marking attendance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mark_attendance'])) {
    $check_in_time = $_POST['check_in_time'];
    $check_out_time = $_POST['check_out_time'];
    $date = $_POST['date'];

    // Insert attendance record
    $sql_insert = "INSERT INTO Attendance (child_id, check_in_time, check_out_time, date, staff_id) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("isssi", $child_id, $check_in_time, $check_out_time, $date, $staff_id);

    if ($stmt_insert->execute()) {
        // Add attendance notification
        $message = "Attendance marked for " . $child['name'] . " on " . $date . " from " . $check_in_time . " to " . $check_out_time;
        $sql_notify = "INSERT INTO Attendance_Notifications (child_id, message, notification_time) VALUES (?, ?, NOW())";
        $stmt_notify = $conn->prepare($sql_notify);
        $stmt_notify->bind_param("is", $child_id, $message);
        $stmt_notify->execute();

        $success_message = "Attendance added successfully and notification created.";
    } else {
        $error_message = "Failed to add attendance. Please try again.";
    }
}

// Fetch attendance records for this child
$sql_attendance = "SELECT * FROM Attendance WHERE child_id = ?";
$stmt_attendance = $conn->prepare($sql_attendance);
$stmt_attendance->bind_param("i", $child_id);
$stmt_attendance->execute();
$attendance_result = $stmt_attendance->get_result();

// Handle deleting attendance
if (isset($_GET['delete_id'])) {
    $attendance_id = $_GET['delete_id'];

    $sql_delete = "DELETE FROM Attendance WHERE attendance_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $attendance_id);

    if ($stmt_delete->execute()) {
        $success_message = "Attendance record deleted successfully.";
    } else {
        $error_message = "Failed to delete the attendance record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mark Attendance for <?php echo htmlspecialchars($child['name']); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Mark Attendance for <?php echo htmlspecialchars($child['name']); ?></h2>

    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>

    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>
    <div class="card mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
    <!-- Form for Marking Attendance -->
    <form method="POST">
        <div class="form-group">
            <label for="check_in_time">Check-in Time</label>
            <input type="datetime-local" class="form-control" name="check_in_time" required>
        </div>
        <div class="form-group">
            <label for="check_out_time">Check-out Time</label>
            <input type="datetime-local" class="form-control" name="check_out_time" required>
        </div>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" required>
        </div>
        <button type="submit" name="mark_attendance" class="btn btn-primary">Mark Attendance</button>
    </form>
    </div>
    </div>


    <!-- Display Attendance Records -->
    <h4 class="mt-5">Attendance Records</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Check-in Time</th>
                <th>Check-out Time</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($attendance = $attendance_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($attendance['check_in_time']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['check_out_time']); ?></td>
                    <td><?php echo htmlspecialchars($attendance['date']); ?></td>
                    <td>
                        <a href="?delete_id=<?php echo $attendance['attendance_id']; ?>" class="btn btn-danger btn-sm" 
                        onclick="return confirm('Are you sure you want to delete this attendance record?');">Delete</a>
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
