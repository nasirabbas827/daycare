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

// Fetch the user data from the database
$sql = "SELECT username, full_name FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $stmt->bind_result($username, $full_name);
    $stmt->fetch();
} else {
    // If user data is not found, redirect to login page
    header("location: index.php");
    exit;
}

$stmt->close();

// Fetch total children
$sql_children = "SELECT COUNT(*) as total_children FROM children WHERE parent_id = ?";
$stmt = $conn->prepare($sql_children);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total_children = $result->fetch_assoc()['total_children'];

// Fetch recent notifications
$sql_notifications = "SELECT message, notification_type, created_at FROM notifications
                      ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($sql_notifications);
$stmt->execute();
$notifications = $stmt->get_result();

// Fetch attendance notifications
$sql_attendance = "SELECT an.message, an.notification_time FROM attendance_notifications an
                   JOIN children c ON an.child_id = c.child_id
                   WHERE c.parent_id = ?
                   ORDER BY an.notification_time DESC LIMIT 5";
$stmt = $conn->prepare($sql_attendance);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$attendance_notifications = $stmt->get_result();

// Fetch unpaid invoices
$sql_invoices = "SELECT i.invoice_id, i.total_amount, i.due_date FROM invoices i
                 JOIN children c ON i.child_id = c.child_id
                 WHERE c.parent_id = ? AND i.status = 'Unpaid'";
$stmt = $conn->prepare($sql_invoices);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$unpaid_invoices = $stmt->get_result();

// Calculate total payments
$sql_payments = "SELECT SUM(p.amount) as total_payments FROM payments p
                 JOIN invoices i ON p.invoice_id = i.invoice_id
                 JOIN children c ON i.child_id = c.child_id
                 WHERE c.parent_id = ?";
$stmt = $conn->prepare($sql_payments);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_payments = $stmt->get_result()->fetch_assoc()['total_payments'];

// Fetch meal request status
$sql_meal_requests = "SELECT mr.meal_type, mr.status FROM meal_requests mr
                      JOIN children c ON mr.child_id = c.child_id
                      WHERE c.parent_id = ?
                      ORDER BY mr.request_date DESC LIMIT 5";
$stmt = $conn->prepare($sql_meal_requests);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$meal_requests = $stmt->get_result();

// Generate self-notification for upcoming due invoices
$upcoming_due_invoices = [];
while ($invoice = $unpaid_invoices->fetch_assoc()) {
    $due_date = new DateTime($invoice['due_date']);
    $today = new DateTime();
    $days_until_due = $today->diff($due_date)->days;
    if ($days_until_due <= 7 && $days_until_due >= 0) {
        $upcoming_due_invoices[] = $invoice;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Parent Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
    
    <style>
        .dashboard-card {
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .card-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .bg-gradient-primary {
            background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
        }
        .bg-gradient-success {
            background: linear-gradient(45deg, #1cc88a 0%, #13855c 100%);
        }
        .bg-gradient-info {
            background: linear-gradient(45deg, #36b9cc 0%, #258391 100%);
        }
        .bg-gradient-warning {
            background: linear-gradient(45deg, #f6c23e 0%, #dda20a 100%);
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

        <div class="row">
            <div class="col-md-3">
                <div class="card dashboard-card bg-gradient-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-child card-icon"></i>
                        <h5 class="card-title">Total Children</h5>
                        <p class="card-text display-4"><?php echo $total_children; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card bg-gradient-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-file-invoice-dollar card-icon"></i>
                        <h5 class="card-title">Unpaid Invoices</h5>
                        <p class="card-text display-4"><?php echo $unpaid_invoices->num_rows; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card bg-gradient-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign card-icon"></i>
                        <h5 class="card-title">Total Payments(Pkr)</h5>
                        <p class="card-text display-4"><?php echo number_format($total_payments, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card bg-gradient-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-utensils card-icon"></i>
                        <h5 class="card-title">Meal Requests</h5>
                        <p class="card-text display-4"><?php echo $meal_requests->num_rows; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="fas fa-bell mr-2"></i>Recent Notifications</h5>
                    </div>
                    <div class="card-body notification-list">
                        <ul class="list-group">
                            <?php while ($notification = $notifications->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong><?php echo htmlspecialchars($notification['notification_type']); ?>:</strong>
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                    <small class="text-muted float-right"><?php echo date('M d, H:i', strtotime($notification['created_at'])); ?></small>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-clipboard-check mr-2"></i>Attendance Notifications</h5>
                    </div>
                    <div class="card-body notification-list">
                        <ul class="list-group">
                            <?php while ($attendance = $attendance_notifications->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <i class="fas fa-user-clock mr-2"></i>
                                    <?php echo htmlspecialchars($attendance['message']); ?>
                                    <small class="text-muted float-right"><?php echo date('M d, H:i', strtotime($attendance['notification_time'])); ?></small>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-warning text-white">
                        <h5><i class="fas fa-exclamation-triangle mr-2"></i>Unpaid Invoices</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php 
                            $unpaid_invoices->data_seek(0);
                            while ($invoice = $unpaid_invoices->fetch_assoc()): 
                            ?>
                                <li class="list-group-item">
                                    <i class="fas fa-file-invoice mr-2"></i>
                                    Invoice #<?php echo $invoice['invoice_id']; ?>
                                    <span class="float-right">
                                        $<?php echo number_format($invoice['total_amount'], 2); ?>
                                        <small class="text-muted ml-2">Due: <?php echo date('M d, Y', strtotime($invoice['due_date'])); ?></small>
                                    </span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card dashboard-card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-utensils mr-2"></i>Meal Request Status</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php while ($meal_request = $meal_requests->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <i class="fas fa-hamburger mr-2"></i>
                                    <?php echo htmlspecialchars($meal_request['meal_type']); ?>
                                    <span class="float-right badge badge-<?php echo $meal_request['status'] == 'Approved' ? 'success' : ($meal_request['status'] == 'Rejected' ? 'danger' : 'warning'); ?>">
                                        <?php echo $meal_request['status']; ?>
                                    </span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($upcoming_due_invoices)): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading"><i class="fas fa-exclamation-circle mr-2"></i>Upcoming Due Invoices</h4>
                    <p>The following invoices are due within the next 7 days:</p>
                    <ul>
                        <?php foreach ($upcoming_due_invoices as $invoice): ?>
                            <li>
                                <i class="fas fa-file-invoice mr-2"></i>
                                Invoice #<?php echo $invoice['invoice_id']; ?> - 
                                $<?php echo number_format($invoice['total_amount'], 2); ?> 
                                (Due: <?php echo date('M d, Y', strtotime($invoice['due_date'])); ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Bootstrap JS & dependencies -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>