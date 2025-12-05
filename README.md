
# Google Form Clone

Application web de gestion de sondages (login, inscription, réponses, affichage dynamique) avec HTML, CSS, JavaScript, PHP, MySQL et Bootstrap.

**Nouveautés récentes :**

- Les endpoints PHP sont désormais dans le dossier `/php` (ex : `/php/login_check.php`).
- Tous les appels JS utilisent les nouveaux chemins `/php/xxx.php`.
- Les headers CORS sont présents sur tous les endpoints PHP.
- La vérification de l'authentification (user_id) se fait côté JavaScript avant tout appel aux questions.
- Les questions ne sont affichées que si l'utilisateur est connecté (clé `user_id` dans le localStorage).

## Fonctionnalités principales

- Authentification sécurisée (inscription, login, logout, mot de passe hashé)
- Limitation des tentatives de login (anti-bruteforce)
- Protection contre les injections SQL et validation côté serveur
- Saisie et persistance des réponses aux sondages (liées à l'utilisateur)
- Affichage dynamique des sondages et questions depuis la base
- Navbar dynamique selon l'état de connexion
- Protection des endpoints sensibles (authentification requise)

## Structure du projet

- `index.html` : Formulaire de login
- `register.html` : Formulaire d'inscription
- `home.html` : Liste des sondages
- `questions.html` : Liste des questions et saisie des réponses
- `style.css` : Styles personnalisés
- `script.js`, `register.js`, `home.js`, `questions.js` : Logique JS
- `/php/login_check.php`, `/php/register.php`, `/php/get_sondage.php`, `/php/get_questions.php`, `/php/save_answer.php` : Endpoints PHP (tous dans `/php`)

## Démarrage

1. Importez la structure SQL (`gogoleform.sql`) et les données (`sondage_data.sql`, `question_data.sql`) dans MySQL.
2. Placez le dossier dans `htdocs` de XAMPP.
3. Lancez Apache et MySQL via XAMPP.
4. Accédez à [http://localhost/google-form/](http://localhost/google-form/) dans votre navigateur.

## Librairies utilisées

- [Bootstrap 5](https://getbootstrap.com/)

## Sécurité & bonnes pratiques

- Mots de passe hashés (PHP `password_hash`/`password_verify`)
- Validation et assainissement des entrées côté serveur
- Requêtes préparées partout (anti-injection SQL)
- Limitation brute-force sur le login
- Authentification requise pour les actions sensibles
- Vérification de la connexion côté client (clé `user_id` dans le localStorage)(remplacer le l'appel au local storage par la gestion de token et d'authentification)
- Les requêtes fetch JS ne partent que si l'utilisateur est authentifié
- CORS systématique sur tous les endpoints PHP

## Exemple de connexion

Après inscription, connectez-vous avec vos identifiants créés.

## TODO & améliorations possibles

Voir le fichier `todo.txt` pour les axes d'amélioration sécurité, validation, UX, etc.
