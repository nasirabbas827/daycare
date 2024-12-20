<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get user ID from session
$user_id = $_SESSION["id"];

// Get invoice_id from URL
if (isset($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id'];
} else {
    header("location: view_invoices.php");
    exit;
}

// Fetch the invoice details
$sql = "SELECT * FROM invoices WHERE invoice_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

// Check if the invoice is unpaid
if ($invoice['status'] !== 'Unpaid') {
    header("location: view_invoices.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle payment process
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $payment_date = date('Y-m-d');
    
    // Handle file upload
    $transaction_image = $_FILES['transaction_image']['name'];
    $target_dir = "transactions/";
    $target_file = $target_dir . basename($transaction_image);
    if (move_uploaded_file($_FILES['transaction_image']['tmp_name'], $target_file)) {
        
        // Insert payment record
        $payment_sql = "INSERT INTO payments (invoice_id, amount, payment_date, payment_method, transaction_image) VALUES (?, ?, ?, ?, ?)";
        $payment_stmt = $conn->prepare($payment_sql);
        $payment_stmt->bind_param("iisss", $invoice_id, $amount, $payment_date, $payment_method, $target_file);
        $payment_stmt->execute();

        // Update invoice status to 'paid'
        $update_invoice_sql = "UPDATE invoices SET status = 'paid' WHERE invoice_id = ?";
        $update_invoice_stmt = $conn->prepare($update_invoice_sql);
        $update_invoice_stmt->bind_param("i", $invoice_id);
        $update_invoice_stmt->execute();

        // Redirect to the invoice list page
        header("location: view_invoices.php");
        exit;
    } else {
        $error_message = "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Pay Invoice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-5">
        <h2>Pay Invoice #<?php echo htmlspecialchars($invoice['invoice_id']); ?></h2>

        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>
        <div class="card shadow mx-auto" style="max-width: 600px;">
        <div class="card-body">
        <form action="pay_invoice.php?invoice_id=<?php echo $invoice['invoice_id']; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="text" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($invoice['total_amount']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="Card">Card</option>
                    <option value="Online Transfer">Online Transfer</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>
            <div class="form-group">
                <label for="transaction_image">Upload Transaction Image</label>
                <input type="file" class="form-control" id="transaction_image" name="transaction_image" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Payment</button>
            <a href="view_invoices.php" class="btn btn-outline-dark">Back</a>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
