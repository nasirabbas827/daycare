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

// Get meal_id from the URL
$meal_id = $_GET['meal_id'];

// Fetch meal schedule details
$sql_meal = "SELECT * FROM Meal_Schedule WHERE meal_id = ?";
$stmt_meal = $conn->prepare($sql_meal);
$stmt_meal->bind_param("i", $meal_id);
$stmt_meal->execute();
$meal_result = $stmt_meal->get_result();
$meal = $meal_result->fetch_assoc();

// Handle updating the meal schedule
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_meal'])) {
    $date = $_POST['date'];
    $meal_type = $_POST['meal_type'];
    $meal_time = $_POST['meal_time'];
    $meal_details = $_POST['meal_details'];

    // Update meal schedule in the database
    $sql_update = "UPDATE Meal_Schedule SET date = ?, meal_type = ?, meal_time = ?, meal_details = ? WHERE meal_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $date, $meal_type, $meal_time, $meal_details, $meal_id);

    if ($stmt_update->execute()) {
        $success_message = "Meal schedule updated successfully.";
    } else {
        $error_message = "Failed to update meal schedule.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Meal Schedule</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Edit Meal Schedule</h2>

    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>

    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>

    <!-- Edit Meal Schedule Form -->
    <div class="card mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($meal['date']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="meal_type">Meal Type</label>
                    <select class="form-control" name="meal_type" required>
                        <option value="daily" <?php if ($meal['meal_type'] == 'daily') echo 'selected'; ?>>Daily</option>
                        <option value="weekly" <?php if ($meal['meal_type'] == 'weekly') echo 'selected'; ?>>Weekly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="meal_time">Meal Time</label>
                    <select class="form-control" name="meal_time" required>
                        <option value="Breakfast" <?php if ($meal['meal_time'] == 'Breakfast') echo 'selected'; ?>>Breakfast</option>
                        <option value="Lunch" <?php if ($meal['meal_time'] == 'Lunch') echo 'selected'; ?>>Lunch</option>
                        <option value="Snack" <?php if ($meal['meal_time'] == 'Snack') echo 'selected'; ?>>Snack</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="meal_details">Meal Details</label>
                    <textarea class="form-control" name="meal_details" rows="4" required><?php echo htmlspecialchars($meal['meal_details']); ?></textarea>
                </div>
                <button type="submit" name="update_meal" class="btn btn-primary">Update Meal Schedule</button>
                <a href="meal_schedule.php" class="btn btn-outline-dark">Back</a>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS & dependencies -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
