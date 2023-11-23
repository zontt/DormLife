<?php
// Підключення до бази даних
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'registerUser';

$conn = mysqli_connect($servername, $username, $password, $dbname);

require_once('../registration/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Отримати user_id з сесії
    session_start();
    $user_id = $_SESSION['user_id'];

    $full_name = $_POST["full_name"];
    $phone_number = $_POST["phone_number"];
    $birth_date = $_POST["birth_date"];

    // Завантаження фотографії
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["photo_path"]["name"]);

    if (move_uploaded_file($_FILES["photo_path"]["tmp_name"], $target_file)) {
        // Файл був успішно завантажений
        // Оновлення даних в таблиці user_details на основі user_id
        $sql = "UPDATE user_details SET full_name = ?, phone_number = ?, birth_date = ?, photo_path = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $full_name, $phone_number, $birth_date, $target_file, $user_id);

        if ($stmt->execute()) {
            echo "Дані успішно оновлено.";
        } else {
            echo "Помилка при оновленні даних: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "Помилка при завантаженні фотографії.";
    }
}


$conn->close();
?>
