<?php
session_start();
$reg_already = isset($_SESSION['reg_already']) && $_SESSION['reg_already'] == 'true';
$success_message = isset($_SESSION['success_message']) && $_SESSION['success_message'] == 'true';
$error_message = isset($_SESSION['error_message']) && $_SESSION['error_message'] == 'true';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Registration DormLife</title>
    <script src="script.js"></script>
    <link rel="icon" type="image/png" href="../../img/logo.ico">
</head>
<body>
    <div class="white--menu">
        <div class="top--menu">
            <a href="силка">
                <img src="../../img/logo.png" alt="logo">
            </a>
            <a class="text--logo" href="силка">
                <p>DormLife</p> 
            </a>
            <div class="registration--authorizations">
                <a class="registration" href="index.php"><p>Реєстрація</p></a>
                <a class="sleah" href="#"><p>/</p></a>
                <a class="authorizations" href="../authorization/index.php"><p>Авторизація</p></a>
            </div>   
        </div>
    </div>

    <div class="container">
    <div class="square active">
        <div class="overlay">
        <p>Реєстрація</p>
            <?php
            if ($reg_already) {
                echo '<div class="message-container centered-text error-message">';
                echo '  <p style="color: red; font-family: \'e-Ukraine-Regular\', sans-serif; font-size: 16px;">Користувача з такою поштою уже зареєстровано.</p>';
                echo '</div>';
                unset($_SESSION['reg_already']);
            }
            if ($success_message) {
                echo '<div class="message-container centered-text success-message">';
                echo '  <p style="color: #06F527; font-family: \'e-Ukraine-Regular\', sans-serif; font-size: 16px;">Реєстрація пройшла успішно!</p>';
                echo '</div>';
                unset($_SESSION['success_message']);
            }
            if ($error_message) {
                echo '<div class="message-container centered-text error-message">';
                echo '  <p style="color: red; font-family: \'e-Ukraine-Regular\', sans-serif; font-size: 16px;">Помилка при додаванні даних користувача.</p>';
                echo '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <form method="POST" action="registration-form.php">
                <label for="email">Пошта (@nubip.edu.ua)</label>
                <input type="email" id="email" name="email" placeholder="Введіть пошту @nubip.edu.ua" required pattern="[a-zA-Z0-9._%+-]+@nubip\.edu\.ua">

                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" placeholder="Введіть пароль" required>
          
                <label for="confirm-password">Пов.пароль</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Повторіть пароль" required>
          
                <button class="button--r" type="submit">Зареєструватися</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
