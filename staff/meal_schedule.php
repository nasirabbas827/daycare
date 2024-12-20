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

// Handle adding a new meal schedule
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_meal'])) {
    $date = $_POST['date'];
    $meal_type = $_POST['meal_type'];
    $meal_time = $_POST['meal_time'];
    $meal_details = $_POST['meal_details'];

    // Insert meal schedule record into the database
    $sql_insert = "INSERT INTO Meal_Schedule (date, meal_type, meal_time, meal_details, created_by)
                   VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssssi", $date, $meal_type, $meal_time, $meal_details, $staff_id);

    if ($stmt_insert->execute()) {
        $success_message = "Meal schedule added successfully.";
    } else {
        $error_message = "Failed to add meal schedule.";
    }
}

// Handle deletion of a meal schedule
if (isset($_GET['delete_id'])) {
    $meal_id = $_GET['delete_id'];

    // Delete the meal schedule from the database
    $sql_delete = "DELETE FROM Meal_Schedule WHERE meal_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $meal_id);

    if ($stmt_delete->execute()) {
        $success_message = "Meal schedule deleted successfully.";
    } else {
        $error_message = "Failed to delete meal schedule.";
    }
}

// Fetch all meal schedules
$sql_meals = "SELECT * FROM Meal_Schedule ORDER BY date DESC";
$stmt_meals = $conn->prepare($sql_meals);
$stmt_meals->execute();
$meals_result = $stmt_meals->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Meal Schedule</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Meal Schedule</h2>

    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>

    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>

    <!-- Add Meal Schedule Form in a Card -->
    <div class="card mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" name="date" required>
                </div>
                <div class="form-group">
                    <label for="meal_type">Meal Type</label>
                    <select class="form-control" name="meal_type" required>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="meal_time">Meal Time</label>
                    <select class="form-control" name="meal_time" required>
                        <option value="Breakfast">Breakfast</option>
                        <option value="Lunch">Lunch</option>
                        <option value="Snack">Snack</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="meal_details">Meal Details</label>
                    <textarea class="form-control" name="meal_details" rows="4" required></textarea>
                </div>
                <button type="submit" name="add_meal" class="btn btn-primary">Add Meal Schedule</button>
            </form>
        </div>
    </div>

    <!-- Display Meal Schedules -->
    <h4 class="mt-5">Scheduled Meals</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Meal Type</th>
                <th>Meal Time</th>
                <th>Meal Details</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($meal = $meals_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($meal['date']); ?></td>
                    <td><?php echo htmlspecialchars($meal['meal_type']); ?></td>
                    <td><?php echo htmlspecialchars($meal['meal_time']); ?></td>
                    <td><?php echo htmlspecialchars($meal['meal_details']); ?></td>
                    <td>
                        <a href="edit_meal.php?meal_id=<?php echo $meal['meal_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete_id=<?php echo $meal['meal_id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this meal schedule?');">Delete</a>
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
