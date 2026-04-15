/**
 * ============================================
 * КНОПКА "НАВЕРХ" (SCROLL TOP BUTTON)
 * Скрипт для плавной прокрутки страницы вверх
 * ============================================
 */

// Получаем элемент кнопки
const scrollTopBtn = document.getElementById('scrollTopBtn');

// Функция показа/скрытия кнопки в зависимости от прокрутки
function toggleScrollTopButton() {
    if (window.scrollY > 300) {
        scrollTopBtn.classList.add('show');
    } else {
        scrollTopBtn.classList.remove('show');
    }
}

// Функция плавной прокрутки наверх
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Добавляем обработчики событий
if (scrollTopBtn) {
    window.addEventListener('scroll', toggleScrollTopButton);
    scrollTopBtn.addEventListener('click', scrollToTop);
}