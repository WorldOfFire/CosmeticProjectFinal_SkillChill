<?php
    require_once 'config.php';
    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    $index = $input['index'] ?? '';

    if (!$index) {
        echo json_encode(['unique' => false]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM company_employees WHERE employees_index = ?");
    $stmt->execute([$index]);
    $exists = $stmt->fetch() ? true : false;

    echo json_encode(['unique' => !$exists]);
?>