<?php
// process_application.php

// Прийняття даних від Ajax-запиту
$action = isset($_POST['action']) ? $_POST['action'] : '';
$applicationId = isset($_POST['application_id']) ? $_POST['application_id'] : '';

// Підключення до бази даних
require_once('../registration/db.php');

// Перевірка доступу до бази даних
if (!$conn) {
    echo json_encode(array("status" => "error", "message" => "Connection error: " . mysqli_connect_error()));
    exit();
}

// Логіка обробки даних
if ($action === 'accept') {
    // Логіка для прийняття заявки
    acceptApplication($applicationId, $conn);

    echo json_encode(array("status" => "success", "message" => "Application accepted"));
} elseif ($action === 'reject') {
    // Логіка для відхилення заявки
    rejectApplication($applicationId, $conn);

    echo json_encode(array("status" => "success", "message" => "Application rejected"));
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid action"));
}

// Закриття з'єднання з базою даних
mysqli_close($conn);

// Завершити виконання скрипта після виведення JSON
exit();

// Функція для прийняття заявки (приклад)
function acceptApplication($applicationId, $conn)
{
    // Додаткова логіка для прийняття заявки
    // ...

    // Приклад:
    // Отримання даних з таблиці studentapplication
    $query = "SELECT * FROM studentapplication WHERE id = $applicationId";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $applicationData = mysqli_fetch_assoc($result);

        // Додати дані до accepted_applications
        $queryInsertAccepted = "INSERT INTO accepted_applications (submission_date, last_name, first_name, middle_name, photo_path, birth_date, registered_address, home_address, email, mobile_phone, parents_phone, dormitory_number, room_number, tuition_funding, education_level, specialty, group_code, academic_year, social_category, previous_overpayment, next_year_payment, payment_receipt_path, room_preferences, application_upload_path, submitted_by_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertAccepted = mysqli_prepare($conn, $queryInsertAccepted);
        mysqli_stmt_bind_param(
            $stmtInsertAccepted,
            'ssssssssssssssssssssssss',
            $applicationData['submission_date'],
            $applicationData['last_name'],
            $applicationData['first_name'],
            $applicationData['middle_name'],
            $applicationData['photo_path'],
            $applicationData['birth_date'],
            $applicationData['registered_address'],
            $applicationData['home_address'],
            $applicationData['email'],
            $applicationData['mobile_phone'],
            $applicationData['parents_phone'],
            $applicationData['dormitory_number'],
            $applicationData['room_number'],
            $applicationData['tuition_funding'],
            $applicationData['education_level'],
            $applicationData['specialty'],
            $applicationData['group_code'],
            $applicationData['academic_year'],
            $applicationData['social_category'],
            $applicationData['previous_overpayment'],
            $applicationData['next_year_payment'],
            $applicationData['payment_receipt_path'],
            $applicationData['room_preferences'],
            $applicationData['application_upload_path'],
            $applicationData['submitted_by_user_id']
        );
        mysqli_stmt_execute($stmtInsertAccepted);

        // Видалити дані з studentapplication
        $queryDelete = "DELETE FROM studentapplication WHERE id = $applicationId";
        mysqli_query($conn, $queryDelete);
    }
}

// Функція для відхилення заявки (приклад)
function rejectApplication($applicationId, $conn)
{
    // Додаткова логіка для відхилення заявки
    // ...

    // Приклад:
    // Отримання даних з таблиці studentapplication
    $query = "SELECT * FROM studentapplication WHERE id = $applicationId";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $applicationData = mysqli_fetch_assoc($result);

        // Додати дані до rejected_applications
        $queryInsertRejected = "INSERT INTO rejected_applications (submission_date, last_name, first_name, middle_name, photo_path, birth_date, registered_address, home_address, email, mobile_phone, parents_phone, dormitory_number, room_number, tuition_funding, education_level, specialty, group_code, academic_year, social_category, previous_overpayment, next_year_payment, payment_receipt_path, room_preferences, application_upload_path, submitted_by_user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertRejected = mysqli_prepare($conn, $queryInsertRejected);
        mysqli_stmt_bind_param(
            $stmtInsertRejected,
            'ssssssssssssssssssssssss',
            $applicationData['submission_date'],
            $applicationData['last_name'],
            $applicationData['first_name'],
            $applicationData['middle_name'],
            $applicationData['photo_path'],
            $applicationData['birth_date'],
            $applicationData['registered_address'],
            $applicationData['home_address'],
            $applicationData['email'],
            $applicationData['mobile_phone'],
            $applicationData['parents_phone'],
            $applicationData['dormitory_number'],
            $applicationData['room_number'],
            $applicationData['tuition_funding'],
            $applicationData['education_level'],
            $applicationData['specialty'],
            $applicationData['group_code'],
            $applicationData['academic_year'],
            $applicationData['social_category'],
            $applicationData['previous_overpayment'],
            $applicationData['next_year_payment'],
            $applicationData['payment_receipt_path'],
            $applicationData['room_preferences'],
            $applicationData['application_upload_path'],
            $applicationData['submitted_by_user_id']
        );
        mysqli_stmt_execute($stmtInsertRejected);

        // Видалити дані з studentapplication
        $queryDelete = "DELETE FROM studentapplication WHERE id = $applicationId";
        mysqli_query($conn, $queryDelete);
    }
}
?>
