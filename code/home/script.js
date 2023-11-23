
document.addEventListener("DOMContentLoaded", function () {
    // Отримуємо посилання на елементи
    const menuBar = document.querySelector('#content nav .bx.bx-menu');
    const sidebar = document.getElementById('sidebar');
    const searchButton = document.querySelector('#content nav form .form-input button');
    const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
    const searchForm = document.querySelector('#content nav form');

    // Закриваємо меню, додавши клас "hide" при завантаженні сторінки
    sidebar.classList.add('hide');

    // Додаємо подію для кнопки відкриття/закриття меню
    menuBar.addEventListener('click', function () {
        sidebar.classList.toggle('hide');
    });

    // Логіка для зміни інтерфейсу при зміні розміру вікна
    if (window.innerWidth < 768) {
        sidebar.classList.add('hide');
    } else if (window.innerWidth > 576) {
    }
});



