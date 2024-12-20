<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION["id"];

// Fetch all invoices for the user (assuming child_id is related to the user)
$sql = "SELECT * FROM invoices WHERE child_id IN (SELECT child_id FROM children WHERE parent_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View Invoices</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Invoices</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Attendance Days</th>
                    <th>Total Amount</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['invoice_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['attendance_days']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['due_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'Unpaid') { ?>
                                <a href="pay_invoice.php?invoice_id=<?php echo $row['invoice_id']; ?>" class="btn btn-primary">Pay Now</a>
                            <?php } ?>
                        </td>
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
