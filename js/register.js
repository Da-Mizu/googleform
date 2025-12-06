document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (localStorage.getItem('user_id')) {
        document.getElementById('registerMessage').textContent = "Vous êtes déjà connecté.";
        return;
    }
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const message = document.getElementById('registerMessage');
    message.textContent = '';
    try {
        const response = await fetch('http://localhost/google-form/php/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, email, password })
        });
        const result = await response.json();
        if (response.ok && result.success) {
            message.style.color = 'green';
            message.textContent = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            setTimeout(() => { window.location.href = 'index.html'; }, 1200);
        } else {
            message.style.color = '#dc3545';
            message.textContent = result.error || 'Erreur lors de l\'inscription.';
        }
    } catch (error) {
        message.style.color = '#dc3545';
        message.textContent = "Erreur serveur. Veuillez réessayer.";
    }
});
