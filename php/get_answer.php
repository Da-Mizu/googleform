<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
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
    echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    exit;
}

// Récupérer les paramètres
$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : null;
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

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
    // Vérifier que l'utilisateur est le propriétaire du formulaire
    $stmtOwner = $pdo->prepare('SELECT user_id, title FROM form WHERE id = ?');
    $stmtOwner->execute([$form_id]);
    $form = $stmtOwner->fetch();
    
    if (!$form) {
        http_response_code(404);
        echo json_encode(['error' => 'Formulaire introuvable']);
        exit;
    }
    
    if (intval($form['user_id']) !== $user_id) {
        http_response_code(403);
        echo json_encode(['error' => 'Accès refusé : seul le créateur peut voir les réponses']);
        exit;
    }
    
    // Récupérer toutes les questions du formulaire
    $stmtQuestions = $pdo->prepare('SELECT id, question_text, type, anonymus FROM question WHERE form_id = ? ORDER BY id');
    $stmtQuestions->execute([$form_id]);
    $questions = $stmtQuestions->fetchAll();
    
    // Pour chaque question, récupérer les réponses
    $stmtAnswers = $pdo->prepare('SELECT id, user_id, answer_text, answered_at FROM answer WHERE question_id = ? ORDER BY answered_at DESC');
    
    foreach ($questions as &$question) {
        $stmtAnswers->execute([$question['id']]);
        $answers = $stmtAnswers->fetchAll();
        
        // Si la question est anonyme, masquer les informations utilisateur
        if (isset($question['anonymus']) && intval($question['anonymus']) === 1) {
            foreach ($answers as &$answer) {
                $answer['answer_text'] = null;
                $answer['user_id'] = null;
                $answer['masked'] = true;
            }
        } else {
            foreach ($answers as &$answer) {
                $answer['masked'] = false;
            }
        }
        
        $question['answers'] = $answers;
    }
    
    $response = [
        'success' => true,
        'form_title' => $form['title'],
        'questions' => $questions
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des réponses']);
}
