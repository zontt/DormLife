<?php
// Start the session
session_start();

// Include database connection
require_once('../registration/db.php');

// Check the connection
if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}

// Check access permissions
$queryCheckPermission = "SELECT can_access FROM permissions WHERE role_id = 4 AND resource = 'admin_tab'";
$resultCheckPermission = mysqli_query($conn, $queryCheckPermission);

if ($resultCheckPermission && mysqli_num_rows($resultCheckPermission) > 0) {
    $rowCheckPermission = mysqli_fetch_assoc($resultCheckPermission);
    $canAccessAdminTab = $rowCheckPermission['can_access'];

    // Get user id from the session
    $user_id = $_SESSION['user_id'];

    // Check user role (administrator = 4)
    $queryUserRole = "SELECT role_id FROM users WHERE id = ?";
    $stmtUserRole = mysqli_prepare($conn, $queryUserRole);
    mysqli_stmt_bind_param($stmtUserRole, 'i', $user_id);
    mysqli_stmt_execute($stmtUserRole);
    $resultUserRole = mysqli_stmt_get_result($stmtUserRole);

    if ($resultUserRole && mysqli_num_rows($resultUserRole) > 0) {
        $rowUserRole = mysqli_fetch_assoc($resultUserRole);
        $userRole = $rowUserRole['role_id'];

        // Check if the user has access and is an administrator
        if ($canAccessAdminTab && $userRole == 4) {
            // Get user information
            $sql = "SELECT users.id, user_details.photo_path
                    FROM users
                    LEFT JOIN user_details ON users.id = user_details.user_id
                    WHERE users.id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'i', $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($result && $user = mysqli_fetch_assoc($result)) {
                // Choose photo from the user_details table if it exists
                $user_image = !empty($user['photo_path']) ? $user['photo_path'] : '../../img/default--avatar.png';

                // Check if the request is a POST request
                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    // Check if action and application_id are set in POST data
                    if ($_SERVER["REQUEST_METHOD"] === "POST") {
                        // Check if action and application_id are set in POST data
                        if (isset($_POST['action']) && isset($_POST['application_id'])) {
                            $action = $_POST['action'];
                            $applicationId = $_POST['application_id'];
                    
                            // ... інші дії ...
                    
                            // Вивід чистого JSON без обгортки <pre>
                            header('Content-Type: application/json');
                            echo json_encode(array("status" => "success", "message" => "Operation successful"));
                            exit(); // Важливо завершити виконання скрипта після виведення JSON
                        } else {
                            // Missing action or application_id in POST data
                            header('Content-Type: application/json');
                            echo json_encode(array("status" => "error", "message" => "Missing action or application_id in POST data"));
                            exit(); // Важливо завершити виконання скрипта після виведення JSON
                        }
                    }
                }
            } else {
                // Handle user information retrieval error
                echo json_encode(array("status" => "error", "message" => "User information retrieval error: " . mysqli_error($conn)));
            }
        } else {
            // User does not have sufficient permissions or is not an administrator
            echo json_encode(array("status" => "error", "message" => "You do not have sufficient permissions to view this page."));
        }
    } else {
        // Handle user role retrieval error
        echo json_encode(array("status" => "error", "message" => "User role retrieval error: " . mysqli_error($conn)));
    }
} else {
    // Handle permission retrieval error
    echo json_encode(array("status" => "error", "message" => "Permission retrieval error: " . mysqli_error($conn)));
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
    <title>Подані заяви</title>
    <link rel="icon" type="image/png" href="../../img/logo.ico">
</head>

<body>
    <section id="sidebar">
        <a href="#" class="brand">
            <svg class='bx bxs-smile' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 43 43" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M35.5445 10.3674C36.371 8.41922 34.4068 6.45501 32.4586 7.28151L8.51159 17.4409C6.51217 18.2891 6.63072 21.162 8.69322 21.8426L17.0769 24.6092C17.6160 24.7871 18.0389 25.21 18.2168 25.7492L20.9834 34.1328C21.6640 36.1953 24.5369 36.3138 25.3852 34.3144L35.5445 10.3674ZM31.0591 3.98276C35.9734 1.89792 40.9281 6.8526 38.8432 11.7669L28.6839 35.7139C26.5442 40.7574 19.2975 40.4584 17.5806 35.2557L15.0968 27.7292L7.57028 25.2454C2.36764 23.5286 2.06861 16.2818 7.11212 14.1421L31.0591 3.98276Z" />
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
                <a href="../application/index.php">
                    <i class='bx bx-pencil'></i>
                    <span class="text">Подати заявку</span>
                </a>
            </li>
        <ul class="side-menu">
            <?php
            // Відобразити вкладку "Подані заяви", якщо користувач має доступ і роль адміністратора
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
<div class="table-container">
    <p>Подані заяви<i class='bx bx-table'></i></p>
    <table id="myTable" class="order-column display compact scrollable-table" style="width:100%">
        <thead>
            <tr>
                <th>Дата подання</th>
                <th>Прізвище</th>
                <th>Ім'я</th>
                <th>По батькові</th>
                <th>Фото</th>
                <th>Дата народження</th>
                <th>Адреса реєстрації</th>
                <th>Домашня адреса</th>
                <th>Email</th>
                <th>Мобільний телефон</th>
                <th>Телефон батьків</th>
                <th>Номер гуртожитку</th>
                <th>Номер кімнати</th>
                <th>Форма фінансування</th>
                <th>Рівень освіти</th>
                <th>Спеціальність</th>
                <th>Група</th>
                <th>Академічний рік</th>
                <th>Соціальна категорія</th>
                <th>Попередні переплати</th>
                <th>Платіж на наступний рік</th>
                <th>Шлях до квитанції</th>
                <th>Вибір кімнат</th>
                <th>Шлях до файлу заяви</th>
                <th>ID користувача, що подав</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Отримання даних з таблиці studentapplication
            $queryApplications = "SELECT * FROM studentapplication";
            $resultApplications = mysqli_query($conn, $queryApplications);
            if (!$resultApplications) {
                die("Помилка: " . mysqli_error($conn));
            }

            while ($application = mysqli_fetch_assoc($resultApplications)) {
                echo "<tr>";
                echo "<td>{$application['submission_date']}</td>";
                echo "<td>{$application['last_name']}</td>";
                echo "<td>{$application['first_name']}</td>";
                echo "<td>{$application['middle_name']}</td>";
                echo "<td>{$application['photo_path']}</td>";
                echo "<td>{$application['birth_date']}</td>";
                echo "<td>{$application['registered_address']}</td>";
                echo "<td>{$application['home_address']}</td>";
                echo "<td>{$application['email']}</td>";
                echo "<td>{$application['mobile_phone']}</td>";
                echo "<td>{$application['parents_phone']}</td>";
                echo "<td>{$application['dormitory_number']}</td>";
                echo "<td>{$application['room_number']}</td>";
                echo "<td>{$application['tuition_funding']}</td>";
                echo "<td>{$application['education_level']}</td>";
                echo "<td>{$application['specialty']}</td>";
                echo "<td>{$application['group_code']}</td>";
                echo "<td>{$application['academic_year']}</td>";
                echo "<td>{$application['social_category']}</td>";
                echo "<td>{$application['previous_overpayment']}</td>";
                echo "<td>{$application['next_year_payment']}</td>";
                echo "<td>{$application['payment_receipt_path']}</td>";
                echo "<td>{$application['room_preferences']}</td>";
                echo "<td>{$application['application_upload_path']}</td>";
                echo "<td>{$application['submitted_by_user_id']}</td>";
                echo "<td>";
                echo "<button class='accept-button' data-application-id='" . $application['id'] . "'>Прийняти</button>";
                echo "<button class='reject-button' data-application-id='" . $application['id'] . "'>Відхилити</button>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

    </section>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>

    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script src="script.js"></script>
    <?php
    // Закриття з'єднання з базою даних
    mysqli_close($conn);
    ?>
</body>

</html>