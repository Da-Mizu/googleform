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


$question_id = $data['question_id'] ?? null;
$answer_text = trim($data['answer_text'] ?? '');

$user_id = $data['user_id'] ?? null;
if (!is_numeric($user_id) || intval($user_id) <= 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentification requise pour répondre au sondage']);
    exit;
}

if (!is_numeric($question_id) || intval($question_id) <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'question_id manquant ou invalide']);
    exit;
}
if (strlen($answer_text) < 1 || strlen($answer_text) > 1000) {
    http_response_code(400);
    echo json_encode(['error' => 'Réponse vide ou trop longue (max 1000 caractères)']);
    exit;
}

$stmt = $pdo->prepare('INSERT INTO answer (question_id, user_id, answer_text) VALUES (?, ?, ?)');
$stmt->execute([intval($question_id), $user_id, $answer_text]);

header('Content-Type: application/json');
echo json_encode(['success' => true]);
