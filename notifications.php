<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from session
$user_id = $_SESSION["id"];

// Fetch all children for the user
$sql_children = "SELECT child_id FROM children WHERE parent_id = ?";
$stmt_children = $conn->prepare($sql_children);
$stmt_children->bind_param("i", $user_id);
$stmt_children->execute();
$children_result = $stmt_children->get_result();

// Fetch all attendance notifications for each child
$attendance_notifications = [];
while ($child = $children_result->fetch_assoc()) {
    $child_id = $child['child_id'];
    
    $sql_attendance = "SELECT * FROM attendance_notifications WHERE child_id = ? ORDER BY notification_time DESC";
    $stmt_attendance = $conn->prepare($sql_attendance);
    $stmt_attendance->bind_param("i", $child_id);
    $stmt_attendance->execute();
    $attendance_notifications[$child_id] = $stmt_attendance->get_result();
}

// Fetch general notifications
$sql_notifications = "SELECT * FROM notifications ORDER BY created_at DESC";
$stmt_notifications = $conn->prepare($sql_notifications);
$stmt_notifications->execute();
$general_notifications = $stmt_notifications->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Notifications</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Notifications</h2>
        <div class="row">
            <div class="col-md-6">
                <h3>Attendance Notifications</h3>
                <?php
                if (!empty($attendance_notifications)) {
                    foreach ($attendance_notifications as $child_id => $attendance_result) {
                        echo "<h5>Child #$child_id</h5>";
                        while ($attendance = $attendance_result->fetch_assoc()) {
                            echo "<div class='card mb-3'>
                                    <div class='card-body'>
                                        <p><strong>Message:</strong> " . htmlspecialchars($attendance['message']) . "</p>
                                        <p><strong>Time:</strong> " . htmlspecialchars($attendance['notification_time']) . "</p>
                                    </div>
                                  </div>";
                        }
                    }
                } else {
                    echo "<p>No attendance notifications available.</p>";
                }
                ?>
            </div>
            <div class="col-md-6">
                <h3>Other Notifications</h3>
                <?php
                if ($general_notifications->num_rows > 0) {
                    while ($notification = $general_notifications->fetch_assoc()) {
                        echo "<div class='card mb-3'>
                                <div class='card-body'>
                                    <p><strong>Message:</strong> " . htmlspecialchars($notification['message']) . "</p>
                                    <p><strong>Type:</strong> " . htmlspecialchars($notification['notification_type']) . "</p>
                                    <p><strong>Created At:</strong> " . htmlspecialchars($notification['created_at']) . "</p>
                                </div>
                              </div>";
                    }
                } else {
                    echo "<p>No other notifications available.</p>";
                }
                ?>
            </div>
        </div>
        <a href="home.php" class="btn btn-outline-dark">Back</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
