<?php
include('config.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

$user_id = $_SESSION["id"]; // Parent ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $date_of_birth = $_POST['date_of_birth'];
    $age_group = $_POST['age_group'];
    $allergies = $_POST['allergies'];
    $special_needs = $_POST['special_needs'];

    $sql = "INSERT INTO Children (parent_id, name, date_of_birth, age_group, allergies, special_needs) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssss", $user_id, $name, $date_of_birth, $age_group, $allergies, $special_needs);
    if ($stmt->execute()) {
        header("Location: view_children.php");
        exit;
    } else {
        $error_message = "Error adding child.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Child</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-5">
    <div class="card shadow mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h2 class="text-center">Add Child</h2>
            <?php if (!empty($error_message)) { ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php } ?>
            <form method="POST">
                <div class="form-group">
                    <label for="name">Child's Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
                </div>
                <div class="form-group">
                    <label for="age_group">Age Group:</label>
                    <select class="form-control" id="age_group" name="age_group" required>
                        <option value="Toddler">Toddler</option>
                        <option value="Preschool">Preschool</option>
                        <option value="Elementary">Elementary</option>
                        <option value="Teen">Teen</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="allergies">Allergies:</label>
                    <textarea class="form-control" id="allergies" name="allergies"></textarea>
                </div>
                <div class="form-group">
                    <label for="special_needs">Special Needs:</label>
                    <textarea class="form-control" id="special_needs" name="special_needs"></textarea>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Add Child</button>
                    <a href="view_children.php" class="btn btn-outline-dark">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
