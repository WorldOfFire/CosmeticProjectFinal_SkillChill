<?php
    session_start();
    require_once 'config.php';

    if (empty($_SESSION['user'])) {
        header("Location: sign_in.php");
        exit;
    }

    $user_access = $_SESSION['access'];
    $user_first_login = $_SESSION['firstLogin'];
    $user_id = $_SESSION['id_user'];
    $error = $_SESSION['error'] ?? '';
    $success = $_SESSION['success'] ?? '';
    unset($_SESSION['error'], $_SESSION['success']);

    $stmt = $pdo->prepare("
        SELECT ce.*, pu.*, ua.*, uac.latest_activity
        FROM company_employees ce
        JOIN portal_user pu ON ce.id = pu.employees_id
        LEFT JOIN user_attempts ua ON pu.id_user = ua.user_id
        LEFT JOIN (
            SELECT p_user_id, MAX(activity_date) AS latest_activity
            FROM user_activity
            GROUP BY p_user_id
        ) uac ON pu.id_user = uac.p_user_id
        WHERE pu.id_user != ?
        AND ce.registered = 1
        ORDER BY ce.name ASC, ce.surname ASC
    ");
    $stmt->execute([$user_id]);
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
                <?php if (!empty($personnel) && $user_access >= 4): ?>
                <form method="post">
                    <input type="hidden" name="redirect_to" value="users_settings_panel.php?user=<?= urlencode($_SESSION['user']) ?>">
                    <table>
                        <tr class="table-header">
                            <th colspan="10">Ostatnia aktywność na portalu</th>
                        </tr>
                        <tr class="tr-checbox table-actions">
                            <th><input type="checkbox" id="master-checkbox"></th>
                            <th>
                                <div id="audit-all" style="display:none">
                                    <button type="submit" formaction="handler_audit_user.php" name="audit-all" title="Pobierz aktywność zaznaczonych użytkowników">
                                        <i class="fa-solid fa-file"></i>
                                    </button>
                                </div>
                            </th>
                            <th colspan="8"></th>
                        </tr>
                        <tr class="table-columns">
                            <th></th>
                            <th>Imię</th>
                            <th>Nazwisko</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Index</th>
                            <th>Kraj Biznesowy</th>
                            <th>Data</th>
                            <th>Godzina</th>
                            <th>
                                <button type="submit" formaction="handler_audit_user.php" name="no-activity" title="Pobierz użytkowników bez aktywności na portalu">
                                    <i class="fa-solid fa-file-excel"></i>
                                </button></th>
                        </tr>
                        <?php foreach ($personnel as $p): ?>
                            <?php if (((int)$p['access'] <= $user_access) && !empty($p['latest_activity'])): ?>
                                <tr>
                                    <td><input type="checkbox" class="table-checkbox" name="selected_ids[]" value="<?= $p['id_user'] ?>"></td>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td><?= htmlspecialchars($p['surname']) ?></td>
                                    <td><?= htmlspecialchars($p['login'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['mail']) ?></td>
                                    <td><?= htmlspecialchars($p['employees_index'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['country']) ?></td>
                                    <td><?= !empty($p['latest_activity']) ? date('Y-m-d', strtotime($p['latest_activity'])) : '-' ?></td>
                                    <td><?= !empty($p['latest_activity']) ? date('H:i', strtotime($p['latest_activity'])) : '-' ?></td>
                                    <td>
                                        <button type="submit" formaction="handler_audit_user.php" name="audit" value="<?= $p['id_user'] ?>" title="Pobierz aktywność użytkownika">
                                            <i class="fa-solid fa-file"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
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
            const masterCheckbox = document.getElementById('master-checkbox');
            const auditButton = document.getElementById('audit-all');
            const checkboxes = document.querySelectorAll('input.table-checkbox');

            if (!masterCheckbox || !auditButton || checkboxes.length === 0) return;

            function toggleauditButton() {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                auditButton.style.display = anyChecked ? 'inline-block' : 'none';
            }

            masterCheckbox.addEventListener('change', () => {
                checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
                toggleauditButton();
            });

            checkboxes.forEach(cb => cb.addEventListener('change', toggleauditButton));

            toggleauditButton();
        });
    </script>
</body>
</html>