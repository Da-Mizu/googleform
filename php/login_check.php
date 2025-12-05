<?php
// Autoriser CORS pour le développement local (toutes origines)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
// login_check.php
// Vérifie si l'utilisateur existe dans la base MySQL

// Paramètres de connexion à la base (à renseigner manuellement)
$host = 'localhost';
$db = 'google-form';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur de connexion à la base']);
    exit;
}

// Récupère les données du formulaire (JSON)
$ip = $_SERVER['REMOTE_ADDR'];
//Configuration de la limitation des tentatives de connexion
$maxAttempts = 5;
$lockMinutes = 15;

// Connexion à la base pour le suivi des tentatives
$pdo->exec('CREATE TABLE IF NOT EXISTS login_attempts (ip VARCHAR(45) PRIMARY KEY, attempts INT DEFAULT 0, last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP, locked_until DATETIME DEFAULT NULL)');

$attemptStmt = $pdo->prepare('SELECT attempts, locked_until FROM login_attempts WHERE ip = ?');
$attemptStmt->execute([$ip]);
$attempt = $attemptStmt->fetch();

if ($attempt && $attempt['locked_until'] && strtotime($attempt['locked_until']) > time()) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Trop de tentatives. Réessayez plus tard.']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (!$username || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

// Vérifie si l'utilisateur existe

$stmt = $pdo->prepare('SELECT * FROM user WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    // Réinitialiser les tentatives en cas de succès
    $resetStmt = $pdo->prepare('DELETE FROM login_attempts WHERE ip = ?');
    $resetStmt->execute([$ip]);
    echo json_encode(['success' => true, 'user' => $user]);
} else {
    // Incrémenter le nombre de tentatives
    if ($attempt) {
        $newAttempts = $attempt['attempts'] + 1;
        $lockedUntil = ($newAttempts >= $maxAttempts) ? date('Y-m-d H:i:s', strtotime("+$lockMinutes minutes")) : null;
        $updateStmt = $pdo->prepare('UPDATE login_attempts SET attempts = ?, last_attempt = NOW(), locked_until = ? WHERE ip = ?');
        $updateStmt->execute([$newAttempts, $lockedUntil, $ip]);
    } else {
        $insertStmt = $pdo->prepare('INSERT INTO login_attempts (ip, attempts, last_attempt, locked_until) VALUES (?, 1, NOW(), NULL)');
        $insertStmt->execute([$ip]);
    }
    http_response_code(401);
    $msg = ($attempt && $attempt['attempts'] + 1 >= $maxAttempts) ? 'Trop de tentatives. Réessayez dans ' . $lockMinutes . ' minutes.' : 'Utilisateur ou mot de passe incorrect';
    echo json_encode(['success' => false, 'error' => $msg]);
}
