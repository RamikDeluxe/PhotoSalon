<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'photosalonramik';

$link = mysqli_connect($host, $user, $pass, $name);

// Получаем ID услуги из URL
$service_id = isset($_GET['service_id']) ? (int)$_GET['service_id'] : 0;

// Получаем данные услуги из БД
$service_query = "SELECT ID, Name, Format, Price, Material FROM photoservices WHERE ID = $service_id";
$service_result = mysqli_query($link, $service_query);
$service = mysqli_fetch_assoc($service_result);

if (!$service) {
    header('Location: ServicesListPage.php');
    exit;
}

// Разбиваем материалы на массив для выбора в форме
$materials_array = explode('|', $service['Material']);
$materials_trimmed = array_map('trim', $materials_array);

// Обработка подписки на рассылку
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

// Обработка заказа на печать
$order_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_submit'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $phone = mysqli_real_escape_string($link, $_POST['phone']);
    $source = mysqli_real_escape_string($link, $_POST['source']);
    $count = (int)$_POST['count'];
    $format = mysqli_real_escape_string($link, $_POST['format']);
    $material = mysqli_real_escape_string($link, $_POST['material']);
    
    if (!empty($name) && !empty($phone) && !empty($source) && $count > 0 && !empty($format) && !empty($material)) {
        $query = "INSERT INTO onlineorders (Name, PhoneNumber, Source, Count, Format, Material) 
                  VALUES ('$name', '$phone', '$source', $count, '$format', '$material')";
        $res = mysqli_query($link, $query);
        
        if ($res) {
            $order_message = '<div class="alert alert-success mt-3">Заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.</div>';
        } else {
            $order_message = '<div class="alert alert-danger mt-3">Ошибка при отправке: ' . mysqli_error($link) . '</div>';
        }
    } else {
        $order_message = '<div class="alert alert-warning mt-3">Пожалуйста, заполните все поля формы.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['Name']); ?> - Фотосалон ЯСАГК</title>
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
        .sidebar-widget {
            background: var(--secondary);
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .sidebar-widget h4 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
            font-size: 1.3rem;
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
        @media (max-width: 768px) {
            .hero-section { padding: 80px 0; }
            .hero-section h1 { font-size: 2rem; }
            .section-title { font-size: 1.8rem; }
        }
        .bg-primary { background-color: var(--primary) !important; }
        .border-white { border-color: var(--white) !important; }
        .text-white { color: var(--white); }
        .format-info {
            background: var(--secondary);
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .format-info span {
            font-weight: bold;
            color: var(--primary);
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
            <h1><?php echo htmlspecialchars($service['Name']); ?></h1>
            <p class="lead fs-4 mb-4">Печать формата <?php echo htmlspecialchars($service['Format']); ?></p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-5">
                        <h2 class="section-title">Об услуге</h2>
                        <p class="lead mb-4"><?php echo htmlspecialchars($service['Name']); ?> - <?php echo htmlspecialchars($service['Format']); ?></p>
                        <p>Профессиональная печать фотографий формата <?php echo htmlspecialchars($service['Format']); ?> в фотосалоне "ЯСАГК". Мы используем современное оборудование и качественные расходные материалы для получения ярких и долговечных отпечатков.</p>
                        
                        <div class="format-info">
                            <p><span>📐 Формат:</span> <?php echo htmlspecialchars($service['Format']); ?></p>
                            <p><span>💰 Цена:</span> <?php echo $service['Price']; ?> ₽ за штуку</p>
                            <p><span>🎨 Доступные материалы:</span> <?php echo str_replace('|', ', ', htmlspecialchars($service['Material'])); ?></p>
                        </div>
                        
                        <p class="mt-4">Вы можете принести готовые фотографии на любом носителе (USB-флеш, диск, облачное хранилище) или отправить их нам онлайн. Срок изготовления - от 30 минут. Для больших заказов возможно изготовление в день обращения.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="sidebar-widget">
                        <h4>Запись на печать</h4>
                        <p class="small text-muted mb-3">Оставьте заявку, мы перезвоним в течение 15 минут</p>
                        
                        <?php echo $order_message; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <input type="text" name="name" class="form-control" placeholder="Ваше имя *" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" name="phone" class="form-control" placeholder="Телефон *" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="source" class="form-control" placeholder="Ссылка на фото *" required>
                            </div>
                            <div class="mb-3">
                                <select name="count" class="form-control" required>
                                    <option value="">Выберите количество фото</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select name="format" class="form-control" required>
                                    <option value="">Выберите формат</option>
                                    <option value="10x15" <?php echo ($service['Format'] == '10×15 см') ? 'selected' : ''; ?>>10×15 см</option>
                                    <option value="15x20" <?php echo ($service['Format'] == '15×20 см') ? 'selected' : ''; ?>>15×20 см</option>
                                    <option value="20x30" <?php echo ($service['Format'] == '20×30 см') ? 'selected' : ''; ?>>20×30 см</option>
                                    <option value="30x40" <?php echo ($service['Format'] == '30×40 см') ? 'selected' : ''; ?>>30×40 см</option>
                                    <option value="40x60" <?php echo ($service['Format'] == '40×60 см') ? 'selected' : ''; ?>>40×60 см</option>
                                    <option value="50x70" <?php echo ($service['Format'] == '50×70 см') ? 'selected' : ''; ?>>50×70 см</option>
                                    <option value="100x140" <?php echo ($service['Format'] == '100×140 см') ? 'selected' : ''; ?>>100×140 см</option>
                                    <option value="Любой" <?php echo ($service['Format'] == 'Любые форматы') ? 'selected' : ''; ?>>Любой формат</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select name="material" class="form-control" required>
                                    <option value="">Выберите материал</option>
                                    <?php foreach ($materials_trimmed as $material): ?>
                                        <option value="<?php echo htmlspecialchars($material); ?>">
                                            <?php echo htmlspecialchars($material); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="order_submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-2"></i>Отправить заявку
                            </button>
                        </form>
                    </div>
                    <div class="sidebar-widget">
                        <h4>Контакты</h4>
                        <div class="d-flex mb-3">
                            <i class="fas fa-phone-alt me-3" style="color:var(--primary); width:20px;"></i>
                            <div><strong>Телефон:</strong><br><a href="tel:+74951234567" class="text-dark text-decoration-none">+7 (495) 123-45-67<br>+7 (916) 987-65-43</a></div>
                        </div>
                        <div class="d-flex mb-3">
                            <i class="fas fa-envelope me-3" style="color:var(--primary); width:20px;"></i>
                            <div><strong>Email:</strong><br><a href="mailto:YGK@mail.ru" class="text-dark text-decoration-none">YGK@mail.ru</a></div>
                        </div>
                        <div class="d-flex mb-3">
                            <i class="fas fa-clock me-3" style="color:var(--primary); width:20px;"></i>
                            <div><strong>Часы работы:</strong><br>Пн-Пт: 10:00 - 20:00<br>Сб-Вс: 10:00 - 18:00</div>
                        </div>
                        <div class="d-flex">
                            <i class="fas fa-map-marker-alt me-3" style="color:var(--primary); width:20px;"></i>
                            <div><strong>Адрес:</strong><br>г. Ярославль, ул. Чайковского, 55</div>
                        </div>
                    </div>
                    <div class="sidebar-widget bg-primary text-white">
                        <h4 class="text-white border-white">Акция</h4>
                        <div class="text-center">
                            <i class="fas fa-gift fa-3x mb-3"></i>
                            <h5>Специальное предложение</h5>
                            <p>При заказе от 4 фотографий - скидка 15%</p>
                            <a class="btn btn-light">Записаться</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Вам также может понравиться</h2>
            <div class="row g-4 mt-3">
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