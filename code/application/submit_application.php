<?php
require_once('../registration/db.php');
session_start();

// Отримуємо ідентифікатор користувача з сесії
$submitted_by_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Обробка даних форми
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Витягуємо дані з форми
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $middle_name = mysqli_real_escape_string($conn, $_POST["middle_name"]);

    // Check if the file input field exists in the form
    $photo_paths = isset($_FILES["photo_path"]) ? uploadFile($_FILES["photo_path"], "uploads/photos/") : [];

    $birth_date = $_POST["birth_date"];
    $registered_address = mysqli_real_escape_string($conn, $_POST["registered_address"]);
    $home_address = mysqli_real_escape_string($conn, $_POST["home_address"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $mobile_phone = mysqli_real_escape_string($conn, $_POST["mobile_phone"]);
    $parents_phone = mysqli_real_escape_string($conn, $_POST["parents_phone"]);
    $dormitory_number = mysqli_real_escape_string($conn, $_POST["dormitory_number"]);
    $room_number = mysqli_real_escape_string($conn, $_POST["room_number"]);
    $tuition_funding = mysqli_real_escape_string($conn, $_POST["tuition_funding"]);
    $education_level = mysqli_real_escape_string($conn, $_POST["education_level"]);
    $specialty = mysqli_real_escape_string($conn, $_POST["specialty"]);
    $group_code = mysqli_real_escape_string($conn, $_POST["group_code"]);
    $academic_year = mysqli_real_escape_string($conn, $_POST["academic_year"]);
    $social_category = mysqli_real_escape_string($conn, $_POST["social_category"]);
    $previous_overpayment = $_POST["previous_overpayment"];
    $next_year_payment = $_POST["next_year_payment"];
    
    // Check if the file input field exists in the form
    $payment_receipt_paths = isset($_FILES["payment_receipt"]) ? uploadFile($_FILES["payment_receipt"], "uploads/payment_receipts/") : [];

    $room_preferences = mysqli_real_escape_string($conn, $_POST["room_preferences"]);
    
    // Check if the file input field exists in the form
    $application_upload_paths = isset($_FILES["application_upload"]) ? uploadFile($_FILES["application_upload"], "uploads/applications/") : [];

    // Обробка випадків, коли $photo_paths та $application_upload_paths не є масивами
    $photo_path = !empty($photo_paths) ? $photo_paths[0] : '';
    $application_upload_path = !empty($application_upload_paths) ? $application_upload_paths[0] : '';

    // Отримуємо поточну дату та час
    $submission_date = date("Y-m-d H:i:s");

    // SQL-запит для вставки даних у базу даних
    $sql = "INSERT INTO StudentApplication (submitted_by_user_id, last_name, first_name, middle_name, photo_path, birth_date, registered_address, home_address, email, mobile_phone, parents_phone, dormitory_number, room_number, tuition_funding, education_level, specialty, group_code, academic_year, social_category, previous_overpayment, next_year_payment, payment_receipt_path, room_preferences, application_upload_path, submission_date)
    VALUES ('$submitted_by_user_id', '$last_name', '$first_name', '$middle_name', '$photo_path', '$birth_date', '$registered_address', '$home_address', '$email', '$mobile_phone', '$parents_phone', '$dormitory_number', '$room_number', '$tuition_funding', '$education_level', '$specialty', '$group_code', '$academic_year', '$social_category', '$previous_overpayment', '$next_year_payment', '" . implode(",", $payment_receipt_paths) . "', '$room_preferences', '$application_upload_path', '$submission_date')";

    if ($conn->query($sql) === TRUE) {
        echo "Дані форми успішно вставлені";
    } else {
        echo "Помилка: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();

function uploadFile($files, $targetDirectory) {
    $uploadedFiles = [];

    if (is_array($files["name"])) {
        foreach ($files["name"] as $key => $name) {
            if ($files["error"][$key] === UPLOAD_ERR_OK) {
                $targetFile = $targetDirectory . basename($name);

                if (move_uploaded_file($files["tmp_name"][$key], $targetFile)) {
                    $uploadedFiles[] = $targetFile;
                } else {
                    // Помилка при переміщенні файлу, пропустити його і продовжити
                    echo "Помилка при переміщенні файлу " . $name . ": " . $files["error"][$key];
                }
            } else {
                // Помилка при завантаженні файлу, пропустити його і продовжити
                echo "Помилка при завантаженні файлу " . $name . ": " . $files["error"][$key];
            }
        }
    } else {
        // Якщо файл не є масивом
        if ($files["error"] === UPLOAD_ERR_OK) {
            $targetFile = $targetDirectory . basename($files["name"]);

            if (move_uploaded_file($files["tmp_name"], $targetFile)) {
                $uploadedFiles[] = $targetFile;
            } else {
                // Помилка при переміщенні файлу, пропустити його і продовжити
                echo "Помилка при переміщенні файлу " . $files["name"] . ": " . $files["error"];
            }
        } else {
            // Помилка при завантаженні файлу, пропустити його і продовжити
            echo "Помилка при завантаженні файлу " . $files["name"] . ": " . $files["error"];
        }
    }

    return $uploadedFiles;
}
?>
