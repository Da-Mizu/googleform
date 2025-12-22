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
$target_username = isset($input['target_username']) ? trim($input['target_username']) : '';
$access_type = isset($input['access_type']) ? trim($input['access_type']) : 'answer';

$validAccess = ['view', 'answer', 'admin'];
if (!in_array($access_type, $validAccess, true)) {
    $access_type = 'answer';
}

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
if ($target_username === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Nom d’utilisateur requis']);
    exit;
}

try {
    // Vérifier que le user_id est bien propriétaire du formulaire
    $stmtForm = $pdo->prepare('SELECT id, user_id FROM form WHERE id = ?');
    $stmtForm->execute([$form_id]);
    $form = $stmtForm->fetch();
    if (!$form) {
        http_response_code(404);
        echo json_encode(['error' => 'Formulaire introuvable']);
        exit;
    }
    if (intval($form['user_id']) !== $user_id) {
        http_response_code(403);
        echo json_encode(['error' => 'Seul le créateur peut partager ce sondage']);
        exit;
    }

    // Récupérer l'utilisateur cible
    $stmtUser = $pdo->prepare('SELECT id, username, email FROM user WHERE username = ?');
    $stmtUser->execute([$target_username]);
    $targetUser = $stmtUser->fetch();
    if (!$targetUser) {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur cible introuvable']);
        exit;
    }

    // Insérer ou mettre à jour l'accès
    $stmtAccess = $pdo->prepare('INSERT INTO survey_access (form_id, user_id, access_type) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE access_type = VALUES(access_type)');
    $stmtAccess->execute([$form_id, $targetUser['id'], $access_type]);

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors du partage du sondage']);
}
