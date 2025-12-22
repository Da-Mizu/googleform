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


// Récupérer l'ID utilisateur depuis le body de la requête POST
$input = json_decode(file_get_contents('php://input'), true);
$user_id = isset($input['user_id']) ? intval($input['user_id']) : null;

if ($user_id) {
    // Récupérer tous les sondages avec :
    // - answered : si l'utilisateur a déjà répondu
    // - user_role : owner si créateur, sinon access_type s'il a été partagé (view/answer/admin)
    $stmt = $pdo->prepare('
        SELECT 
            f.id,
            f.title,
            f.description,
            f.user_id,
            CASE WHEN EXISTS (
                SELECT 1 FROM answer a
                JOIN question q ON a.question_id = q.id
                WHERE q.form_id = f.id AND a.user_id = ?
            ) THEN 1 ELSE 0 END AS answered,
            CASE 
                WHEN f.user_id = ? THEN "owner"
                ELSE sa.access_type
            END AS user_role
        FROM form f
        LEFT JOIN survey_access sa ON sa.form_id = f.id AND sa.user_id = ?
    ');
    $stmt->execute([$user_id, $user_id, $user_id]);
} else {
    // Si pas d'utilisateur, retourner tous les sondages
    $stmt = $pdo->prepare('SELECT id, title, description, user_id, 0 AS answered, NULL AS user_role FROM form');
    $stmt->execute();
}
$sondages = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($sondages);
