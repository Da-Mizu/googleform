<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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



$form_id = isset($_GET['form_id']) ? $_GET['form_id'] : null;
if (!is_numeric($form_id) || intval($form_id) <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'form_id manquant ou invalide']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, question_text FROM question WHERE form_id = ?');
$stmt->execute([intval($form_id)]);
$questions = $stmt->fetchAll();

header('Content-Type: application/json');
// Authentification requise (optionnel)
// Authentification déléguée au client (localStorage)
echo json_encode($questions);
