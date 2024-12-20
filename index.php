<?php
include('config.php');

// Fetch statistics from database
$stats = array();

// Total children
$query = "SELECT COUNT(*) as total FROM children";
$result = mysqli_query($conn, $query);
$stats['children'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Total staff
$query = "SELECT COUNT(*) as total FROM staff";
$result = mysqli_query($conn, $query);
$stats['staff'] = mysqli_fetch_assoc($result)['total'] ?? 0;

// Dummy testimonials (can be replaced with database data)
$testimonials = [
    [
        'name' => 'Sarah Johnson',
        'role' => 'Parent',
        'image' => 'images/parent1.jpg',
        'text' => 'The care and attention my child receives here is exceptional. The staff is amazing!'
    ],
    [
        'name' => 'Michael Smith',
        'role' => 'Parent',
        'image' => 'images/parent2.jpg',
        'text' => 'The learning programs have helped my daughter develop tremendously. Highly recommended!'
    ],
    [
        'name' => 'Emily Davis',
        'role' => 'Parent',
        'image' => 'images/parent3.jpg',
        'text' => 'The facility is clean, safe, and welcoming. My son loves coming here every day!'
    ]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daycare Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="Daycare Management System">
    <meta property="og:description" content="Providing exceptional childcare services">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .jumbotron {
            height: 500px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./images/hotel.jpg');
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .jumbotron h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .jumbotron p {
            font-size: 1.8rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .feature-card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #4e73df;
        }

        .stats-section {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
            padding: 60px 0;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .social-share {
            position: fixed;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
        }

        .social-share a {
            display: block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border-radius: 50%;
            margin: 10px 0;
            color: white;
            transition: transform 0.3s ease;
        }

        .social-share a:hover {
            transform: scale(1.1);
        }

        .facebook { background: #3b5998; }
        .twitter { background: #1da1f2; }
        .linkedin { background: #0077b5; }
        .whatsapp { background: #25d366; }

        @media (max-width: 768px) {
            .jumbotron {
                height: 400px;
            }

            .jumbotron h1 {
                font-size: 2.5rem;
            }

            .jumbotron p {
                font-size: 1.2rem;
            }

            .social-share {
                position: fixed;
                left: 0;
                top: auto;
                bottom: 0;
                transform: none;
                width: 100%;
                background: white;
                display: flex;
                justify-content: center;
                padding: 10px;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            }

            .social-share a {
                margin: 0 10px;
            }
        }
    </style>
</head>
<body>

<?php include('navbar.php'); ?>

<div class="jumbotron text-center">
    <h1>Welcome to Little Angels Daycare</h1>
    <p>Nurturing Minds, Building Futures</p>
    <a href="login.php" class="btn btn-primary btn-lg px-5 py-3">Join Our Family</a>
</div>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Services</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card feature-card text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-graduation-cap feature-icon"></i>
                        <h4 class="card-title">Early Education</h4>
                        <p class="card-text">Structured learning programs designed for different age groups to promote cognitive development.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-heart feature-icon"></i>
                        <h4 class="card-title">Nurturing Care</h4>
                        <p class="card-text">Professional and caring staff dedicated to your child's wellbeing and happiness.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-puzzle-piece feature-icon"></i>
                        <h4 class="card-title">Creative Activities</h4>
                        <p class="card-text">Engaging activities that foster creativity, social skills, and personal growth.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $stats['children']; ?>+</div>
                    <div class="stat-label">Happy Children</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $stats['staff']; ?>+</div>
                    <div class="stat-label">Professional Staff</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">What Parents Say</h2>
        <div class="row">
            <?php foreach($testimonials as $testimonial): ?>
            <div class="col-md-4">
                <div class="testimonial-card text-center">
                    <img src="https://images.pexels.com/photos/712513/pexels-photo-712513.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2" alt="<?php echo htmlspecialchars($testimonial['name']); ?>" class="testimonial-img">
                    <h5><?php echo htmlspecialchars($testimonial['name']); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($testimonial['role']); ?></p>
                    <p class="mt-3"><?php echo htmlspecialchars($testimonial['text']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Social Share Buttons -->
<div class="social-share">
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
       target="_blank" class="facebook">
        <i class="fab fa-facebook-f"></i>
    </a>
    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=Check out this amazing daycare!" 
       target="_blank" class="twitter">
        <i class="fab fa-twitter"></i>
    </a>
    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
       target="_blank" class="linkedin">
        <i class="fab fa-linkedin-in"></i>
    </a>
    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode('Check out this amazing daycare! ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
       target="_blank" class="whatsapp">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>

<!-- Call to Action Section -->
<section class="py-5 text-center">
    <div class="container">
        <h2 class="mb-4">Ready to Give Your Child the Best Care?</h2>
        <p class="mb-4">Join our daycare family and watch your child thrive in a nurturing environment.</p>
        <a href="register.php" class="btn btn-primary btn-lg px-5">Register Now</a>
    </div>
</section>

<footer class="py-4 bg-dark text-white">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <p>
                    <i class="fas fa-map-marker-alt mr-2"></i> 123 Daycare Street<br>
                    <i class="fas fa-phone mr-2"></i> (123) 456-7890<br>
                    <i class="fas fa-envelope mr-2"></i> info@littleangels.com
                </p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white">About Us</a></li>
                    <li><a href="#" class="text-white">Services</a></li>
                    <li><a href="#" class="text-white">Programs</a></li>
                    <li><a href="#" class="text-white">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Newsletter</h5>
                <form>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Subscribe</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
            <p class="mb-0">&copy; 2024 Little Angels Daycare. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>