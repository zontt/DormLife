document.addEventListener('DOMContentLoaded', function () {
    var emailInput = document.getElementById('email');
    var passwordInput = document.getElementById('password');
    var confirmPasswordInput = document.getElementById('confirm-password');

    var emailPattern = /@nubip\.edu\.ua/;

    emailInput.addEventListener('input', function () {
        if (emailPattern.test(emailInput.value)) {
            emailInput.setCustomValidity('');
        } else {
            emailInput.setCustomValidity('Пошта має бути введена за доменом @nubip.edu.ua');
        }
    });

    passwordInput.addEventListener('input', function () {
        if (passwordInput.value.length < 5) {
            passwordInput.setCustomValidity('Пароль має містити як мінімум 5 символів');
        } else if (!/[A-Z]/.test(passwordInput.value)) {
            passwordInput.setCustomValidity('Пароль має містити як мінімум одну велику літеру');
        } else {
            passwordInput.setCustomValidity('');
        }
    });

    confirmPasswordInput.addEventListener('input', function () {
        if (passwordInput.value === confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('');
        } else {
            confirmPasswordInput.setCustomValidity('Повторний пароль не співпадає з паролем');
        }
    });
});
