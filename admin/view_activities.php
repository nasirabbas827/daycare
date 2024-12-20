<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];

    // Fetch activities for the selected child
    $sql = "SELECT * FROM activities WHERE child_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Activities</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Activities Records</h2>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Activity Date</th>
                        <th>Activity Description</th>
                        <th>Photos/Videos</th>
                        <th>Meals</th>
                        <th>Naps</th>
                        <th>Other</th>
                        <th>Playtime</th>
                        <th>Learning/Lesson</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($activity = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$activity['activity_date']}</td>
                                <td>{$activity['activity_description']}</td>
                                <td>{$activity['photos_videos']}</td>
                                <td>{$activity['meals']}</td>
                                <td>{$activity['naps']}</td>
                                <td>{$activity['other_activities']}</td>
                                <td>{$activity['playtime']}</td>
                                <td>{$activity['learning_lesson']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No Activity Data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
