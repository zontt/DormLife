<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once('db.php');

$email = $_POST['email'];
$password = $_POST['password'];
$repeatPassword = $_POST['confirm-password'];

$reg_already = false;
$success_message = false;
$error_message = false;

// Перевірка, чи користувач з такою поштою вже існує
$checkUserQuery = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
$checkUserQuery->bind_param("s", $email);
$checkUserQuery->execute();
$checkUserResult = $checkUserQuery->get_result();

if ($checkUserResult->num_rows > 0) {
    // Якщо користувач з такою поштою вже існує, встановити прапорець помилки реєстрації
    $reg_already = true;
    $_SESSION['reg_already'] = 'true';
} else {
    // Хешування паролю перед збереженням його в базу даних
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Додавання нового користувача до бази даних з хешованим паролем
    $sql = $conn->prepare("INSERT INTO `users` (email, pass, role_id) VALUES (?, ?, 1)");
    $sql->bind_param("ss", $email, $hashedPassword);

    if ($sql->execute()) {
        // Отримання ідентифікатора нового користувача
        $newUserId = $sql->insert_id;

        // Додавання запису в таблицю user_details з пустими полями
        $insertUserDetailsQuery = $conn->prepare("INSERT INTO `user_details` (user_id, full_name, phone_number, birth_date, photo_path) VALUES (?, '', '', '', '')");
        $insertUserDetailsQuery->bind_param("i", $newUserId);

        if ($insertUserDetailsQuery->execute()) {
            // Встановлення повідомлення про успішну реєстрацію
            $success_message = true;
            $_SESSION['success_message'] = 'true';
        } else {
            // Встановлення повідомлення про помилку
            $error_message = true;
            $_SESSION['error_message'] = 'Помилка при додаванні даних користувача: ' . $insertUserDetailsQuery->error; // Вивід конкретного повідомлення про помилку
        }

        $insertUserDetailsQuery->close();
    } else {
        // Встановлення повідомлення про помилку
        $error_message = true;
        $_SESSION['error_message'] = 'Помилка при додаванні користувача: ' . $sql->error; // Вивід конкретного повідомлення про помилку
    }

    $sql->close();
}

// Закриття з'єднання
$conn->close();

header("Location: index.php");
exit();
?>
