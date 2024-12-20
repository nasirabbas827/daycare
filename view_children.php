<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"]; // Parent ID

$sql = "SELECT * FROM Children WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Children</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    
    <h2 class="text-center">My Children</h2>
    <a href="add_child.php" class="btn btn-success">Add New Child</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date of Birth</th>
                <th>Age Group</th>
                <th>Allergies</th>
                <th>Special Needs</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_of_birth']); ?></td>
                    <td><?php echo htmlspecialchars($row['age_group']); ?></td>
                    <td><?php echo htmlspecialchars($row['allergies']); ?></td>
                    <td><?php echo htmlspecialchars($row['special_needs']); ?></td>
                    <td>
                        <a href="edit_child.php?child_id=<?php echo $row['child_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete_child.php?child_id=<?php echo $row['child_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this child?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
