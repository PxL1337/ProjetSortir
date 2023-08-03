document.addEventListener('DOMContentLoaded', (event) => {
    const password = document.getElementById('user_new_password');
    const confirmPassword = document.getElementById('user_new_password_confirmation');

    // Vérifie que les deux éléments existent avant de continuer
    if (!password || !confirmPassword) {
        return;
    }

    const validatePassword = () => {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Les mots de passe ne correspondent pas");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('change', validatePassword);
});