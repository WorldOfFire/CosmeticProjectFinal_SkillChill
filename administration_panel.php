<?php
    session_start();

    if (empty($_SESSION['user'])) {
        header("Location: sign_in.php");
        exit;
    }

    $user_access = $_SESSION['access'];
    $user_first_login = $_SESSION['firstLogin'];
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Portal B2B</title>
    <meta name="description" content="Portal B2B dla pracowników firmy kosmetycznej - dostęp do analiz i danych wewnętrznych.">
    <meta name="author" content="Firma Kosmetyczna">
    <meta name="robots" content="noindex, nofollow">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-store">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <meta name="theme-color" content="#d6336c">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="stylePanel.css?v=<?php echo filemtime('stylePanel.css'); ?>">
    <script src="activity_tracker.js"></script>
</head>
<body>
    <div class="triangle-right"></div>
    <header>
        <div class="logo-container"><img src="img/logo.png" alt="Logo"></div>
        <div class="header-text">
            <h1>Portal B2B dla dystrybutorów</h1>
            <h2>Firma kosmetyczna</h2>
        </div>
    </header>
    <main>
        <div class="main-container">
            <div class="return-container">
                <div>
                    <button onclick="window.location.href='main_panel.php?user=<?= urlencode($_SESSION['user']) ?>'"
                            type="button" id="return_button"
                            title="Powrót">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
                <div>
                    <button onclick="window.location.href='settings_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" id="settings_button" title="Ustawienia konta">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <button onclick="window.location.href='handler_logout.php'" type="button" id="logout_button" title="Wyloguj">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </div>
            </div>
            <div class="action-container">
                <div>
                        <?php if($user_access >= 4) : ?>
                            <button onclick="window.location.href='users_add_panel.php?user=<?= urlencode($_SESSION['user']) ?>'"
                                    type="button" id="add_user_button"
                                    title="Zatwierdź pracowników">
                                <i class="fa-solid fa-user-check"></i>
                            </button>
                            <button onclick="window.location.href='users_settings_panel.php?user=<?= urlencode($_SESSION['user']) ?>'"
                                    type="button" id="settings_user_button"
                                    title="Zarządzaj kontami pracowników">
                                <i class="fa-solid fa-user-cog"></i>
                            </button>
                            <button onclick="window.location.href='users_audit_panel.php?user=<?= urlencode($_SESSION['user']) ?>'"
                                    type="button" id="audit_user_button"
                                    title="Aktywność pracowników na portalu">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </button>
                        <?php endif; ?>
                            <button onclick="window.location.href='users_unlock_panel.php?user=<?= urlencode($_SESSION['user']) ?>'"
                                    type="button" id="unlock_user_button"
                                    title="Odblokuj konta pracowników">
                                    <i class="fa-solid fa-lock-open"></i>
                            </button>
                    </div>
            </div>
        </div>
    </main>
    <footer>
        <div>Skill&Chill - II etap rekrutacji - Szulińska Weronika</div>
    </footer>   
</body>
</html>