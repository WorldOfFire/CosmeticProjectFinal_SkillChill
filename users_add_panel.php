<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user'])) {
    header("Location: sign_in.php");
    exit;
}

$user_access = $_SESSION['access'];
$user_first_login = $_SESSION['firstLogin'];

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error']);
unset($_SESSION['success']);

$stmt = $pdo->query("SELECT * FROM company_employees WHERE registered = 0 ORDER BY name ASC, surname ASC");
$personnel = $stmt->fetchAll();
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
                <button onclick="window.location.href='administration_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" id="return_button" title="Powrót">
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
            <form method="post" action="handler_add_user.php">
                <input type="hidden" name="redirect_to" value="users_add_panel.php?user=<?= urlencode($_SESSION['user']) ?>">
                <table>
                    <tr class="table-header">
                        <th colspan="9">Oczekujący na weryfikację</th>
                    </tr>
                    <tr class="tr-checbox table-actions">
                        <th><input type="checkbox" id="select-all"></th>
                        <th>
                            <div id="confirm-all" style="display: none">
                                <button type="submit" name="confirm-all" formaction="handler_add_user.php" title="Potwierdź rejestrację zaznaczonych użytkowników">
                                    <i class="fa-solid fa-circle-check"></i>
                                </button>
                            </div>
                        </th>
                        <th>
                            <div id="delete-all" style="display: none">
                                <button type="submit" name="delete-all" formaction="handler_delete_user.php" title="Usuń zaznaczonych użytkowników">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </th>
                        <th colspan="6"></th>
                    </tr>
                    <tr class="table-columns">
                        <th></th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Email</th>
                        <th>Index</th>
                        <th>Kraj działalności</th>
                        <th>Poziom dostępu</th>
                        <th></th>
                        <th></th>
                    </tr>
                    <?php foreach ($personnel as $p): ?>
                    <tr>
                        <td><input type="checkbox" class="table-checkbox" name="selected_ids[]" value="<?= $p['id'] ?>"></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['surname']) ?></td>
                        <td><?= htmlspecialchars($p['mail']) ?></td>
                        <td><?= htmlspecialchars($p['employees_index']) ?></td>
                        <td><?= htmlspecialchars($p['country']) ?></td>
                        <td>
                            <select name="access_level[<?= $p['id'] ?>]">
                                <option value="0">Pracownik dystrybutora</option>
                                <option value="1">Dystrybutor</option>
                                <option value="2">Export manager</option>
                                <option value="3">Administrator</option>
                                <option value="4">Super-administrator</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="confirm" value="<?= $p['id'] ?>" formaction="handler_add_user.php">
                                <i class="fa-solid fa-circle-check"></i>
                            </button>
                        </td>
                        <td>
                            <button type="submit" name="delete" value="<?= $p['id'] ?>" formaction="handler_delete_user.php" title="Usuń użytkownika">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
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
document.addEventListener('DOMContentLoaded', function() {
    const masterCheckbox = document.getElementById('select-all');
    const deleteButton = document.getElementById("delete-all");
    const confirmButton = document.getElementById("confirm-all")
    const checkboxes = document.querySelectorAll('input.table-checkbox');

    function toggleButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        deleteButton.style.display = anyChecked ? 'block' : 'none';
        confirmButton.style.display = anyChecked ? 'block' : 'none';
    }

    masterCheckbox.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleButton();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleButton);
    });

    toggleButton();
});
</script>
</body>
</html>
