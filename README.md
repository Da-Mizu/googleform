# Google Form Clone

Application web de gestion de sondages (inscription, connexion, création de sondages, réponses, affichage dynamique) réalisée avec HTML, CSS, JavaScript, PHP, MySQL et Bootstrap.

**Dernières mises à jour :**

- Réorganisation du projet : fichiers HTML dans `/html`, JS dans `/js`, endpoints PHP dans `/php`.
- Support des types de questions : texte libre et choix multiple (radio).
- Création de sondages : formulaire dynamique avec ajout automatique de questions et options.
- Filtrage des sondages déjà complétés par l’utilisateur.
- Redirection automatique vers la liste des sondages après soumission des réponses.
- Envoi sécurisé du `user_id` en POST (corps JSON) au lieu de GET (URL).
- Headers CORS présents sur tous les endpoints PHP.
- Vérification d’authentification côté client avant les appels sensibles.

## Fonctionnalités principales

- Authentification : inscription, connexion et déconnexion avec mots de passe hachés.
- Création de sondages : interface dynamique permettant de sélectionner le type de question (texte libre / choix multiple) et d’ajouter des options.
- Types de questions :
  - Texte libre (textarea).
  - Choix multiple (radio buttons) avec options personnalisables.
  - Ajout automatique d’inputs pour questions et options (principe similaire à Google Forms).
- Réponses aux sondages : affichage adapté selon le type de question et persistance en base.
- Filtrage intelligent : masquage des sondages déjà complétés par l’utilisateur.
- Protection anti-bruteforce : limitation des tentatives de connexion via la table `login_attempts`.
- Protection contre les injections SQL : utilisation de requêtes préparées.
- Navbar dynamique : boutons affichés selon l’état de connexion (Create, Logout, Register, Login).
- Transactions SQL utilisées lors de la création de sondages pour garantir la cohérence.

## Structure du projet

### Fichiers HTML (`/html`)
- `index.html` : page de connexion.
- `register.html` : page d’inscription.
- `home.html` : liste des sondages disponibles.
- `questions.html` : affichage des questions et formulaire de réponses.
- `create_survey.html` : interface de création de sondages (utilisateurs connectés).

### Fichiers JavaScript (`/js`)
- `script.js` : logique d’authentification et gestion du login.
- `register.js` : logique d’inscription.
- `home.js` : affichage dynamique et filtrage des sondages.
- `questions.js` : rendu adaptatif des questions et envoi des réponses.
- `create_survey.js` : gestion du formulaire de création (inputs dynamiques, types de question).

### Endpoints PHP (`/php`)
- `login_check.php` : authentification utilisateur avec anti‑bruteforce.
- `register.php` : inscription avec validation et hachage des mots de passe.
- `get_sondage.php` : récupération de la liste des sondages (filtrée par utilisateur).
- `get_questions.php` : récupération des questions avec type et options (pour choix multiple).
- `save_answer.php` : enregistrement des réponses aux sondages.
- `create_survey.php` : création de sondages avec questions et options (utilisation de transaction SQL).

### Base de données
- `sql/gogoleform.sql` : structure initiale des tables.
- `sql/migration_question_types.sql` : migration ajoutant le support des types de questions et des options.
  - Ajout de la colonne `type` dans la table `question`.
  - Création de la table `question_option` pour les choix multiples.

### Autres
- `style.css` : styles personnalisés.

## Démarrage

1. Importez les fichiers SQL du dossier `/sql` dans votre instance MySQL.
2. Placez le projet dans le répertoire racine de votre serveur web (ex. `htdocs` pour XAMPP).
3. Démarrez Apache et MySQL (XAMPP ou autre).
4. Ouvrez votre navigateur à l’adresse : `http://localhost/google-form/html/index.html`.

## Librairies utilisées

- Bootstrap 5 (pour les composants et la mise en page).

## Sécurité & bonnes pratiques

- Mots de passe hachés (`password_hash` / `password_verify`).
- Validation et assainissement des entrées côté serveur.
- Requêtes préparées pour prévenir les injections SQL.
- Limitation des tentatives de connexion (table `login_attempts`) pour réduire le risque de bruteforce.
- Authentification requise pour les actions sensibles.
- Envoi du `user_id` en POST (corps JSON) plutôt qu’en paramètre d’URL.
- Vérification de la connexion côté client (ex. `user_id` stocké dans localStorage) avant d’effectuer des requêtes fetch.
- Headers CORS présents sur les endpoints PHP.
- Transactions SQL lors de la création de sondages pour garantir la cohérence.
- Structure du projet organisée pour faciliter la maintenance.

## Exemple de connexion

- Après l’inscription, connectez‑vous avec vos identifiants créés.
- Une fois connecté, la navbar proposera les actions disponibles (Créer un sondage, Se déconnecter, etc.).

## TODO & améliorations possibles

Voir `todo.txt` pour les axes d’amélioration (sécurité renforcée, validations supplémentaires, améliorations UX, tests automatisés, etc.).