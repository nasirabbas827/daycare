<?php
session_start();
include('config.php');

// Fetch summary data
$totalChildren = $conn->query("SELECT COUNT(*) FROM children")->fetch_row()[0];
$totalStaff = $conn->query("SELECT COUNT(*) FROM staff")->fetch_row()[0];
$totalParents = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$pendingMealRequests = $conn->query("SELECT COUNT(*) FROM meal_requests WHERE status = 'Pending'")->fetch_row()[0];

// Fetch age group data for pie chart
$ageGroupData = $conn->query("SELECT age_group, COUNT(*) as count FROM children GROUP BY age_group");
$ageGroups = [];
$ageGroupCounts = [];
while ($row = $ageGroupData->fetch_assoc()) {
    $ageGroups[] = $row['age_group'];
    $ageGroupCounts[] = $row['count'];
}

// Fetch monthly payment data for bar chart
$monthlyPaymentData = $conn->query("
    SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total
    FROM payments
    WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
    ORDER BY month
");
$months = [];
$payments = [];
while ($row = $monthlyPaymentData->fetch_assoc()) {
    $months[] = date('M Y', strtotime($row['month']));
    $payments[] = $row['total'];
}

// Fetch recent activities
$recentActivities = $conn->query("
    SELECT 'activity' AS type, activity_date AS date, CONCAT(c.name, ' - ', a.activity_description) AS description
    FROM activities a
    JOIN children c ON a.child_id = c.child_id
    UNION ALL
    SELECT 'meal_request' AS type, request_date AS date, CONCAT(c.name, ' - ', m.meal_type, ' request') AS description
    FROM meal_requests m
    JOIN children c ON m.child_id = c.child_id
    UNION ALL
    SELECT 'payment' AS type, payment_date AS date, CONCAT('Payment of $', p.amount, ' for invoice #', p.invoice_id) AS description
    FROM payments p
    ORDER BY date DESC
    LIMIT 10
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Daycare Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-card {
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        #ageGroupChart{
    width: 100% !important;  /* Make the charts responsive */
    height: 280px !important; /* Adjust the height as needed */
}

    </style>
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container-fluid mt-4">
    <h1 class="mb-4">Daycare Admin Dashboard</h1>

    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white dashboard-card">
                <div class="card-body">
                    <h5 class="card-title">Total Children</h5>
                    <p class="card-text display-4"><?php echo $totalChildren; ?></p>
                    <i class="fas fa-child fa-3x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white dashboard-card">
                <div class="card-body">
                    <h5 class="card-title">Total Staff</h5>
                    <p class="card-text display-4"><?php echo $totalStaff; ?></p>
                    <i class="fas fa-users fa-3x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white dashboard-card">
                <div class="card-body">
                    <h5 class="card-title">Total Parents</h5>
                    <p class="card-text display-4"><?php echo $totalParents; ?></p>
                    <i class="fas fa-user-friends fa-3x"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white dashboard-card">
                <div class="card-body">
                    <h5 class="card-title">Pending Meals</h5>
                    <p class="card-text display-4"><?php echo $pendingMealRequests; ?></p>
                    <i class="fas fa-utensils fa-3x"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card" >
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Age Group Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="ageGroupChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Monthly Payments</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyPaymentChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php
                        while ($activity = $recentActivities->fetch_assoc()) {
                            $icon = '';
                            switch ($activity['type']) {
                                case 'activity':
                                    $icon = '<i class="fas fa-running text-primary"></i>';
                                    break;
                                case 'meal_request':
                                    $icon = '<i class="fas fa-utensils text-warning"></i>';
                                    break;
                                case 'payment':
                                    $icon = '<i class="fas fa-money-bill-wave text-success"></i>';
                                    break;
                            }
                            echo "<li class='list-group-item'>{$icon} " . htmlspecialchars($activity['description']) . " <small class='text-muted float-right'>" . date('M d, H:i', strtotime($activity['date'])) . "</small></li>";
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
<script>
// Age Group Pie Chart
var ageGroupCtx = document.getElementById('ageGroupChart').getContext('2d');
var ageGroupChart = new Chart(ageGroupCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($ageGroups); ?>,
        datasets: [{
            data: <?php echo json_encode($ageGroupCounts); ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
            ],
        }]
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: 'Age Group Distribution'
        }
    }
});

// Monthly Payment Bar Chart
var monthlyPaymentCtx = document.getElementById('monthlyPaymentChart').getContext('2d');
var monthlyPaymentChart = new Chart(monthlyPaymentCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Total Payments',
            data: <?php echo json_encode($payments); ?>,
            backgroundColor: 'rgba(75, 192, 192, 0.8)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value, index, values) {
                        return '$' + value;
                    }
                }
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Monthly Payments'
            }
        }
    }
});
</script>
</body>
</html>