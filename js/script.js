// Navbar user actions
function renderNavbarUserActions() {
    const navbar = document.getElementById('navbarUserActions');
    if (!navbar) return;
    navbar.innerHTML = '';
    const userId = localStorage.getItem('user_id');
    if (userId) {
        const createBtn = document.createElement('a');
        createBtn.className = 'btn btn-success ms-2';
        createBtn.textContent = 'Create';
        createBtn.href = 'create_survey.html';
        navbar.appendChild(createBtn);
        const logoutBtn = document.createElement('button');
        logoutBtn.className = 'btn btn-outline-light ms-2';
        logoutBtn.textContent = 'Logout';
        logoutBtn.onclick = function() {
            localStorage.removeItem('user_id');
            window.location.href = 'index.html';
        };
        navbar.appendChild(logoutBtn);
    } else {
        const registerBtn = document.createElement('a');
        registerBtn.className = 'btn btn-outline-light ms-2';
        registerBtn.textContent = 'Register';
        registerBtn.href = 'register.html';
        const loginBtn = document.createElement('a');
        loginBtn.className = 'btn btn-light ms-2';
        loginBtn.textContent = 'Login';
        loginBtn.href = 'index.html';
        navbar.appendChild(registerBtn);
        navbar.appendChild(loginBtn);
    }
}
renderNavbarUserActions();
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const message = document.getElementById('loginMessage');
    console.log('Tentative de connexion pour l\'utilisateur :', username + ' avec le mot de passe :', password);
    message.textContent = '';
    try {
        const response = await fetch('http://localhost/google-form/php/login_check.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ username, password })
        });
        const result = await response.json();
        if (response.ok && result.success) {
            message.style.color = 'green';
            message.textContent = 'Connexion réussie !';
            // Stocker l'ID utilisateur dans le localStorage
            if (result.user && result.user.id) {
                localStorage.setItem('user_id', result.user.id);
            }
            setTimeout(() => {
                window.location.href = 'home.html';
            }, 800);
        } else {
            message.style.color = '#dc3545';
            message.textContent = result.error || 'Utilisateur ou mot de passe incorrect.';
        }
    } catch (error) {
        message.style.color = '#dc3545';
        message.textContent = "Erreur serveur. Veuillez réessayer.";
    }
});
