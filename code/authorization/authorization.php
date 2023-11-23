<?php
session_start();
require_once('../registration/db.php');

$error_message = ""; // Ініціалізуйте змінну для повідомлень про помилки

$reg_already = false;

// Перевірка з'єднання з базою даних
if ($conn->connect_errno) {
    $error_message = "Помилка з'єднання з базою даних: " . $conn->connect_error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$conn->connect_errno) {
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Захистіть введення електронної пошти
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Захистіть введення паролю

    $sql = "SELECT id, pass FROM `users` WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPasswordHash = $row['pass'];
    
        // Порівняйте хеш паролю збереженого в базі з хешем введеного паролю
        $isPasswordCorrect = password_verify($password, $storedPasswordHash);

    
        if ($isPasswordCorrect) {
            // Успішна аутентифікація. Встановіть сесію або прапорець аутентифікації.
            session_start();
            $_SESSION['user_id'] = $row['id']; // Збережіть ідентифікатор користувача в сесії.
            header("Location: ../home/index.php"); // Перенаправлення на головну сторінку користувача.
            exit();
        } else {
            $reg_already = 'wrong_password';
        }
    } else {
        $reg_already = 'user_not_found';
    }
    
    $_SESSION['reg_already'] = $reg_already;
    
    header("Location: index.php");
    exit();
}
?>
