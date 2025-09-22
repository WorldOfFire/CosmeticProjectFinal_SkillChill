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
    <script src="password_validation.js"></script>
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
                <div></div>
                <div>
                    <?php if($user_first_login != 0) : ?>
                        <button onclick="window.location.href='settings_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" id="settings_button" title="Ustawienia konta">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                    <?php endif; ?>
                    <button onclick="window.location.href='handler_logout.php'" type="button" id="logout_button" title="Wyloguj">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </div>
            </div>
            <div class="action-container">
                <?php if($user_first_login == 0) : ?>
                    <div>
                        <form action="handler_new_password.php" method="post" class="change-password-form">
                            <input type="hidden" name="redirect_to" value="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?user=' . urlencode($_SESSION['user']) ?>">

                            <label>Stare hasło:
                                <input type="password" name="old_password" autocomplete="new-password" required/>
                            </label>
                            <label>Nowe hasło:
                                <input type="password" name="new_password" class="new-password" autocomplete="new-password" required/>
                            </label>
                            <label>Powtórz nowe hasło:
                                <input type="password" name="new_repeat_password" class="new-repeat-password" autocomplete="new-password" required/>
                            </label>
                            <div id="new_user-p">
                                <button type="reset" title="Wyczyść">
                                    <i class="fa-solid fa-eraser"></i>
                                </button>
                                <button type="submit" title="Zmień hasło">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                </button>
                            </div>

                            <div class="password-match-error" style="color:red; display:none;">Hasła nie są takie same</div>
                            <?php if ($error): ?>
                                <div style="color:red;"><?= htmlspecialchars($error) ?></div>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if($user_first_login != 0) : ?>
                    <div>
                        <?php if($user_access > 0) : ?>
                            <button onclick="window.location.href='administration_panel.php?user=<?= urlencode($_SESSION['user']) ?>'"
                                    type="button" id="administration_button"
                                    title="Zarządzaj użytkownikami">
                                <i class="fa-solid fa-user-cog"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div>
                        <button onclick="window.location.href='sales_channels_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" id="s_channels_button" title="Kanały sprzedaży">
                            <i class="fa-solid fa-network-wired"></i>
                        </button>
                    </div>
                    <div>
                        <?php if($user_access > 1) : ?>
                            <button onclick="window.location.href='purchase_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" id="p_report_button" title="Raport zakupów">
                                <i class="fa-solid fa-chart-pie"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div>
                        <button onclick="window.location.href='media_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" id="media_button" title="Media">
                            <i class="fa-solid fa-file"></i>
                        </button>
                    </div>
                    <div>
                        <?php if($user_access > 0) : ?>
                            <button onclick="window.location.href='tools_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" id="tools_button" title="Zarządzanie danymi">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <footer>
        <div>Skill&Chill - II etap rekrutacji - Szulińska Weronika</div>
    </footer>
</body>
</html>
