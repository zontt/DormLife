<?php
require_once('../registration/db.php'); // Додайте цей рядок на початку вашого index.php

// Отримуємо ідентифікатор користувача з сесії
session_start();
$user_id = $_SESSION['user_id'];

// Виконуємо запит до бази даних для отримання інформації про користувача
$sql = "SELECT users.id, user_details.photo_path
        FROM users
        LEFT JOIN user_details ON users.id = user_details.user_id
        WHERE users.id = $user_id";
$result = mysqli_query($conn, $sql);

if ($result) {
    $user = mysqli_fetch_assoc($result);

    // Вибираємо фото із таблиці user_details, якщо воно існує
    $user_image = !empty($user['photo_path']) ? $user['photo_path'] : '../../img/default--avatar.png';
}

// Перевірка дозволу
$queryCheckPermission = "SELECT can_access FROM permissions WHERE role_id = 4 AND resource = 'admin_tab'";
$stmtCheckPermission = mysqli_prepare($conn, $queryCheckPermission);
mysqli_stmt_execute($stmtCheckPermission);
$resultCheckPermission = mysqli_stmt_get_result($stmtCheckPermission);

if (!$resultCheckPermission || mysqli_num_rows($resultCheckPermission) == 0) {
    echo "Помилка: дозвіл не знайдено";
    exit;
}

$rowCheckPermission = mysqli_fetch_assoc($resultCheckPermission);
$canAccessAdminTab = $rowCheckPermission['can_access'];

// Отримання ролі користувача
$queryUserRole = "SELECT role_id FROM users WHERE id = ?";
$stmtUserRole = mysqli_prepare($conn, $queryUserRole);
mysqli_stmt_bind_param($stmtUserRole, "i", $user_id);
mysqli_stmt_execute($stmtUserRole);
$resultUserRole = mysqli_stmt_get_result($stmtUserRole);

if (!$resultUserRole || mysqli_num_rows($resultUserRole) == 0) {
    echo "Помилка: роль користувача не знайдена";
    exit;
}

$rowUserRole = mysqli_fetch_assoc($resultUserRole);
$userRole = $rowUserRole['role_id'];


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Створення контракту</title>
    <link rel="icon" type="image/png" href="../../img/logo.ico">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <section id="sidebar">
        <a href="#" class="brand">
            <svg class='bx bxs-smile' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43 43" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M35.5445 10.3674C36.371 8.41922 34.4068 6.45501 32.4586 7.28151L8.51159 17.4409C6.51217 18.2891 6.63072 21.162 8.69322 21.8426L17.0769 24.6092C17.616 24.7871 18.0389 25.21 18.2168 25.7492L20.9834 34.1328C21.6640 36.1953 24.5369 36.3138 25.3852 34.3144L35.5445 10.3674ZM31.0591 3.98276C35.9734 1.89792 40.9281 6.8526 38.8432 11.7669L28.6839 35.7139C26.5442 40.7574 19.2975 40.4584 17.5806 35.2557L15.0968 27.7292L7.57028 25.2454C2.36764 23.5286 2.06861 16.2818 7.11212 14.1421L31.0591 3.98276Z"/>
            </svg>
            <span class="text--logo">DormLife</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="../home/index.php">
                    <i class='bx bx-category'></i>
                    <span class="text">Головна</span>
                </a>
            </li>
            <li>
                <a href="index.php" class="active">
                    <i class='bx bx-pencil'></i>
                    <span class="text">Подати заявку</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
        <?php
        if ($canAccessAdminTab && $userRole == 4) {
            echo "<li>
                    <a href='../applications-inspection/index.php'>
                        <i class='bx bx-table'></i>
                        <span class='text'>Подані заяви</span>
                    </a>
                </li>";
        }
        ?>
			<li>
				<a href="../settings/index.php">
					<i class='bx bx-cog'></i>
					<span class="text">Налаштування</span>
				</a>
			</li>
			<li>
				<a href="../authorization/index.php" class="logout">
					<i class='bx bx-log-out'></i>
					<span class="text">Вийти</span>
				</a>
			</li>
		</ul>
    </section>
    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Категорії</a>
            <form action="#"></form>
            <a href="#" class="profile">
            <img src="<?php echo $user_image; ?>" alt="Аватар користувача">
			</a>
        </nav>
        <div class="application">
            <div class="headline"><p>Подати заяву</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" style="fill: rgba(28, 28, 28, 1);transform: ;msFilter:;"><path d="M4 21a1 1 0 0 0 .24 0l4-1a1 1 0 0 0 .47-.26L21 7.41a2 2 0 0 0 0-2.82L19.42 3a2 2 0 0 0-2.83 0L4.3 15.29a1.06 1.06 0 0 0-.27.47l-1 4A1 1 0 0 0 3.76 21 1 1 0 0 0 4 21zM18 4.41 19.59 6 18 7.59 16.42 6zM5.91 16.51 15 7.41 16.59 9l-9.1 9.1-2.11.52z"></path></svg>
            </div>
            <div class="container-subtitle">
                <div class="subtitle">
                  <a href="#" id="info" data-tab="infoTab" data-back="null" data-next="studentDataTab" onclick="navigateTab('infoTab')"><p>Інформація</p></a>
                  <i class='bx bx-chevron-right'></i>
                  <a href="#" id="student" data-tab="studentDataTab" data-back="infoTab" data-next="paymentTab" onclick="navigateTab('studentDataTab')"><p>Дані студента</p></a>
                  <i class='bx bx-chevron-right'></i>
                  <a href="#" id="payment" data-tab="paymentTab" data-back="studentDataTab" data-next="applicationTab" onclick="navigateTab('paymentTab')"><p>Оплата</p></a>
                  <i class='bx bx-chevron-right'></i>
                  <a href="#" id="application" data-tab="applicationTab" data-back="paymentTab" data-submit="true" onclick="navigateTab('applicationTab')"><p>Заява</p></a>
                </div>
              </div>
            
            <div class="line-p"><div class="line"></div></div>
            <form id="applicationForm" action="submit_application.php" method="post" enctype="multipart/form-data">
            <div id="infoTab" class="tab-content" style="display: flex;justify-content: center;">
                <div class="info">
                    <!-- <p>Детальна інформація та положення про поселення</p> <a href="https://nubip.edu.ua/node/10287">https://nubip.edu.ua/node/10287</a> -->
                    <div class="item">
                        <p>Шановні студенти! Дану форму створено з метою автоматизованого збору заяв на проживання в гуртожитку на 2023-2024 навчальний рік, ЇХ ТРЕБА ПОДАТИ ДО 17.06.2023 р. Вона включає:</p>
                        <p>1) заповнення анкети онлайн,</p>
                        <p>2) подачу фото написаної "від руки" заяви, за заповненим попередньо зразком в онлайн форму.</p>
                        <p>3) долучення чеку про оплату в формі ( Студенти-вступники на магістратуру можуть здійснити проплату після здачі вступних іспитів).</p>
                        <p>4) розгляд заяви комендантом та деканатом.</p>
                    </div>
                    <div class="item">
                        <p>У поселенні на наступний рік буде відмовлено студентам, які не ліквідували борг за поточний навчальний рік.</p>
                    </div>
                    <div class="item">
                        <p>Поселення в гуртожиток відбувається окремо на кожен навчальний рік. Для поселення  вступників достатньо виключно написання заяви та оплати, при поселенні студентів старших курсів враховується також дотримання правил проживання в гуртожитку (відсутність боргів по оплаті, порушень - дотримання чистоти та порядку в кімнатах, дотримання дисципліни, культури поведінки) та документів, що подаються при поселенні коменданту.</p>
                    </div>
                    <div class="item">
                        <p>Поточна вартість проживання в гуртожитку: 800 грн за місяць. Вартість оплати залежить від терміну поселення, який відрізняється для різних курсів.</p>
                        <p>Студенти 1-3 курсів  поселяються на період 14.08.23-26.07.24 - сума оплати 9150 грн.</p>
                        <p>Студенти 4 курсу поселяються на період  14.08.23-28.06.24 - сума оплати 8400 грн.</p>
                        <p>Студенти магістри 1 року поселяються на період 04.09.23-26.07.24 - сума оплати 8615 грн.</p>
                        <p>У випадки переплати в попередньому навчальному році необхідно  звернутись до бухгалтерії в кабінет 126 корпусу 3, уточнити суму переплати та відняти її від вказаної суми. В такому випадку прикріпляються 2 квитанції про оплату.</p>
                    </div>
                    <div class="item">
                        <p>РЕКВІЗИТИ: - Одержувач: Національний університет біоресурсів і природокористування України, Банк: Державна казначейська служба України м. Київ , ЄДРПОУ: 00493706, Реєстраційні рахунки відповідно: За проживання в гуртожитку - UA338201720313281002202016289 Призначення: ПІБ, за проживання в гуртожитку</p>
                    </div>
                    <div class="item">
                        <p>Заява подається разом з квитанцією про оплату!</p>
                        <p>У випадку наявності пільг на оплату, замість квитанції прикріплюється підписана заява на отримання пільги (без дати) та підтверджуючі документи.</p>
                    </div>
                    <div class="item">
                        <p>Стендовий приклад заяви (дати поселення та виселення вказувати згідно термінів поселення, вказаних вище у оплаті)</p>
                        <img src="../../img/заява.png" alt="Пиклад заяви">
                    </div>
                    <div class="item">
                        <p>Студентам з числа соц.категорій "учасники бойових дій та їх діти", "учасники Революції Гідності та їх діти"  незалежно від форми фінансування існує можливість знижки в оплаті за проживання у гуртожитку (100% або 50% знижка). ДЛЯ ЦЬОГО ТРЕБА ЗВЕРНУТИСЬ В ДЕКАНАТ І НАПИСАТИ ЗАЯВУ ПРО НАДАННЯ ПІЛЬГИ! Дану заяву прикріпити замість квитанції про оплату</p>
                    </div>
                    <div class="item">
                        <p>Студенти з числа дітей-сиріт та осіб, що позбавлені батьківського піклування мають право на повне державне матеріальне утримання. Для цього їм потрібно звернутися в соц.відділ НУБіП України.527-81-42</p>
                    </div>
                    <div class="item">
                        <p>Написану заяву, необхідно підписати у коменданта та представника деканату (декана або заступника), та подати у дану форму!</p>
                    </div>
                    <div class="item">
                         <p>ПІСЛЯ ПОЗИТИВНОГО РІШЕННЯ ФОРМУЄТЬСЯ НАКАЗ НА ПОСЕЛЕННЯ СТУДЕНТІВ - БУДЕ НАДІСЛАНО ЧЕРЕЗ СТАРОСТ. Далі студенти проходить процедуру поселення у гуртожитку в серпні</p>
                    </div>
                    <div class="item">
                        <p>Студенту для поселення в гуртожиток потрібно мати:</p>
                        <li>квитанцію про оплату за проживання в гуртожитку. Без наявності квитанції про оплату (оригінал або копія) за проживання у гуртожитку поселення студентів категорично забороняється;</li>
                        <li>копію паспорта та ідентифікаційного номеру;</li>
                        <li>3 фотокартки розміром 3х4;</li>
                        <li>студенти 1 курсу і магістри першого року навчання обов’язково мають зареєструватися за місцем фактичного проживання, тобто в гуртожитку. При наявності паспорта громадянина України старого зразку (в формі книжечки) студенти приїжджають на поселення з відміткою в паспорті про зняття з домашнього місця реєстрації (також можна одночасно знятися із реєстрації та зареєструватися за новим місцем проживання у паспортному відділені університету), студенти які мають паспорт в формі ID-карти повинні мати при собі довідку про зняття з місця реєстрації Ф – 13;</li>
                        <li>студенти чоловічої статті – приписне свідоцтво з відміткою про зняття з військового обліку за попереднім місцем проживання;</li>
                        <li>довідку про проходження флюорографії та довідку про стан санітарно-епідеміологічного оточення дитини. Довідка надається сімейним лікарем, в якій вказується епідеміологічний стан дитини (наявність/відсутність педикульозу, шкірних захворювань, що протягом 21 дня не було контактів з людьми, які хворіють інфекційними хворобами та не перебуває на самоізоляції, тощо).</li>
                    </div>
                    <div class="item">
                        <p>Детальна інформація та положення про поселення</p>
                        <a href="https://nubip.edu.ua/node/10287">https://nubip.edu.ua/node/10287</a>
                    </div>
                    <div class="item">
                        <p>Якщо Вам все зрозуміло, переходимо до внесення даних</p>
                    </div>
                    <div class="control">
                        <div class="checkbox-wrapper-4">
                            <input class="inp-cbx" id="morning" type="checkbox"/>
                            <label class="cbx" for="morning"><span>
                            <svg width="12px" height="10px">
                              <use xlink:href="#check-4"></use>
                            </svg></span><span>Погоджуюсь з правилами подачі заяви</span></label>
                            <svg class="inline-svg">
                              <symbol id="check-4" viewbox="0 0 12 10">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                              </symbol>
                            </svg>
                        </div>
                        <button id="nextButton" onclick="navigateTab('studentDataTab')" class="button--n">Далі</button>
                    </div>
                    
                </div>
            </div>
            
            <div id="studentDataTab" class="tab-content" style="display: flex;justify-content: center;">
                <div class="info">
                    
                        <div class="item">
                            <p>Вказані дані використовуватимуться при формуванні наказу! Обов'язково їх перевіряйте перед відправкою</p>
                        </div>
                        <div class="item">
                            <label for="last_name">Прізвище:</label>
                            <input type="text" id="last_name" name="last_name" required placeholder="Ведіть дані">
                        </div>
                        
                        <div class="item">
                            <label for="first_name">Ім’я:</label>
                            <input type="text" id="first_name" name="first_name" required placeholder="Ведіть дані">
                        </div>
                        
                        <div class="item">
                            <label for="middle_name">По-батькові:</label>
                            <input type="text" id="middle_name" name="middle_name" required placeholder="Ведіть дані">
                        </div>
                        <div class="item">
                            <label for="photo_path">Фото студента:</label>
                            <input type="file" name="photo_path" required>
                            <small><p>Оберіть зображення 3:4</p></small>
                        </div>
                        <div class="item">
                            <label for="birth_date">Дата народження:</label>
                            <input type="date" id="birth_date" name="birth_date" required>
                        </div>
                        <div class="item">
                            <label for="registered_address">Зареєестрована адреса:</label>
                            <input type="text" id="registered_address" name="registered_address" required placeholder="Ведіть дані"">
                        </div>
                        <div class="item">
                            <label for="home_address">Домашня адреса:</label>
                            <input type="text" id="home_address" name="home_address" required placeholder="Ведіть дані">
                        </div>
                        <div class="item">
                            <label for="email">Електронна пошта:</label>
                            <input type="email" id="email" name="email" required placeholder="Ведіть дані">
                        </div>
                        <div class="item">
                            <label for="mobile_phone">Моб.телефон (власний):</label>
                            <input type="tel" id="mobile_phone" name="mobile_phone" required placeholder="Моб.телефон (власний)" pattern="[0-9]+" title="Будь ласка, введіть тільки цифри та символ +">
                        </div>
                        
                        <div class="item">
                            <label for="parents_phone">Моб.телефон (батьків):</label>
                            <input type="tel" id="parents_phone" name="parents_phone" required placeholder="Моб.телефон (батьків)" pattern="[0-9]+" title="Будь ласка, введіть тільки цифри та символ +">
                        </div>
                        <div class="item">
                            <label for="dormitory_number">№ гуртожитку:</label>
                            <select id="dormitory_number" name="dormitory_number" required>
                                <option value="" selected disabled hidden>Внесіть дані</option>
                                <option value="№7">№7</option>
                                <option value="№10">№10</option>
                            </select>
                        </div>
                        <div class="item">
                            <label for="parents_phone">Бажана кімната для проживання (№):</label>
                            <input type="text" id="room_number" name="room_number" required placeholder="Ведіть дані">
                        </div>
                        <div class="item">
                            <label for="tuition_funding">Форма фінансування навчання:</label>
                            <select id="tuition_funding" name="tuition_funding" required>
                                <option value="" selected disabled hidden>Внесіть дані</option>
                                <option value="Контракт">Контракт</option>
                                <option value="Бюджет">Бюджет</option>
                            </select>
                        </div>
                        <div class="item">
                            <label for="education_level">Освітній ступінь:</label>
                            <select id="education_level" name="education_level">
                                <option value="" selected disabled hidden>Внесіть дані</option>
                                <option value="Бакалавр">Бакалавр</option>
                                <option value="Магістр">Магістр</option>
                            </select>
                        </div>
                        <div class="item">
                            <label for="specialty">Спеціальність:</label>
                            <select id="specialty" name="specialty">
                                <option value="" selected disabled hidden>Внесіть дані</option>
                                <option value="126">126 Інформаційні системи та технології</option>
                            </select>
                        </div>
                        <div class="item">
                            <label for="parents_phone">Шифр групи (з вказанням спеціальності):</label>
                            <input type="text" id="group_code" name="group_code" required placeholder="Ведіть дані">
                        </div>
                        <div class="item">
                            <label for="academic_year">Курс навчання у 2023-2024 навчальному році:</label>
                            <select id="academic_year" name="academic_year">
                                <option value="" selected disabled hidden>Внесіть дані</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="М1">М1</option>
                                <option value="М2">М2</option>
                                <option value="2ск">2ск</option>
                                <option value="3sk">3sk</option>
                            </select>
                        </div>
                        <div class="item">
                            <label for="social_category">Чи належите Ви до певної соціальної категорії?</label>
                            <select id="social_category" name="social_category">
                                <option value="" selected disabled hidden>Внесіть дані</option>
                                <option value="Так">Так</option>
                                <option value="Ні">Ні</option>
                            </select>
                        </div>
                        <div class="control-l">
                            <button id="prevButton" onclick="navigateTab('infoTab')" class="button--l">Назад</button>
                            <button id="nextButton" onclick="navigateTab('paymentTab')" class="button--r">Далі</button>
                        </div>
                    
                </div>
            </div>
                        
            <div id="paymentTab" class="tab-content" style="display: flex;justify-content: center;">
                <div class="info">
                    <div class="item">
                        <label for="previous_overpayment">Вкажіть суму переплати за попередній навчальний рік (дані стосовно переплати можна отримати у бухгалтерії в кабінетів 126 корпусу 3). Якщо переплата відсутня, вкажіть 0</label>
                        <input type="number" id="previous_overpayment" name="previous_overpayment" placeholder="Вкажіть суму переплати (якщо відсутня, вкажіть 0)" required>
                    </div>
                
                    <div class="item">
                        <label for="next_year_payment">Вкажіть суму доплати на наступний навчальний рік (якщо переплата була відсутня, вказується повна сума оплати). Вказана сума оплати повинна бути за весь навчальний рік, та збігатися із сумою у квитанції</label>
                        <input type="number" id="next_year_payment" name="next_year_payment" placeholder="Вкажіть суму доплати за весь наступний навчальний рік" required>
                    </div>
                
                    <div class="item">
                        <label for="payment_receipt">Платіжний чек:</label>
                        <input type="file" id="payment_receipt" name="payment_receipt[]" multiple accept=".pdf, .doc, .docx" required>
                        <small><p>Оберіть один або кілька файлів (.pdf, .doc, .docx)</p></small>
                    </div>
                    <div class="control-l">
                        <button id="prevButton" onclick="navigateTab('studentDataTab')" class="button--l">Назад</button>
                        <button id="nextButton" onclick="navigateTab('applicationTab')" class="button--r">Далі</button>                  
                    </div>
                </div>
            </div>

            <div id="applicationTab" class="tab-content" style="display: flex;justify-content: center;">
                <div class="info">
                    <div class="item">
                        <p>Згідно заповнених даних та стендового прикладу  заповніть, будь ласка, письмову заяву та завантажіть її в дану форму (формат PDF або зображення)</p>
                    </div>
                    <div class="item">
                        <label for="application_upload">Заява:</label> 
                        <input type="file" id="application_upload" name="application_upload[]" multiple accept=".pdf, .jpg, .jpeg, .png" required>
                        <small><p>Оберіть один або кілька файлів (.pdf, .jpg, .jpeg, .png)</p></small>
                    </div>
                    <div class="item">
                        <label for="room_preferences">Вкажіть побажання щодо сусідів по кімнаті або інші коментарі:</label>
                        <textarea id="room_preferences" name="room_preferences" rows="4" placeholder="Ваші побажання або коментарі"></textarea>
                    </div>
                    <div class="item">
                        <p>Додаткова інформація для пільговиків</p>
                        <p>ПЕРЕЛІК ПІЛЬГОВИКІВ І ПРОЦЕДУРА</p>
                        <a href="https://drive.google.com/file/d/1v-bIhdrtBfl-kKQ_ji0jUx8jkw5SBt9u/view?usp=sharing">https://drive.google.com/file/d/1v-bIhdrtBfl-kKQ_ji0jUx8jkw5SBt9u/view?usp=sharing</a>
                        <p>Зразок заяви для пільговиків (заяви подаються в деканат на пошту fit@nubip.edu.ua до 30.06)</p>
                        <a href="https://drive.google.com/file/d/1CWI1tcTulmPPbWDgbTY03ZIIBx_Tr5X6/view?usp=sharing">https://drive.google.com/file/d/1CWI1tcTulmPPbWDgbTY03ZIIBx_Tr5X6/view?usp=sharing</a>
                    </div>
                    <div class="item">
                        <p>За виявлення проблем з Вами зв'яжуться співробітники деканату!</p>
                    </div>
                    <div class="control-l">
                        <button id="prevButton" onclick="navigateTab('paymentTab')" class="button--l">Назад</button>
                        <button id="submitButton" type="submit" class="button--s">Подати заяву <i class='bx bx-check'></i></button>               
                    </div>                 
                </div>
            </div>
            </form>
        </div>
    </section>
    
    <script src="script.js"></script>
</body>
</html>
