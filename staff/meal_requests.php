<?php
include('config.php');
session_start();

// Check if staff is logged in
if (!isset($_SESSION["staff_id"]) || empty($_SESSION["staff_id"])) {
    header("location: ../staff_login.php");
    exit;
}

// Fetch staff details
$staff_id = $_SESSION["staff_id"];
$sql_staff = "SELECT * FROM staff WHERE staff_id = ?";
$stmt_staff = $conn->prepare($sql_staff);
$stmt_staff->bind_param("i", $staff_id);
$stmt_staff->execute();
$result_staff = $stmt_staff->get_result();
$staff = $result_staff->fetch_assoc();

// Fetch meal requests
$sql_requests = "SELECT * FROM meal_requests JOIN children ON meal_requests.child_id = children.child_id ORDER BY request_date DESC";
$stmt_requests = $conn->prepare($sql_requests);
$stmt_requests->execute();
$requests_result = $stmt_requests->get_result();

// Handle status update for a meal request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    $sql_update = "UPDATE meal_requests SET status = ? WHERE request_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $status, $request_id);
    
    if ($stmt_update->execute()) {
        $success_message = "Meal request status updated successfully!";
    } else {
        $error_message = "Failed to update the status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Meal Requests</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center">Meal Requests</h2>

        <!-- Display Success or Error Messages -->
        <?php if (!empty($success_message)) { ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php } elseif (!empty($error_message)) { ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Child Name</th>
                    <th>Meal Type</th>
                    <th>Meal Time</th>
                    <th>Meal Details</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $requests_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['meal_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['meal_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['meal_details']); ?></td>
                        <td><?php echo htmlspecialchars($row['request_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                <select name="status" class="form-control" required>
                                    <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Approved" <?php echo ($row['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="Rejected" <?php echo ($row['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-primary mt-2">Update Status</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="staff_dashboard.php" class="btn btn-outline-dark mt-3">Back to Dashboard</a>
    </div>

    <!-- Optional: Add Bootstrap JS & dependencies if needed -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
