<?php
session_start();

if (empty($_SESSION['user'])) {
    header("Location: sign_in.php");
    exit;
}

require_once 'country_map.php';

$user_access = $_SESSION['access'];
$user_first_login = $_SESSION['firstLogin'];
$user_name = $_SESSION['user_name'];
$user_surname = $_SESSION['user_surname'];
$user_index = $_SESSION['user_index'];
$user_country = $_SESSION['user_country'];
$user_login = $_SESSION['user'];
$user_mail = $_SESSION['user_mail'];

// Pobranie komunikatów
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
$success = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
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
    <link rel="stylesheet" href="styleUserDesign.css?v=<?php echo filemtime('styleUserDesign.css'); ?>">
    <link rel="stylesheet" href="styleSettings.css?v=<?php echo filemtime('styleSettings.css'); ?>">
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
                <button onclick="window.location.href='main_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" type="button" title="Powrót">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>
            <div>
                <button onclick="window.location.href='handler_logout.php'" type="button" title="Wyloguj">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </div>
        </div>

        <div class="action-container form-buttons-class">
            <form method="post" action="handler_account.php" id="account-form">
                <!-- Imię -->
                <div>
                    <label>Imię:</label>
                    <span id="name-display"><?= htmlspecialchars($user_name) ?></span>
                    <div id="name-edit" class="edit-field">
                        <input type="text" id="edit-name" name="name" value="<?= htmlspecialchars($user_name) ?>">
                        <button type="button" class="cancel-btn" onclick="cancelEdit('name')"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <button type="button" class="edit-btn" onclick="toggleEdit('name')"><i class="fa-solid fa-pen"></i></button>
                </div>

                <!-- Nazwisko -->
                <div>
                    <label>Nazwisko:</label>
                    <span id="surname-display"><?= htmlspecialchars($user_surname) ?></span>
                    <div id="surname-edit" class="edit-field">
                        <input type="text" id="edit-surname" name="surname" value="<?= htmlspecialchars($user_surname) ?>">
                        <button type="button" class="cancel-btn" onclick="cancelEdit('surname')"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <button type="button" class="edit-btn" onclick="toggleEdit('surname')"><i class="fa-solid fa-pen"></i></button>
                </div>

                <!-- Login -->
                <div>
                    <label>Login:</label>
                    <span id="login-display"><?= htmlspecialchars($user_login) ?></span>
                    <input type="hidden" id="login-input" name="login" value="<?= htmlspecialchars($user_login) ?>">
                </div>

                <!-- Index -->
                <div>
                    <label>Index:</label>
                    <span id="index-display"><?= htmlspecialchars($user_index) ?></span>
                    <input type="hidden" id="index-input" name="index" value="<?= htmlspecialchars($user_index) ?>">
                </div>

                <!-- Mail -->
                <div>
                    <label>e-mail:</label>
                    <span id="mail-display"><?= htmlspecialchars($user_mail) ?></span>
                    <div id="mail-edit" class="edit-field">
                        <input type="text" id="edit-mail" name="mail" value="<?= htmlspecialchars($user_mail) ?>">
                        <button type="button" class="cancel-btn" onclick="cancelEdit('mail')"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <button type="button" class="edit-btn" onclick="toggleEdit('mail')"><i class="fa-solid fa-pen"></i></button>
                </div>

                <!-- Kraj -->
                <div>
                    <label>Kraj biznesowy:</label>
                    <span id="country-display"><?= htmlspecialchars($user_country) ?></span>
                    <div id="country-edit" class="edit-field">
                        <select id="edit-country" name="country">
                            <?php foreach ($countryMap as $code => $name): ?>
                                <option value="<?= htmlspecialchars($name) ?>" <?= ($user_country === $name) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="cancel-btn" onclick="cancelEdit('country')"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <button type="button" class="edit-btn" onclick="toggleEdit('country')"><i class="fa-solid fa-pen"></i></button>
                </div>

                <!-- Hasło -->
                <div>
                    <label>Hasło:</label>
                    <span id="password-display">********</span>
                    <div id="password-edit" class="edit-field">
                        <input type="hidden" name="redirect_to" value="settings_panel.php">
                        <input type="password" name="old_password" placeholder="Stare hasło" autocomplete="new-password"/>
                        <input type="password" name="new_password" placeholder="Nowe hasło" class="new-password" autocomplete="new-password"/>
                        <input type="password" name="new_repeat_password" placeholder="Powtórz nowe hasło" class="new-repeat-password" autocomplete="new-password"/>
                        
                    </div>
                    <button type="button" id="cancel-btn-id" class="cancel-btn" onclick="cancelEdit('password')"><i class="fa-solid fa-xmark"></i></button>
                    <button type="button" id="edit-password-btn" onclick="toggleEdit('password')"><i class="fa-solid fa-pen"></i></button>
                </div>

                <div id="form-buttons">
                    <!-- Submit do danych -->
                    <button type="submit" id="submit-data-btn" title="Zatwierdź"><i class="fa-solid fa-circle-check"></i></button>
                    <!-- Submit do hasła, domyślnie ukryty -->
                    <button type="submit" id="submit-password-btn" formaction="handler_new_password.php" style="display:none;" title="Zatwierdź"><i class="fa-solid fa-circle-check"></i></button>
                    <button type="reset" class="reset-btn" title="Wyczyść"><i class="fa-solid fa-eraser"></i></button>
                </div>

            </form>
        </div>
    </div>
</main>
<footer>
    <div>Skill&Chill - II etap rekrutacji - Szulińska Weronika</div>
</footer>

<script>
    const fldName = document.getElementById('edit-name');
    const fldSurname = document.getElementById('edit-surname');
    const fldMail = document.getElementById('edit-mail');
    const fldCountry = document.getElementById('edit-country');
    const buttonCancelPass = document.getElementById('cancel-btn-id');

    const originalName = "<?= addslashes($user_name) ?>";
    const originalSurname = "<?= addslashes($user_surname) ?>";
    const originalMail = "<?= addslashes($user_mail) ?>";
    const originalCountry = "<?= addslashes($user_country) ?>";

    const editButtons = document.querySelectorAll('.edit-btn');
    const passwordBtn = document.getElementById('edit-password-btn');
    const submitData = document.getElementById('submit-data-btn');
    const submitPass = document.getElementById('submit-password-btn');

    function normalizeString(str) {
        return str.normalize('NFD').replace(/[\u0300-\u036f]/g,'');
    }

    function updateLogin() {
        const name = fldName.value.trim() || originalName;
        const surname = fldSurname.value.trim() || originalSurname;
        const login = normalizeString(name.charAt(0).toLowerCase()) + normalizeString(surname.toLowerCase().replace(/\s+/g,''));
        document.getElementById('login-display').textContent = login;
        document.getElementById('login-input').value = login;
    }

    function toggleEdit(field) {
        const editField = document.getElementById(field + '-edit');
        const displayField = document.getElementById(field + '-display');

        editField.style.display = 'flex';
        displayField.style.display = 'none';
        document.getElementById('form-buttons').style.display = 'flex';

        if (field === 'password') {
            editButtons.forEach(btn => btn.style.display = 'none');
            buttonCancelPass.style.display = 'flex';
            passwordBtn.style.display = 'flex';
            submitData.style.display = 'none';
            submitPass.style.display = 'flex';
        } else {
            passwordBtn.style.display = 'none';
            buttonCancelPass.style.display = 'none';
            submitData.style.display = 'flex';
            submitPass.style.display = 'none';
        }
    }

    function cancelEdit(field) {
        const editField = document.getElementById(field + '-edit');
        const displayField = document.getElementById(field + '-display');

        editField.style.display = 'none';
        displayField.style.display = 'flex';

        if (field === 'name') fldName.value = originalName;
        if (field === 'surname') fldSurname.value = originalSurname;
        if (field === 'mail') fldMail.value = originalMail;
        if (field === 'country') fldCountry.value = originalCountry;
        if (field === 'password') {
            document.querySelector('#password-edit input[name="old_password"]').value = '';
            document.querySelector('#password-edit input[name="new_password"]').value = '';
            document.querySelector('#password-edit input[name="new_repeat_password"]').value = '';
            buttonCancelPass.style.display = 'none';
        }

        editButtons.forEach(btn => btn.style.display = 'flex');
        passwordBtn.style.display = 'flex';

        submitData.style.display = 'flex';
        submitPass.style.display = 'none';

        updateLogin();
    }

    fldName.addEventListener('input', updateLogin);
    fldSurname.addEventListener('input', updateLogin);
    document.addEventListener('DOMContentLoaded', () => {
        submitData.style.display = 'flex';
        submitPass.style.display = 'none';
        editButtons.forEach(btn => btn.style.display = 'flex');
        passwordBtn.style.display = 'flex';
        document.getElementById('form-buttons').style.display = 'none';
    });
</script>

</body>
</html>
