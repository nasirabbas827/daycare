<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle adding a new notification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_notification'])) {
    $message = $_POST['message'];
    $notification_type = $_POST['notification_type'];

    // Insert notification record into the database
    $sql_insert = "INSERT INTO notifications (message, notification_type) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ss", $message, $notification_type);

    if ($stmt_insert->execute()) {
        $success_message = "Notification added successfully.";
    } else {
        $error_message = "Failed to add notification.";
    }
}

// Handle deleting a notification
if (isset($_GET['delete_id'])) {
    $notification_id = $_GET['delete_id'];

    // Delete the notification record from the database
    $sql_delete = "DELETE FROM notifications WHERE notification_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $notification_id);

    if ($stmt_delete->execute()) {
        $success_message = "Notification deleted successfully.";
    } else {
        $error_message = "Failed to delete notification.";
    }
}

// Fetch all notifications
$sql_notifications = "SELECT * FROM notifications ORDER BY created_at DESC";
$stmt_notifications = $conn->prepare($sql_notifications);
$stmt_notifications->execute();
$notifications_result = $stmt_notifications->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Notifications</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">

    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>

    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>

    <!-- Add Notification Form in a Card -->
    <div class="card mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST">
    <h2 class="text-center">Manage Notifications</h2>

            <div class="form-group">
                    <label for="notification_type">Notification Type</label>
                    <input type="text" class="form-control" name="notification_type" required>
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea class="form-control" name="message" rows="4" required></textarea>
                </div>

                <button type="submit" name="add_notification" class="btn btn-primary">Add Notification</button>
            </form>
        </div>
    </div>

    <!-- Display Notifications -->
    <h4 class="mt-5">All Notifications</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Message</th>
                <th>Type</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($notification = $notifications_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($notification['message']); ?></td>
                    <td><?php echo htmlspecialchars($notification['notification_type']); ?></td>
                    <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                    <td>
                        <a href="?delete_id=<?php echo $notification['notification_id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this notification?');">Delete</a>
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
