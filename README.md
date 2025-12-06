
# Google Form Clone

Application web de gestion de sondages (login, inscription, réponses, affichage dynamique, création de sondages) avec HTML, CSS, JavaScript, PHP, MySQL et Bootstrap.

**Dernières mises à jour :**

- Organisation complète du projet : fichiers HTML dans `/html`, fichiers JS dans `/js`, endpoints PHP dans `/php`
- **Types de questions** : support des questions à texte libre ET à choix multiple (radio buttons)
- **Création de sondages** : formulaire dynamique avec ajout automatique de questions et d'options
- Filtrage intelligent : les sondages auxquels l'utilisateur a déjà répondu ne s'affichent plus dans la liste
- Redirection automatique vers la liste des sondages après soumission des réponses
- Envoi sécurisé de `user_id` en POST (body JSON) au lieu de GET (URL)
- Headers CORS présents sur tous les endpoints PHP
- Vérification de l'authentification côté JavaScript avant les appels sensibles

## Fonctionnalités principales

- **Authentification sécurisée** : inscription, login, logout avec mots de passe hashés
- **Création de sondages** : formulaire dynamique avec choix du type de question (texte libre / choix multiple)
- **Types de questions** : 
  - Texte libre avec textarea
  - Choix multiple avec radio buttons et options personnalisables
  - Ajout automatique d'inputs pour questions et options (même principe que Google Forms)
- **Réponse aux sondages** : affichage adapté selon le type de question, persistance des réponses
- **Filtrage intelligent** : masquage des sondages déjà complétés par l'utilisateur
- **Limitation des tentatives de login** : système anti-bruteforce avec table `login_attempts`
- **Protection contre les injections SQL** : requêtes préparées partout
- **Navbar dynamique** : boutons adaptés selon l'état de connexion (Create, Logout, Register, Login)
- **Redirection automatique** : retour à la liste après soumission de réponses

## Structure du projet

### Fichiers HTML (`/html`)
- `index.html` : Formulaire de login
- `register.html` : Formulaire d'inscription
- `home.html` : Liste des sondages disponibles
- `questions.html` : Affichage des questions et saisie des réponses
- `create_survey.html` : Création de nouveaux sondages (utilisateurs connectés)

### Fichiers JavaScript (`/js`)
- `script.js` : Logique d'authentification et login
- `register.js` : Logique d'inscription
- `home.js` : Affichage dynamique des sondages (filtrés selon les réponses)
- `questions.js` : Affichage adaptatif des questions (textarea / radio) et soumission des réponses
- `create_survey.js` : Gestion du formulaire de création (inputs dynamiques, types de questions)

### Endpoints PHP (`/php`)
- `login_check.php` : Authentification utilisateur avec anti-bruteforce
- `register.php` : Inscription avec validation et hachage des mots de passe
- `get_sondage.php` : Récupération de la liste des sondages (filtrés par utilisateur)
- `get_questions.php` : Récupération des questions avec type et options (si choix multiple)
- `save_answer.php` : Enregistrement des réponses aux questions
- `create_survey.php` : Création de sondages avec questions et options (transaction SQL)

### Base de données
- `sql/gogoleform.sql` : Structure initiale des tables
- `sql/migration_question_types.sql` : Migration pour ajouter le support des types de questions et options
  - Ajout colonne `type` dans la table `question`
  - Création table `question_option` pour les choix multiples

### Autres
- `style.css` : Styles personnalisés

## Démarrage

1. Importez la structure SQL et les données depuis le dossier `/sql` dans MySQL.
2. Placez le dossier dans `htdocs` de XAMPP.
3. Lancez Apache et MySQL via XAMPP.
4. Accédez à [http://localhost/google-form/html/index.html](http://localhost/google-form/html/index.html) dans votre navigateur.

## Librairies utilisées

- [Bootstrap 5](https://getbootstrap.com/)

## Sécurité & bonnes pratiques

- Mots de passe hashés (PHP `password_hash`/`password_verify`)
- Validation et assainissement des entrées côté serveur
- Requêtes préparées partout (anti-injection SQL)
- Limitation brute-force sur le login (table `login_attempts`)
- Authentification requise pour les actions sensibles
- Envoi sécurisé de `user_id` en POST (body JSON) au lieu de GET (URL)
- Vérification de la connexion côté client (clé `user_id` dans le localStorage)
- Les requêtes fetch JS ne partent que si l'utilisateur est authentifié
- CORS systématique sur tous les endpoints PHP
- Transactions SQL pour garantir la cohérence des données (création de sondages)
- Structure de projet organisée pour une meilleure maintenabilité

## Exemple de connexion

Après inscription, connectez-vous avec vos identifiants créés.

## TODO & améliorations possibles

Voir le fichier `todo.txt` pour les axes d'amélioration sécurité, validation, UX, etc.
