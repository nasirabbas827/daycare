<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"]; // Parent ID

// Check if child_id is provided
if (!isset($_GET['child_id'])) {
    header("location: view_children.php");
    exit;
}

$child_id = $_GET['child_id'];

// Fetch child's current details
$sql = "SELECT * FROM Children WHERE child_id = ? AND parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $child_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("location: view_children.php");
    exit;
}

$child = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $date_of_birth = $_POST['date_of_birth'];
    $age_group = $_POST['age_group'];
    $allergies = $_POST['allergies'];
    $special_needs = $_POST['special_needs'];

    $update_sql = "UPDATE Children SET name = ?, date_of_birth = ?, age_group = ?, allergies = ?, special_needs = ?, updated_at = NOW() WHERE child_id = ? AND parent_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssss", $name, $date_of_birth, $age_group, $allergies, $special_needs, $child_id, $user_id);

    if ($update_stmt->execute()) {
        $success_message = "Child information updated successfully.";
        header("location: view_children.php");
        exit;
    } else {
        $error_message = "Failed to update the record. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Child</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Edit Child</h2>
    <div class="card mx-auto mt-4" style="max-width: 600px;">
        <div class="card-body">
            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>

            <form method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($child['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($child['date_of_birth']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="age_group">Age Group</label>
                    <input type="text" class="form-control" id="age_group" name="age_group" value="<?php echo htmlspecialchars($child['age_group']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="allergies">Allergies</label>
                    <textarea class="form-control" id="allergies" name="allergies"><?php echo htmlspecialchars($child['allergies']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="special_needs">Special Needs</label>
                    <textarea class="form-control" id="special_needs" name="special_needs"><?php echo htmlspecialchars($child['special_needs']); ?></textarea>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="view_children.php" class="btn btn-outline-dark">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
