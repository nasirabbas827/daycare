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
$sql_children = "SELECT child_id, name FROM children WHERE parent_id = ?";
$stmt_children = $conn->prepare($sql_children);
$stmt_children->bind_param("i", $user_id);
$stmt_children->execute();
$children_result = $stmt_children->get_result();

// Fetch meal schedule
$sql_meals = "SELECT * FROM meal_schedule ORDER BY date DESC";
$stmt_meals = $conn->prepare($sql_meals);
$stmt_meals->execute();
$meal_schedule = $stmt_meals->get_result();

// Handle new meal request form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_meal_request'])) {
    $child_id = $_POST['child_id'];
    $meal_type = $_POST['meal_type'];
    $meal_time = $_POST['meal_time'];
    $meal_details = $_POST['meal_details'];

    $sql_insert_request = "INSERT INTO meal_requests (child_id, meal_type, meal_time, meal_details, request_date) 
                           VALUES (?, ?, ?, ?, NOW())";
    $stmt_insert = $conn->prepare($sql_insert_request);
    $stmt_insert->bind_param("isss", $child_id, $meal_type, $meal_time, $meal_details);
    if ($stmt_insert->execute()) {
        $success_message = "Meal request submitted successfully!";
    } else {
        $error_message = "Failed to submit the meal request.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Meal Schedule</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Meal Schedule</h2>
        
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
        <button class="btn btn-primary float-right mb-3" data-toggle="modal" data-target="#mealRequestModal">Request New Meal</button>

            <thead>
                <tr>
                    <th>Date</th>
                    <th>Meal Type</th>
                    <th>Meal Time</th>
                    <th>Meal Details</th>
                    <th>Created By</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $meal_schedule->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                        <td><?php echo htmlspecialchars($row['meal_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['meal_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['meal_details']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_by']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>


        <a href="home.php" class="btn btn-outline-dark mt-3">Back</a>
    </div>

    <!-- Modal for Meal Request -->
    <div class="modal" id="mealRequestModal" tabindex="-1" role="dialog" aria-labelledby="mealRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mealRequestModalLabel">Request New Meal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="child_id">Select Child</label>
                            <select class="form-control" id="child_id" name="child_id" required>
                                <?php while ($child = $children_result->fetch_assoc()) { ?>
                                    <option value="<?php echo $child['child_id']; ?>"><?php echo $child['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="meal_type">Meal Type</label>
                            <input type="text" class="form-control" id="meal_type" name="meal_type" placeholder="Enter meal type" required>
                        </div>
                        <div class="form-group">
                            <label for="meal_time">Meal Time</label>
                            <input type="time" class="form-control" id="meal_time" name="meal_time" required>
                        </div>
                        <div class="form-group">
                            <label for="meal_details">Meal Details</label>
                            <textarea class="form-control" id="meal_details" name="meal_details" rows="3" placeholder="Enter meal details" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="submit_meal_request" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
