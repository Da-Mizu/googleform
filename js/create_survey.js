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
        window.location.href = 'index.html';
    }
}
renderNavbarUserActions();

// Gestion dynamique des inputs de questions
const questionsContainer = document.getElementById('questionsContainer');

function updateQuestionLabels() {
    const questionInputs = questionsContainer.querySelectorAll('.question-input');
    questionInputs.forEach((div, index) => {
        const label = div.querySelector('label');
        label.textContent = `Question ${index + 1}`;
    });
}

function checkAndAddInput() {
    const allInputs = questionsContainer.querySelectorAll('.question-field');
    const emptyInputs = Array.from(allInputs).filter(input => input.value.trim() === '');
    
    // Si il ne reste qu'un seul input vide, on en ajoute un nouveau
    if (emptyInputs.length === 1) {
        const newDiv = document.createElement('div');
        newDiv.className = 'mb-3 question-input';
        
        const newLabel = document.createElement('label');
        newLabel.className = 'form-label';
        newLabel.textContent = `Question ${allInputs.length + 1}`;
        
        const newInput = document.createElement('input');
        newInput.type = 'text';
        newInput.className = 'form-control question-field';
        newInput.placeholder = 'Entrez votre question';
        
        newDiv.appendChild(newLabel);
        newDiv.appendChild(newInput);
        questionsContainer.appendChild(newDiv);
        
        // Ajouter l'event listener au nouvel input
        newInput.addEventListener('input', checkAndAddInput);
    }
}

// Ajouter les event listeners aux inputs initiaux
questionsContainer.querySelectorAll('.question-field').forEach(input => {
    input.addEventListener('input', checkAndAddInput);
});

// Gestion de la soumission du formulaire
document.getElementById('createSurveyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const userId = localStorage.getItem('user_id');
    if (!userId) {
        window.location.href = 'index.html';
        return;
    }
    
    const title = document.getElementById('surveyTitle').value.trim();
    const description = document.getElementById('surveyDescription').value.trim();
    const message = document.getElementById('createMessage');
    
    // Récupérer toutes les questions non vides
    const questionInputs = questionsContainer.querySelectorAll('.question-field');
    const questions = Array.from(questionInputs)
        .map(input => input.value.trim())
        .filter(q => q !== '');
    
    if (questions.length === 0) {
        message.style.color = '#dc3545';
        message.textContent = 'Vous devez ajouter au moins une question.';
        return;
    }
    
    message.textContent = '';
    
    try {
        const response = await fetch('http://localhost/google-form/php/create_survey.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                title, 
                description, 
                questions,
                user_id: userId 
            })
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            message.style.color = 'green';
            message.textContent = 'Sondage créé avec succès !';
            setTimeout(() => {
                window.location.href = 'home.html';
            }, 1500);
        } else {
            message.style.color = '#dc3545';
            message.textContent = result.error || 'Erreur lors de la création du sondage.';
        }
    } catch (error) {
        message.style.color = '#dc3545';
        message.textContent = "Erreur serveur. Veuillez réessayer.";
    }
});
