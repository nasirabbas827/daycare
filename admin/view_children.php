<?php
session_start();
include('config.php');

// Check if the admin is logged in
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all children with their parent's username
$sql = "SELECT users.username AS parent_username, children.*, 
               GROUP_CONCAT(DISTINCT attendance.date ORDER BY attendance.date ASC) AS attendance_dates,
               GROUP_CONCAT(DISTINCT activities.activity_description ORDER BY activities.activity_date ASC) AS activities
        FROM children
        LEFT JOIN users ON users.id = children.parent_id
        LEFT JOIN attendance ON attendance.child_id = children.child_id
        LEFT JOIN activities ON activities.child_id = children.child_id
        GROUP BY children.child_id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Children and Activities</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">View Children and Activities</h2>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Child Name</th>
                        <th>Parent Username</th>
                        <th>Attendance</th>
                        <th>Activities</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($child = $result->fetch_assoc()) {
                            $attendance_dates = $child['attendance_dates'] ? $child['attendance_dates'] : 'No Attendance Data';
                            $activities = $child['activities'] ? $child['activities'] : 'No Activities Logged';
                            echo "<tr>
                                <td>{$child['name']}</td>
                                <td>{$child['parent_username']}</td>
                                <td>{$attendance_dates}</td>
                                <td>{$activities}</td>
                                <td>
                                    <a href='view_attendance.php?child_id={$child['child_id']}' class='btn btn-outline-primary btn-sm'>Attendance</a>
                                    <a href='view_activities.php?child_id={$child['child_id']}' class='btn btn-outline-success btn-sm'>Activities</a>
                                    <a href='manage_invoice.php?child_id={$child['child_id']}' class='btn btn-outline-success btn-sm'>Invoices</a>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No Children Found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
