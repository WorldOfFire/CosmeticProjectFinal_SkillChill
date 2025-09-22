<?php
    session_start();
    require_once 'config.php';

    if (empty($_SESSION['user'])) {
        header("Location: sign_in.php");
        exit;
    }

    $error = $_SESSION['error'] ?? '';
    $success = $_SESSION['success'] ?? '';
    unset($_SESSION['error'], $_SESSION['success']);

    $stmt = $pdo->prepare("
        SELECT *
        FROM company_employees ce
        JOIN portal_user pu ON ce.id = pu.employees_id
        JOIN user_attempts ua ON pu.id_user = ua.user_id
        WHERE ua.account_access = 1
        AND (
                (pu.access = 4 AND pu.access <= ?)
                OR (pu.access < 4 AND pu.access < ?)
            )
        ORDER BY ce.name ASC, ce.surname ASC;
    ");

    $stmt->execute([$_SESSION['access'], $_SESSION['access']]);
    $personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="styleUserDesign.css?v=<?php echo filemtime('styleUserDesign.css'); ?>">
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
                    <button onclick="window.location.href='administration_panel.php?user=<?= urlencode($_SESSION['user']) ?>'"
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
                <?php if (!empty($personnel)): ?>
                <form method="post" action="handler_unlock_user.php">
                    <input type="hidden" name="redirect_to" value="users_unlock_panel.php?user=<?= urlencode($_SESSION['user']) ?>">
                    <table>
                        <thead>
                            <tr class="table-header">
                                <th colspan="8">Użytkownicy zablokowani</th>
                            </tr>
                            <tr class="tr-checbox table-actions">
                                <th><input type="checkbox" id="select-all"></th>
                                <th>
                                    <button type="submit" name="unlock-all" id="unlock-all" style="display:none;" title="Odblokuj zaznaczonych">
                                        <i class="fa-solid fa-unlock"></i>
                                    </button>
                                </th>
                                <th colspan="6"></th>
                            </tr>
                            <tr class="table-columns">
                                <th></th>
                                <th>Imię</th>
                                <th>Nazwisko</th>
                                <th>Login</th>
                                <th>Email</th>
                                <th>Index</th>
                                <th>Kraj Biznesowy</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($personnel as $p): ?>
                            <tr>
                                <td><input type="checkbox" class="table-1" name="selected_ids[]" value="<?= $p['user_id'] ?>"></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['surname']) ?></td>
                                <td><?= htmlspecialchars($p['login']) ?></td>
                                <td><?= htmlspecialchars($p['mail']) ?></td>
                                <td><?= htmlspecialchars($p['employees_index']) ?></td>
                                <td><?= htmlspecialchars($p['country']) ?></td>
                                <td>
                                    <button type="submit" name="unlock" value="<?= $p['user_id'] ?>" title="Odblokuj użytkownika">
                                        <i class="fa-solid fa-unlock"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <footer>
        <div>Skill&Chill - II etap rekrutacji - Szulińska Weronika</div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const masterCheckbox = document.getElementById('select-all');
            const unlockButton = document.getElementById('unlock-all');
            const checkboxes = document.querySelectorAll('input.table-1');

            if (!masterCheckbox || !unlockButton || checkboxes.length === 0) return;

            function toggleUnlockButton() {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                unlockButton.style.display = anyChecked ? 'inline-block' : 'none';
            }

            masterCheckbox.addEventListener('change', () => {
                checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
                toggleUnlockButton();
            });

            checkboxes.forEach(cb => cb.addEventListener('change', toggleUnlockButton));

            toggleUnlockButton();
        });
    </script>
</body>
</html>