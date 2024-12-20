<?php
session_start();
include('config.php');

if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];

    // Fetch attendance for the selected child
    $sql = "SELECT * FROM attendance WHERE child_id = ?";
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
    <title>View Attendance</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Attendance Records</h2>
 
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Check-in Time</th>
                        <th>Check-out Time</th>
                        <th>Staff</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($attendance = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$attendance['date']}</td>
                                <td>{$attendance['check_in_time']}</td>
                                <td>{$attendance['check_out_time']}</td>
                                <td>{$attendance['staff_id']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No Attendance Data</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
