<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

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

$data = json_decode(file_get_contents('php://input'), true);


$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validation username
if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nom d\'utilisateur invalide (3-30 caractères, lettres, chiffres, underscore)']);
    exit;
}
// Validation email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email invalide']);
    exit;
}
// Validation password
if (!is_string($password) || strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['error' => 'Mot de passe trop court (min 6 caractères)']);
    exit;
}

// Vérifier si l'utilisateur existe déjà
$stmt = $pdo->prepare('SELECT id FROM user WHERE username = ? OR email = ?');
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Nom d\'utilisateur ou email déjà utilisé']);
    exit;
}

// Hacher le mot de passe
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO user (username, password, email) VALUES (?, ?, ?)');
$stmt->execute([$username, $hashedPassword, $email]);

header('Content-Type: application/json');
echo json_encode(['success' => true]);
