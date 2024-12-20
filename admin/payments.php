<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Fetch all payments
$sql = "SELECT payments.payment_id, payments.invoice_id, payments.amount, payments.payment_date, payments.payment_method, payments.transaction_image, invoices.status AS invoice_status 
        FROM payments 
        JOIN invoices ON payments.invoice_id = invoices.invoice_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$payments_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Payments</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Payments Overview</h2>

    <!-- Display Success or Error Messages -->
    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php } elseif (!empty($error_message)) { ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php } ?>

    <!-- Payments Table -->
    <table id="paymentsTable" class="display table table-bordered">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Invoice ID</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th>Payment Method</th>
                <th>Transaction Image</th>
                <th>Invoice Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $payments_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['payment_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['invoice_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                    <td><img src="../<?php echo htmlspecialchars($row['transaction_image']); ?>" alt="Transaction Image" width="100"></td>
                    <td><?php echo htmlspecialchars($row['invoice_status']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <a href="admin_dashboard.php" class="btn btn-outline-dark mt-3">Back to Dashboard</a>
</div>

<!-- Include jQuery, DataTables, and export buttons JS -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#paymentsTable').DataTable({
            dom: 'Bfrtip', // To place the export button above the table
            buttons: [
                {
                    extend: 'csvHtml5',
                    text: 'Export to CSV',
                    title: 'Payments Data',
                    className: 'btn btn-success',  // Add the btn btn-success class here
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });
    });
</script>


</body>
</html>
