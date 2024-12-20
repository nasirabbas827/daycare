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

// Handle adding a new activity log
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_activity'])) {
    $activity_date = $_POST['activity_date'];
    $activity_description = $_POST['activity_description'];
    $playtime = $_POST['playtime'];
    $learning_lesson = $_POST['learning_lesson'];
    $meals = $_POST['meals'];
    $naps = $_POST['naps'];
    $other_activities = $_POST['other_activities'];

    // Handle file upload for photos/videos
    $photos_videos = "";
    if (!empty($_FILES['photos_videos']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photos_videos"]["name"]);
        if (move_uploaded_file($_FILES["photos_videos"]["tmp_name"], $target_file)) {
            $photos_videos = $target_file;
        }
    }

    // Insert activity record into the database
    $sql_insert = "INSERT INTO Activities (child_id, activity_date, activity_description, photos_videos, playtime, learning_lesson, meals, naps, other_activities, logged_by)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("issssssssi", $child_id, $activity_date, $activity_description, $photos_videos, $playtime, $learning_lesson, $meals, $naps, $other_activities, $staff_id);

    if ($stmt_insert->execute()) {
        $success_message = "Activity log added successfully.";
    } else {
        $error_message = "Failed to add activity log.";
    }
}

// Fetch all activity logs for this child
$sql_activities = "SELECT * FROM Activities WHERE child_id = ?";
$stmt_activities = $conn->prepare($sql_activities);
$stmt_activities->bind_param("i", $child_id);
$stmt_activities->execute();
$activities_result = $stmt_activities->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Activity Log for <?php echo htmlspecialchars($child['name']); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Activity Log for <?php echo htmlspecialchars($child['name']); ?></h2>

    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php } ?>

    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php } ?>

    <!-- Add Activity Log Form in a Card -->
    <div class="card mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="activity_date">Activity Date</label>
                    <input type="date" class="form-control" name="activity_date" required>
                </div>
                <div class="form-group">
                    <label for="activity_description">Activity Description</label>
                    <textarea class="form-control" name="activity_description" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="photos_videos">Upload Photos/Videos</label>
                    <input type="file" class="form-control" name="photos_videos">
                </div>
                <div class="form-group">
                    <label for="playtime">Playtime</label>
                    <textarea class="form-control" name="playtime" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="learning_lesson">Learning Lesson</label>
                    <textarea class="form-control" name="learning_lesson" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="meals">Meals</label>
                    <textarea class="form-control" name="meals" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="naps">Naps</label>
                    <textarea class="form-control" name="naps" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label for="other_activities">Other Activities</label>
                    <textarea class="form-control" name="other_activities" rows="2"></textarea>
                </div>
                <button type="submit" name="add_activity" class="btn btn-primary">Add Activity</button>
            </form>
        </div>
    </div>

    <!-- Display Activity Logs -->
    <h4 class="mt-5">Activity Logs</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Activity Date</th>
                <th>Description</th>
                <th>Photos/Videos</th>
                <th>Playtime</th>
                <th>Learning Lesson</th>
                <th>Meals</th>
                <th>Naps</th>
                <th>Other Activities</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($activity = $activities_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($activity['activity_date']); ?></td>
                    <td><?php echo htmlspecialchars($activity['activity_description']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($activity['photos_videos']); ?>" target="_blank">View</a></td>
                    <td><?php echo htmlspecialchars($activity['playtime']); ?></td>
                    <td><?php echo htmlspecialchars($activity['learning_lesson']); ?></td>
                    <td><?php echo htmlspecialchars($activity['meals']); ?></td>
                    <td><?php echo htmlspecialchars($activity['naps']); ?></td>
                    <td><?php echo htmlspecialchars($activity['other_activities']); ?></td>
                    <td>
                        <a href="edit_activity.php?activity_id=<?php echo $activity['activity_id']; ?>" class="btn btn-warning btn-sm mb-2">Edit</a>
                        <a href="?delete_id=<?php echo $activity['activity_id']; ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this activity log?');">Delete</a>
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
