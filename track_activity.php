<?php
    session_start();
    require 'config.php';

    $userId = $_SESSION['id_user'] ?? null;

    if ($userId) {
        try {
            $stmt = $pdo->prepare("INSERT INTO user_activity (p_user_id, activity_date) VALUES (?, NOW())");
            $stmt->execute([$userId]);
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            error_log("Błąd zapisu aktywności: " . $e->getMessage());
            echo json_encode(['status' => 'error']);
        }
    } else {
        echo json_encode(['status' => 'not_logged_in']);
    }
?>