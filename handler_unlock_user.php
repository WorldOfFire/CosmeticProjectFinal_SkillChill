<?php
    session_start();
    require_once 'config.php';

    $redirect = $_POST['redirect_to'] ?? 'users_unlock_panel.php?user=' . urlencode($_SESSION['user']);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: $redirect");
        exit;
    }

    function unlockUser($pdo, $user_id) {
        $stmt = $pdo->prepare("
            UPDATE user_attempts
            SET login_attempts = 0, account_access = 0
            WHERE user_id = ?
        ");
        $stmt->execute([$user_id]);
    }

    // Odblokowanie pojedynczego użytkownika
    if (isset($_POST['unlock'])) {
        unlockUser($pdo, (int)$_POST['unlock']);
        $_SESSION['success'] = "Użytkownik został odblokowany.";
    }

    // Odblokowanie wielu użytkowników
    if (isset($_POST['unlock-all']) && !empty($_POST['selected_ids'])) {
        foreach ($_POST['selected_ids'] as $user_id) {
            unlockUser($pdo, (int)$user_id);
        }
        $_SESSION['success'] = "Zaznaczeni użytkownicy zostali odblokowani.";
    }

    header("Location: $redirect");
    exit;
?>
