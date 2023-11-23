<?php
session_start();
$error_message = "";

if (isset($_SESSION['reg_already'])) {
    if ($_SESSION['reg_already'] == 'user_not_found') {
        $error_message = "Користувача не знайдено. Перевірте правильність даних.";
    } elseif ($_SESSION['reg_already'] == 'wrong_password') {
        $error_message = "Невірний пароль. Перевірте правильність даних.";
    }
    unset($_SESSION['reg_already']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Authorization DormLife</title>
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
                <a class="registration" href="../registration/index.php"><p>Реєстрація</p></a>
                <a class="sleah" href="#"><p>/</p></a>
                <a class="authorizations" href="index.php"><p>Авторизація</p></a>
            </div>   
        </div>
    </div>

    <div class="container">
    <div class="square active">
        <div class="overlay">
            <p>Авторизація</p>
            <?php
            if (!empty($error_message)) {
                echo '<div class="centered-text error-message" style="color: red; font-family: \'e-Ukraine-Regular\', sans-serif; font-size: 16px; text-align: center;">'.$error_message.'</div>';
            }
            ?>
            <form method="POST" action="authorization.php">
                <label for="email">Пошта (@nubip.edu.ua)</label>
                <input type="email" id="email" name="email" placeholder="Введіть пошту @nubip.edu.ua" required pattern="[a-zA-Z0-9._%+-]+@nubip\.edu\.ua">

                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" placeholder="Введіть пароль" required>

                <button class="button--r" type="submit">Авторизуватися</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>

