<?php
	$host = 'localhost'; 
	$user = 'root';
	$pass = '';
	$name = 'photosalonramik';
	
	$link = mysqli_connect($host, $user, $pass, $name);

	$message = '';
	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_submit'])) {
		$email = mysqli_real_escape_string($link, $_POST['email']);
		
		if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$query = "INSERT IGNORE INTO spamnews (email) VALUES ('$email')";
			$res = mysqli_query($link, $query);
			
			if ($res && mysqli_affected_rows($link) > 0) {
				$message = '<div class="alert alert-success mt-3">Спасибо! Вы подписались на рассылку.</div>';
			} elseif ($res && mysqli_affected_rows($link) === 0) {
				$message = '<div class="alert alert-info mt-3">Вы уже подписаны на рассылку!</div>';
			} else {
				$message = '<div class="alert alert-danger mt-3">Ошибка: ' . mysqli_error($link) . '</div>';
			}
		} else {
			$message = '<div class="alert alert-warning mt-3">Пожалуйста, введите корректный email.</div>';
		}
	}
?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "photosalonramik";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$sql = "SELECT ID, Name, Format, Price, Material FROM photoservices ORDER BY ID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>(Услуги) Список</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #8a6d3b;
            --secondary: #f9f1e3;
            --dark: #333;
            --white: #fff;
            --transition: all 0.3s ease;
        }
        body {
            font-family: 'Roboto', sans-serif;
            color: var(--dark);
            line-height: 1.6;
        }
        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary) !important;
        }
        .navbar-nav .nav-link {
            font-weight: 500;
            transition: var(--transition);
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary) !important;
        }
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7));
            color: var(--white);
            padding: 120px 0;
            text-align: center;
        }
        .section-title {
            position: relative;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            color: var(--dark);
            font-size: 2rem;
        }
        .service-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: var(--transition);
            height: 100%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(138,109,59,0.15);
        }
        .service-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .price-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary);
            color: var(--white);
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
            z-index: 1;
        }
        .format-badge {
            display: inline-block;
            background: var(--secondary);
            color: var(--dark);
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
            border: 1px solid rgba(138,109,59,0.2);
        }
        .photo-format-card {
            background: var(--white);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: var(--transition);
            height: 100%;
        }
        .photo-format-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(138,109,59,0.15);
        }
        .photo-format-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .photo-format-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0;
        }
        .photo-format-price small {
            font-size: 0.9rem;
            color: #666;
            font-weight: 400;
        }
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 10px 30px;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-primary:hover {
            background-color: #7a5d2b;
            border-color: #7a5d2b;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(138,109,59,0.3);
        }
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
            color: var(--white);
        }
        .footer {
            background-color: var(--dark);
            color: var(--white);
            padding: 60px 0 30px;
        }
        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: var(--transition);
        }
        .footer-links a:hover {
            color: var(--white);
            padding-left: 5px;
        }
        .social-icons a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            margin-right: 10px;
            color: var(--white);
            transition: var(--transition);
        }
        .social-icons a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 40px;
            color: #aaa;
            font-size: 0.9rem;
        }
        .bg-soft {
            background-color: var(--secondary);
        }
        @media (max-width: 768px) {
            .hero-section { padding: 80px 0; }
            .hero-section h1 { font-size: 2rem; }
            .section-title { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="PhotosalonMain.php">
                <i class="fas fa-camera-retro me-2" style="color: var(--primary);"></i>ЯСАГК
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="DocumentPage.php">Документы</a></li>
                    <li class="nav-item"><a class="nav-link" href="ServicesListPage.php">Фотопечать</a></li>
                    <li class="nav-item"><a class="nav-link" href="GoodsListPage.php">Сувениры</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <h1>Фотопечать</h1>
            <p class="lead fs-4 mb-4">Профессиональная печать фотографий любых форматов</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="section-title">Форматы фотопечати</h2>
            <div class="row g-4">
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['ID'];
                        $name = htmlspecialchars($row['Name']);
                        $format = htmlspecialchars($row['Format']);
                        $price = $row['Price'];
                        $material = htmlspecialchars($row['Material']);
                        
                        $icon = 'fas fa-image';
                        if (stripos($name, 'USB') !== false || stripos($name, 'CD') !== false) {
                            $icon = 'fas fa-layer-group';
                        } elseif (stripos($name, 'постер') !== false) {
                            $icon = 'fas fa-images';
                        }
                        
                        $price_display = ($price > 0) ? $price . ' ₽' : 'по запросу';
                        $price_small = ($price > 0) ? '<small>/ шт</small>' : '';
                        
                        $materials_array = explode('|', $material);
                        $material_badges = '';
                        foreach ($materials_array as $mat) {
                            $material_badges .= '<span class="format-badge">' . trim(htmlspecialchars($mat)) . '</span>';
                        }
                ?>
                <div class="col-md-3">
                    <div class="photo-format-card">
                        <div class="photo-format-icon">
                            <i class="<?php echo $icon; ?>"></i>
                        </div>
                        <h4><?php echo $format; ?></h4>
                        <p><?php echo $name; ?></p>
                        <div class="photo-format-price">
                            <?php echo $price_display; ?>
                            <?php echo $price_small; ?>
                        </div>
                        <div class="material-badges">
                            <?php echo $material_badges; ?>
                        </div>
                        <div class="mt-3">
                            <a href="ServiceSamplePage.php?service_id=<?php echo $id; ?>" class="btn btn-outline-primary btn-sm">Заказать</a>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<div class="col-12"><p class="text-center">Услуги временно недоступны</p></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-soft">
        <div class="container">
            <h2 class="section-title">Вам также может понравиться</h2>
            <div class="row g-4">
            <?php
            $conn = new mysqli("localhost", "root", "", "photosalonramik");

            if ($conn->connect_error) {
                die("Ошибка подключения: " . $conn->connect_error);
            }

            $sql = "SELECT ID, Name, Price, ShortDescription, ImageURL, Category 
                    FROM goods 
                    WHERE IsAvailable = 1
                    ORDER BY RAND() 
                    LIMIT 4";
            $result = $conn->query($sql);
            ?>

            <div class="row g-4">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="col-md-3">
                            <div class="card service-card h-100 d-flex flex-column">
                                <div class="position-relative">
                                    <img src="<?php echo htmlspecialchars($row['ImageURL']); ?>" 
                                        class="card-img-top" 
                                        alt="<?php echo htmlspecialchars($row['Name']); ?>">
                                    <span class="price-badge"><?php echo $row['Price']; ?> ₽</span>
                                </div>
                                <div class="card-body d-flex flex-column flex-grow-1">
                                    <h5><?php echo htmlspecialchars($row['Name']); ?></h5>
                                    <p class="small text-muted flex-grow-1"><?php echo htmlspecialchars($row['ShortDescription']); ?></p>
                                    <a href="GoodsSamplePage.php?id=<?php echo $row['ID']; ?>" 
                                    class="btn btn-outline-primary btn-sm w-100 mt-auto">Подробнее</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center">Товары временно недоступны</p>
                    </div>
                <?php endif; ?>            
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5 class="mb-4">ЯСАГК</h5>
                    <p class="text-white-50">Мы в социальных сетях:</p>
                    <div class="social-icons mt-4">
                        <a href="https://ygk.edu.yar.ru/"><i class="fab fa-instagram"></i></a>
                        <a href="https://ygk.edu.yar.ru/"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://ygk.edu.yar.ru/"><i class="fab fa-vk"></i></a>
                        <a href="https://ygk.edu.yar.ru/"><i class="fab fa-telegram-plane"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5 class="mb-4">Навигация</h5>
                    <div class="footer-links d-flex flex-column">
                        <a href="PhotosalonMain.php" class="mb-2">Главная</a>
                        <a href="PhotosalonMain.php#services" class="mb-2">Преимущества</a>
                        <a href="PhotosalonMain.php#contact">Контакты</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h5 class="mb-4">Услуги печати</h5>
                    <div class="footer-links d-flex flex-column">
                        <a href="DocumentPage.php" class="mb-2">Документы</a>
                        <a href="ServicesListPage.php" class="mb-2">Фото</a>
                        <a href="GoodsListPage.php" class="mb-2">Сувениры</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="mb-4">Подписка</h5>
                    <p>Подпишитесь на рассылку, чтобы получать информацию об акциях и новостях.</p>
                    <form method="POST" action="">
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Ваш email" required>
                        <button class="btn btn-primary" type="submit" name="email_submit"><i class="fas fa-paper-plane"></i></button>
                    </div>
                    <?php echo $message; ?>
                </form>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center copyright">
                    <p>© 2026 Фотосалон "ЯСАГК". Все права защищены.</p>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>