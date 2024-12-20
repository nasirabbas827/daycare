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

// Fetch activity details
if (isset($_GET['activity_id'])) {
    $activity_id = $_GET['activity_id'];

    // Fetch activity details from the database
    $sql_activity = "SELECT * FROM Activities WHERE activity_id = ?";
    $stmt_activity = $conn->prepare($sql_activity);
    $stmt_activity->bind_param("i", $activity_id);
    $stmt_activity->execute();
    $activity_result = $stmt_activity->get_result();

    if ($activity_result->num_rows == 0) {
        header("Location: activity_log.php?child_id=" . $_GET['child_id']);
        exit;
    }

    $activity = $activity_result->fetch_assoc();
} else {
    header("Location: activity_log.php?child_id=" . $_GET['child_id']);
    exit;
}

// Handle updating activity log
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_activity'])) {
    $activity_date = $_POST['activity_date'];
    $activity_description = $_POST['activity_description'];
    $playtime = $_POST['playtime'];
    $learning_lesson = $_POST['learning_lesson'];
    $meals = $_POST['meals'];
    $naps = $_POST['naps'];
    $other_activities = $_POST['other_activities'];

    // Handle file upload for photos/videos
    $photos_videos = $activity['photos_videos']; // Keep the old file if no new one is uploaded
    if (!empty($_FILES['photos_videos']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photos_videos"]["name"]);
        if (move_uploaded_file($_FILES["photos_videos"]["tmp_name"], $target_file)) {
            $photos_videos = $target_file;
        }
    }

    // Update activity record in the database
    $sql_update = "UPDATE Activities SET activity_date = ?, activity_description = ?, photos_videos = ?, playtime = ?, learning_lesson = ?, meals = ?, naps = ?, other_activities = ? WHERE activity_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssssssi", $activity_date, $activity_description, $photos_videos, $playtime, $learning_lesson, $meals, $naps, $other_activities, $activity_id);

    if ($stmt_update->execute()) {
        $success_message = "Activity log updated successfully.";
    } else {
        $error_message = "Failed to update activity log.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Activity Log</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Edit Activity Log for <?php echo htmlspecialchars($activity['activity_date']); ?></h2>

    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>

    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>

    <!-- Edit Activity Log Form in a Card -->
    <div class="card mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="activity_date">Activity Date</label>
                    <input type="date" class="form-control" name="activity_date" value="<?php echo htmlspecialchars($activity['activity_date']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="activity_description">Activity Description</label>
                    <textarea class="form-control" name="activity_description" rows="4" required><?php echo htmlspecialchars($activity['activity_description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="photos_videos">Upload Photos/Videos</label>
                    <input type="file" class="form-control" name="photos_videos">
                    <?php if ($activity['photos_videos']) { ?>
                        <p>Current File: <a href="<?php echo htmlspecialchars($activity['photos_videos']); ?>" target="_blank">View</a></p>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <label for="playtime">Playtime</label>
                    <textarea class="form-control" name="playtime" rows="2"><?php echo htmlspecialchars($activity['playtime']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="learning_lesson">Learning Lesson</label>
                    <textarea class="form-control" name="learning_lesson" rows="2"><?php echo htmlspecialchars($activity['learning_lesson']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="meals">Meals</label>
                    <textarea class="form-control" name="meals" rows="2"><?php echo htmlspecialchars($activity['meals']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="naps">Naps</label>
                    <textarea class="form-control" name="naps" rows="2"><?php echo htmlspecialchars($activity['naps']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="other_activities">Other Activities</label>
                    <textarea class="form-control" name="other_activities" rows="2"><?php echo htmlspecialchars($activity['other_activities']); ?></textarea>
                </div>
                <button type="submit" name="update_activity" class="btn btn-primary">Update Activity</button>
                <a href="activity_log.php?child_id=<?php echo $activity['child_id']; ?>" class="btn btn-outline-dark">Back</a>
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
