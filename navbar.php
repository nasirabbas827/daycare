<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="home.php">Daycare System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php
            if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
                echo '<li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="update_profile.php">Update Profile</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="view_children.php">Children</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="child_data.php">Daily Report</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="view_invoices.php">Payments</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="contact_support.php">Customer Support</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="view_messages.php">View Messages</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
            } else {
                echo '<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="staff_login.php">Staff Login</a></li>';
                echo '<li class="nav-item"><a class="nav-link" href="./admin/admin_login.php">Admin Login</a></li>';
            }
            ?>
        </ul>
    </div>
</nav>
