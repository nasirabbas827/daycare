<?php
session_start();
include('config.php');

// Check if the admin is logged in
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Retrieve the child_id from the URL
if (isset($_GET['child_id'])) {
    $child_id = $_GET['child_id'];
} else {
    // Redirect if no child_id is provided
    header("Location: manage_child.php");
    exit;
}

// Fetch child details (to show child link)
$child_sql = "SELECT name FROM children WHERE child_id = ?";
$stmt = $conn->prepare($child_sql);
$stmt->bind_param("i", $child_id);
$stmt->execute();
$child_result = $stmt->get_result();
$child = $child_result->fetch_assoc();

// Handle invoice addition
if (isset($_POST['submit_invoice'])) {
    $attendance_days = $_POST['attendance_days'];
    $total_amount = $_POST['total_amount'];
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];

    $add_invoice_sql = "INSERT INTO invoices (child_id, attendance_days, total_amount, due_date, status)
                        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($add_invoice_sql);
    $stmt->bind_param("iisss", $child_id, $attendance_days, $total_amount, $due_date, $status);
    if ($stmt->execute()) {
        $message = "Invoice added successfully!";
    } else {
        $message = "Error adding invoice: " . $stmt->error;
    }
}

// Fetch invoices for the selected child
$invoice_sql = "SELECT * FROM invoices WHERE child_id = ?";
$stmt = $conn->prepare($invoice_sql);
$stmt->bind_param("i", $child_id);
$stmt->execute();
$invoice_result = $stmt->get_result();

// Handle invoice deletion
if (isset($_GET['delete_invoice_id'])) {
    $delete_invoice_id = $_GET['delete_invoice_id'];
    $delete_sql = "DELETE FROM invoices WHERE invoice_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_invoice_id);
    $stmt->execute();
    header("Location: manage_invoice.php?child_id=$child_id"); // Refresh page after deletion
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Invoices</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Invoices for <?php echo htmlspecialchars($child['name']); ?></h2>

    <!-- Display success or error message -->
    <?php if (isset($message)) { ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php } ?>

    <!-- Add Invoice Form -->
    <div class="card mx-auto mb-4" style="max-width: 600px;">
        <div class="card-body">
            <h4 class="card-title text-center mb-4">Add Invoice</h4>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="attendance_days">Attendance Days</label>
                    <input type="number" class="form-control" id="attendance_days" name="attendance_days" required>
                </div>
                <div class="form-group">
                    <label for="total_amount">Total Amount</label>
                    <input type="number" class="form-control" id="total_amount" name="total_amount" required>
                </div>
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="Unpaid">Unpaid</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
                <button type="submit" name="submit_invoice" class="btn btn-primary btn-block">Add Invoice</button>
            </form>
        </div>
    </div>

    <!-- Invoices Table -->
    <h3 class="text-center mb-4">Existing Invoices</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Attendance Days</th>
                <th>Total Amount</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($invoice_result->num_rows > 0) {
                while ($invoice = $invoice_result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$invoice['invoice_id']}</td>
                        <td>{$invoice['attendance_days']}</td>
                        <td>{$invoice['total_amount']}</td>
                        <td>{$invoice['due_date']}</td>
                        <td>{$invoice['status']}</td>
                        <td>
                            <a href='manage_invoice.php?child_id=$child_id&delete_invoice_id={$invoice['invoice_id']}' class='btn btn-outline-danger btn-sm'>Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No Invoices Found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</div>

</body>
</html>
