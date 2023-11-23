// Об'явлення змінних для збереження посилань на вкладки
let tabs;


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
        searchButtonIcon.classList.replace('bx-x', 'bx-search');
        searchForm.classList.remove('show');
    }
});



// Функція для приховання всіх вкладок
function hideTabs() {
  for (let key in tabs) {
    tabs[key].style.display = 'none';
  }
}

// Функція для відображення вкладки за її ідентифікатором
function showTab(tabId) {
  const checkbox = document.getElementById('morning');
  if (!checkbox.checked) {
    alert('Будь ласка, погодьтеся з умовами, перш ніж продовжити.');
    return;
  }

  hideTabs();
  tabs[tabId].style.display = 'flex';
  updateButtons(tabId);
}

// Функція для перемикання між вкладками
function navigateTab(targetTab) {
  showTab(targetTab);
}

// Функція для отримання поточної вкладки
function getCurrentTab() {
  for (let tabId in tabs) {
    if (tabs[tabId].style.display === 'flex') {
      return tabId;
    }
  }
}

// Функція для отримання наступної вкладки
function getNextTab(currentTab) {
  const tabIds = Object.keys(tabs);
  const currentIndex = tabIds.indexOf(currentTab);
  return tabIds[currentIndex + 1];
}

// Функція для отримання попередньої вкладки
function getPreviousTab(currentTab) {
  const tabIds = Object.keys(tabs);
  const currentIndex = tabIds.indexOf(currentTab);
  return tabIds[currentIndex - 1];
}

// Функція для оновлення стану кнопок
function updateButtons(currentTab) {
  const prevButton = document.getElementById('prevButton' + currentTab.charAt(0).toUpperCase() + currentTab.slice(1));
  const nextButton = document.getElementById('nextButton' + currentTab.charAt(0).toUpperCase() + currentTab.slice(1));

  if (prevButton && nextButton) {
    const hasPreviousTab = getPreviousTab(currentTab) !== undefined;
    const hasNextTab = getNextTab(currentTab) !== undefined;

    prevButton.disabled = !hasPreviousTab;
    nextButton.disabled = !hasNextTab;
  }
}

// Ініціалізація об'єкта вкладок
tabs = {
  'infoTab': document.getElementById('infoTab'),
  'studentDataTab': document.getElementById('studentDataTab'),
  'paymentTab': document.getElementById('paymentTab'),
  'applicationTab': document.getElementById('applicationTab')
};

// Приховання вкладок при завантаженні сторінки
hideTabs();

// Відображення початкової вкладки
tabs['infoTab'].style.display = 'flex';

// Оновлення стану кнопок для початкової вкладки
updateButtons('infoTab');

