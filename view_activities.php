<?php
include('config.php');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Get child_id from URL
if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];
} else {
    header("location: home.php");
    exit;
}

// Fetch activities records for the specific child
$sql = "SELECT * FROM activities WHERE child_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $child_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch child data for display
$child_sql = "SELECT * FROM children WHERE child_id = ?";
$child_stmt = $conn->prepare($child_sql);
$child_stmt->bind_param("i", $child_id);
$child_stmt->execute();
$child_data = $child_stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Activities</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Activities Records for <?php echo htmlspecialchars($child_data['name']); ?></h2>
        
        <!-- Displaying Photos/Videos as cards -->
        <div class="row mt-4">
            <?php while ($row = $result->fetch_assoc()) { 
                $photos_videos = explode(',', $row['photos_videos']); // assuming multiple photos/videos are stored as comma-separated values
                ?>
                <div class="col-md-4 mb-3">
                    <?php foreach ($photos_videos as $media) { ?>
                        <div class="card">
                            <img src="staff/<?php echo htmlspecialchars($media); ?>" class="card-img-top" alt="Media">
                            <div class="card-body">
                                <p class="card-text"><?php echo htmlspecialchars($row['activity_description']); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <!-- Displaying Activity Details in a Table -->
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Activity Date</th>
                    <th>Description</th>
                    <th>Playtime</th>
                    <th>Learning Lesson</th>
                    <th>Meals</th>
                    <th>Naps</th>
                    <th>Other Activities</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Reset the result pointer to start from the first row
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['activity_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['activity_description']); ?></td>
                        <td><?php echo htmlspecialchars($row['playtime']); ?></td>
                        <td><?php echo htmlspecialchars($row['learning_lesson']); ?></td>
                        <td><?php echo htmlspecialchars($row['meals']); ?></td>
                        <td><?php echo htmlspecialchars($row['naps']); ?></td>
                        <td><?php echo htmlspecialchars($row['other_activities']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="home.php" class="btn btn-outline-dark">Back</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
