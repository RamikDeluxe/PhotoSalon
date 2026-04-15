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

$feedback_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_submit'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $phone = mysqli_real_escape_string($link, $_POST['phone']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $message_text = mysqli_real_escape_string($link, $_POST['message']);
    
    if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($message_text)) {
        $query = "INSERT INTO feedback (Name, Phone, Email, Message, Status) 
                  VALUES ('$name', '$phone', '$email', '$message_text','new')";
        $res = mysqli_query($link, $query);
        
        if ($res) {
            $feedback_message = '<div class="alert alert-success mt-3">Спасибо за ваш отзыв! Мы обязательно его рассмотрим.</div>';
        } else {
            $feedback_message = '<div class="alert alert-danger mt-3">Ошибка при отправке: ' . mysqli_error($link) . '</div>';
        }
    } else {
        $feedback_message = '<div class="alert alert-warning mt-3">Пожалуйста, заполните все обязательные поля (Имя, Email, Сообщение).</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="arrow.css">
    <style>
        :root {
            --primary: #8a6d3b;
            --secondary: #f9f1e3;
            --dark: #333;
            --light: #f8f9fa;
            --white: #fff;
            --transition: 0.3s ease;
        }

        body {
            font-family: 'Roboto', sans-serif;
            color: var(--dark);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, .navbar-brand {
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
            transition: color var(--transition);
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--primary) !important;
        }

        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7));
            color: var(--white);
            padding: 35px 0 120px;
            text-align: center;
        }
        .hero-section h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }

        .section-title {
            position: relative;
            margin-bottom: 3rem;
            padding-bottom: 1rem;
            color: var(--dark);
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--primary);
        }

        /* ========== АНИМАЦИЯ КАРТОЧЕК ========== */
        .service-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            transition: transform var(--transition), box-shadow var(--transition);
            height: 100%;
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease, box-shadow 0.3s;
        }
        .service-card.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .service-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .contact-info {
            background-color: var(--secondary);
            padding: 30px;
            border-radius: 10px;
            height: 100%;
        }
        .contact-icon {
            font-size: 1.5rem;
            color: var(--primary);
            margin-right: 10px;
            width: 40px;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 10px 30px;
            font-weight: 500;
            transition: all var(--transition);
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
            transition: color var(--transition), padding-left var(--transition);
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
            transition: all var(--transition);
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

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(138,109,59,0.25);
        }

        .about-section {
            background-color: var(--secondary);
            padding: 80px 0;
        }
        .about-image {
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 120px 0 80px;
            }
            .hero-section h1 {
                font-size: 2.5rem;
            }
            .hero-section p {
                font-size: 1.1rem;
            }
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
            <h1>Самая быстрая печать</h1>
            <p class="lead fs-4 mb-4">Документы, фотографии, цветные и нет, всё напечатаем в нашем фотосалоне</p>
            <a href="ServicesListPage.php" class="btn btn-outline-light btn-lg">Записаться онлайн</a>
        </div>
    </section>

    <section class="py-5" id="services">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Наши преимущества</h2>
            </div>
            <div class="row g-4 mt-2">
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card shadow-sm p-4 text-center">
                        <div class="service-icon"><i class="fas fa-check-circle"></i></div>
                        <h4>Скорость</h4>
                        <p>Мы начинаем работать над вашими фото как только вы делаете заказ.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card shadow-sm p-4 text-center">
                        <div class="service-icon"><i class="fas fa-check-circle"></i></div>
                        <h4>Продвинутость</h4>
                        <p>Мы работаем в онлайн форматах и постоянно совершенствуем дизайн.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card shadow-sm p-4 text-center">
                        <div class="service-icon"><i class="fas fa-check-circle"></i></div>
                        <h4>Универсальность</h4>
                        <p>Мы работаем с большинством форматов, включая пользовательские.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card shadow-sm p-4 text-center">
                        <div class="service-icon"><i class="fas fa-check-circle"></i></div>
                        <h4>Эффективность</h4>
                        <p>Вам не нужно далеко ехать за фото, у нас есть салоны по всему городу.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card shadow-sm p-4 text-center">
                        <div class="service-icon"><i class="fas fa-check-circle"></i></div>
                        <h4>Доступность</h4>
                        <p>Мы работаем с любым числом фотографий.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card service-card shadow-sm p-4 text-center">
                        <div class="service-icon"><i class="fas fa-check-circle"></i></div>
                        <h4>Быстрая Доставка</h4>
                        <p>Если вам срочно нужны фотографии, вы можете воспользоваться доставкой.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="contact">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Контакты и обратная связь</h2>
                <p class="mb-5">Оставьте нам сообщение о том, что вам понравилось или не понравилось</p>
            </div>
            <div class="row mt-4">
                <div class="col-lg-8 mb-5 mb-lg-0">
                    <?php echo $feedback_message; ?>
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Ваше имя *</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Иван Иванов" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="tel" name="phone" class="form-control" id="phone" placeholder="+7 (999) 123-45-67">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" id="email" placeholder="example@mail.ru" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Сообщение *</label>
                            <textarea class="form-control" name="message" id="message" rows="5" placeholder="Ваши комментарии к печати..." required></textarea>
                        </div>
                        <button type="submit" name="feedback_submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-paper-plane me-2"></i>Отправить отзыв
                        </button>
                    </form>
                </div>
                <div class="col-lg-4">
                    <div class="contact-info">
                        <h4 class="mb-4">Контактная информация</h4>
                        <div class="d-flex mb-4">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div><h5>Адрес главного офиса</h5><p class="mb-0">г. Ярославль, ул. Чайковского, 55</p></div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                            <div><h5>Телефон</h5><p class="mb-0">+7 (495) 123-45-67</p><p>+7 (916) 987-65-43</p></div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                            <div><h5>Email</h5><p>YGK@mail.ru</p></div>
                        </div>
                        <div class="d-flex mb-4">
                            <div class="contact-icon"><i class="fas fa-clock"></i></div>
                            <div><h5>Часы работы</h5><p class="mb-0">Пн-Пт: 10:00 - 20:00</p><p>Сб-Вс: 10:00 - 18:00</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h2 class="section-title">О нашем фотосалоне</h2>
                    <p class="mb-4">Фотосалон "ЯСАГК" работает на рынке печати уже более 10 лет. За это время мы заслужили репутацию надежного партнера, который создает качественные и эмоциональные фотографии.</p>
                    <p class="mb-4">Мы используем современное профессиональное оборудование и программное обеспечение, что позволяет нам создавать фотографии высочайшего качества в различных стилях и жанрах.</p>
                </div>
                <div class="col-lg-6">
                    <img src="https://ortgraph.ru/upload/medialibrary/b52/b52fe4bec65e1f0ab5b17b5d6abdddaa.jpg" alt="Наша студия" class="img-fluid about-image">
                </div>
            </div>
        </div>
    </section>

    <button class="scroll-top-btn" id="scrollTopBtn">
        <i class="fas fa-arrow-up"></i>
    </button>

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
    <script src="arrow.js"></script>
    <script>
        const cards = document.querySelectorAll('.service-card');

        function isElementInViewport(el) {
            const rect = el.getBoundingClientRect();
            return rect.top <= window.innerHeight - 100;
        }

        function checkCardsVisibility() {
            cards.forEach(card => {
                if (isElementInViewport(card) && !card.classList.contains('visible')) {
                    card.classList.add('visible');
                }
            });
        }
        
        window.addEventListener('load', checkCardsVisibility);
        window.addEventListener('scroll', checkCardsVisibility);
        
        setTimeout(() => {
            cards.forEach(card => {
                if (isElementInViewport(card)) {
                    card.classList.add('visible');
                }
            });
        }, 100);
    </script>
</body>
</html>