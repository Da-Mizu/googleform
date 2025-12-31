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

$input = json_decode(file_get_contents('php://input'), true);
$form_id = isset($input['form_id']) ? intval($input['form_id']) : null;
$user_id = isset($input['user_id']) ? intval($input['user_id']) : null;

if (!$form_id || $form_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'form_id manquant ou invalide']);
    exit;
}
if (!$user_id || $user_id <= 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentification requise']);
    exit;
}

try {
    // Vérifier que le user_id est bien propriétaire du formulaire
    $stmtForm = $pdo->prepare('SELECT user_id FROM form WHERE id = ?');
    $stmtForm->execute([$form_id]);
    $form = $stmtForm->fetch();
    if (!$form) {
        http_response_code(404);
        echo json_encode(['error' => 'Formulaire introuvable']);
        exit;
    }
    if (intval($form['user_id']) !== $user_id) {
        http_response_code(403);
        echo json_encode(['error' => 'Seul le créateur peut voir les partages']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT sa.user_id, sa.access_type, u.username, u.email FROM survey_access sa JOIN user u ON sa.user_id = u.id WHERE sa.form_id = ? ORDER BY u.username');
    $stmt->execute([$form_id]);
    $accesses = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($accesses);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des accès']);
}
