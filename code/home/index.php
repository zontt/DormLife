<?php
require_once('../registration/db.php');
session_start();

// Перевірка дійсності сесії користувача
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authorization/index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Використання підготовлених запитів для запобігання SQL-ін'єкціям
$sql = "SELECT users.id, user_details.photo_path
        FROM users
        LEFT JOIN user_details ON users.id = user_details.user_id
        WHERE users.id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Помилка: користувач не знайдений";
    exit;
}

$user = mysqli_fetch_assoc($result);
$user_image = !empty($user['photo_path']) ? $user['photo_path'] : '../../img/default--avatar.png';

// Використання підготовлених запитів для інших запитань

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
    <title>Home DormLife</title>
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
            <a href="index.php">
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

    <div class='pass'>
    <?php
    $queryGetStudentData = "SELECT last_name, first_name, middle_name, specialty, dormitory_number, room_number, photo_path
                            FROM accepted_applications
                            WHERE submitted_by_user_id = $user_id";
    $resultGetStudentData = mysqli_query($conn, $queryGetStudentData);

    if (!$resultGetStudentData) {
        echo "Error: " . mysqli_error($conn);
        exit;
    }

    if (mysqli_num_rows($resultGetStudentData) > 0) {
        $studentData = mysqli_fetch_assoc($resultGetStudentData);
        $student_image = !empty($studentData['photo_path']) ? '/DormLife/code/application/' . $studentData['photo_path'] : "/DormLife/code/application/";

        echo "<div class='photo_path'><img src='$student_image' alt='Фото студента'></div>";
        echo "<div class='text-pass'><p>Студент: {$studentData['last_name']} {$studentData['first_name']} {$studentData['middle_name']}</p>";
        echo "<p>Факультет: " . ($studentData['specialty'] == 126 ? 'Інформаційних технологій' : $studentData['specialty']) . "</p>";
        echo "<div class='dorm-room'>
                <p>Гуртожиток: {$studentData['dormitory_number']}</p>
                <p>Кімната: {$studentData['room_number']}</p>
              </div>";
    } else {
        echo "<div class='not-pass'>
                <p>Вашу перепустку не знайдено</p>
                <a href='../application/index.php' class='butt-pass'>Подати заяву</a>
              </div>";
    }
?>

</div>



    <script src="script.js"></script>
</body>
</html>
<?php
exit;
?>
