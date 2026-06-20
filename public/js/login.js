// public/js/login.js
// Gère la soumission du formulaire de connexion via fetch()

document.addEventListener('DOMContentLoaded', () => {

    const form    = document.getElementById('login-form');
    const btn     = document.getElementById('login-btn');
    const errDiv  = document.getElementById('login-error');
    const errText = document.getElementById('login-error-text');

    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        hideError();

        const email    = document.getElementById('login-email').value.trim();
        const password = document.getElementById('login-password').value;

        if (!email || !password) {
            showError('Email et mot de passe requis.');
            return;
        }

        btn.disabled    = true;
        btn.textContent = 'Connexion en cours…';

        const formData = new FormData();
        formData.append('email',    email);
        formData.append('password', password);

        try {
            const response = await fetch('index.php?action=api/v1/login', {
                method: 'POST',
                body:   formData
            });

            const data = await response.json();

            if (data.success) {
                // Redirection vers le dashboard après login réussi
                window.location.href = 'index.php?action=dashboard';
            } else {
                showError(data.message || 'Identifiants invalides.');
                resetBtn();
            }
        } catch (err) {
            showError('Erreur réseau. Vérifiez votre connexion.');
            resetBtn();
        }
    });

    function showError(msg) {
        errText.textContent = msg;
        errDiv.classList.remove('hidden');
    }

    function hideError() {
        errDiv.classList.add('hidden');
        errText.textContent = '';
    }

    function resetBtn() {
        btn.disabled    = false;
        btn.textContent = 'Se Connecter';
    }
});
