<?php
include('config.php');
session_start();

// Check if user is logged in
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

// Fetch total children
$sql_children = "SELECT COUNT(*) as total_children FROM children";
$result_children = $conn->query($sql_children);
$total_children = $result_children->fetch_assoc()['total_children'];

// Fetch total meals
$sql_meals = "SELECT COUNT(*) as total_meals FROM meal_schedule";
$result_meals = $conn->query($sql_meals);
$total_meals = $result_meals->fetch_assoc()['total_meals'];

// Fetch total meal requests
$sql_meal_requests = "SELECT COUNT(*) as total_meal_requests FROM meal_requests";
$result_meal_requests = $conn->query($sql_meal_requests);
$total_meal_requests = $result_meal_requests->fetch_assoc()['total_meal_requests'];

// Fetch total unreplied messages
$sql_messages = "SELECT COUNT(*) as total_unreplied_messages FROM messages WHERE reply_text IS NULL";
$result_messages = $conn->query($sql_messages);
$total_unreplied_messages = $result_messages->fetch_assoc()['total_unreplied_messages'];

$sql_recent_activities = "
    (SELECT 'activity' AS type, activity_date AS date, CONCAT(c.name, ' - ', a.activity_description) AS description
    FROM activities a
    JOIN children c ON a.child_id = c.child_id
    ORDER BY activity_date DESC
    LIMIT 5)
    UNION ALL
    (SELECT 'meal_request' AS type, request_date AS date, CONCAT(c.name, ' - ', m.meal_type, ' meal request') AS description
    FROM meal_requests m
    JOIN children c ON m.child_id = c.child_id
    ORDER BY request_date DESC
    LIMIT 5)
    UNION ALL
    (SELECT 'message' AS type, sent_datetime AS date, CONCAT('New message from ', u.full_name) AS description
    FROM messages m
    JOIN users u ON m.sender_id = u.id
    WHERE m.reply_text IS NULL
    ORDER BY sent_datetime DESC
    LIMIT 5)
    ORDER BY date DESC
    LIMIT 10
";

$result_recent_activities = $conn->query($sql_recent_activities);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Staff Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .dashboard-card {
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-body">
            <h2 class="text-center mb-4">Welcome, <?php echo htmlspecialchars($staff['name']); ?>!</h2>
            <p class="text-center">You are successfully logged in to the staff dashboard.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white shadow dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Children</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo $total_children; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-child fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white shadow dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total Meals</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo $total_meals; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-utensils fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white shadow dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Meal Requests</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo $total_meal_requests; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white shadow dashboard-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Unreplied Messages</div>
                            <div class="h5 mb-0 font-weight-bold"><?php echo $total_unreplied_messages; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="view_children.php" class="btn btn-info btn-block mb-2">View Children</a>
                    <a href="meal_schedule.php" class="btn btn-success btn-block mb-2">Manage Meals</a>
                    <a href="meal_requests.php" class="btn btn-warning btn-block mb-2">View Meal Requests</a>
                    <a href="admin_reply.php" class="btn btn-danger btn-block">Check Messages</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php
                    if ($result_recent_activities->num_rows > 0) {
                        while ($activity = $result_recent_activities->fetch_assoc()) {
                            $icon = '';
                            switch ($activity['type']) {
                                case 'activity':
                                    $icon = '<i class="fas fa-running text-primary"></i>';
                                    break;
                                case 'meal_request':
                                    $icon = '<i class="fas fa-utensils text-warning"></i>';
                                    break;
                                case 'message':
                                    $icon = '<i class="fas fa-envelope text-info"></i>';
                                    break;
                            }
                            echo "<li class='list-group-item'>{$icon} " . htmlspecialchars($activity['description']) . 
                                 " <small class='text-muted float-right'>" . date('M d, H:i', strtotime($activity['date'])) . "</small></li>";
                        }
                    } else {
                        echo "<li class='list-group-item'>No recent activities.</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>