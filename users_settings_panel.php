<?php
    session_start();
    require_once 'config.php';
    require_once 'country_map.php';

    if (empty($_SESSION['user'])) {
        header("Location: sign_in.php");
        exit;
    }

    $user_access = $_SESSION['access'];
    $user_first_login = $_SESSION['firstLogin'];
    $user_id = $_SESSION['id_user'];
    $error = $_SESSION['error'] ?? '';
    $success = $_SESSION['success'] ?? '';
    unset($_SESSION['error']);
    unset($_SESSION['success']);

    $stmt = $pdo->prepare("
        SELECT *
        FROM company_employees ce
        JOIN portal_user pu ON ce.id = pu.employees_id
        JOIN user_attempts ua ON pu.id_user = ua.user_id
        WHERE ce.registered = 1
        AND pu.access <= ?
        AND pu.id_user != ?
        ORDER BY ce.name ASC, ce.surname ASC
    ");

    $stmt->execute([$user_access, $user_id]);
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

    <link rel="icon" href="favicon.ico" type="image/x-icon">

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
                <?php if (!empty($personnel) && $_SESSION['access'] >= 4): ?>
                    <form method="post">
                        <input type="hidden" name="redirect_to" value="users_settings_panel.php?user=<?= urlencode($_SESSION['user']) ?>">
                        <table>
                            <tr class="table-header">
                                <th colspan="11">Użytkownicy aktywni</th>
                            </tr>
                            <tr class="tr-checbox table-actions">
                                <th><input type="checkbox" id="master-checkbox"></th>
                                <th>
                                    <div id="delete-all" style="display:none">
                                        <button type="submit" formaction="handler_delete_user.php" name="delete-all" title="Usuń zaznaczonych użytkowników">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </th>
                                <th>
                                    <div id="lock-all" style="display:none">
                                        <button type="submit" formaction="handler_lock_user.php" name="lock-all" title="Zablokuj zaznaczonych użytkowników">
                                            <i class="fa-solid fa-lock"></i>
                                        </button>
                                    </div>
                                </th>
                                <th>
                                    <div id="reset-all" style="display:none">
                                        <button type="submit" formaction="handler_reset_password.php" name="reset-all" title="Zresetuj hasła zaznaczonych użytkowników">
                                            <i class="fa-solid fa-key"></i>
                                        </button>
                                    </div>
                                </th>
                                <th colspan="7"></th>
                            </tr>
                            <tr class="table-columns">
                                <th></th>
                                <th>Imię</th>
                                <th>Nazwisko</th>
                                <th>Login</th>
                                <th>Email</th>
                                <th>Index</th>
                                <th>Kraj</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <?php foreach ($personnel as $p): ?>
                                <?php if ((int)$p['account_access'] !== 1 && (int)$p['access'] <= $_SESSION['access']): ?>
                                <tr>
                                    <td>
                                        <?php if ($p['access'] < $_SESSION['access']): ?>
                                            <input type="checkbox" class="table-checkbox" name="selected_ids[]" value="<?= $p['id'] ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td><?= htmlspecialchars($p['surname']) ?></td>
                                    <td><?= htmlspecialchars($p['login'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['mail']) ?></td>
                                    <td><?= htmlspecialchars($p['employees_index'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($p['country'] ?? '') ?></td>
                                    <td>
                                        <?php if ($p['access'] < $_SESSION['access']): ?>
                                            <button type="submit" formaction="handler_delete_user.php" name="delete" value="<?= $p['id'] ?>" title="Usuń użytkownika">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($p['access'] < $_SESSION['access']): ?>
                                            <button type="submit" formaction="handler_lock_user.php" name="lock" value="<?= $p['id_user'] ?>" title="Zablokuj użytkownika">
                                                <i class="fa-solid fa-lock"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button type="submit" formaction="handler_reset_password.php" name="reset" value="<?= $p['id_user'] ?>" title="Zresetuj hasło użytkownika">
                                            <i class="fa-solid fa-key"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <?php if ($p['access'] < $_SESSION['access']): ?>
                                            <button type="button"
                                                class="edit-btn"
                                                data-id="<?= (int)$p['id'] ?>" 
                                                data-name="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>"
                                                data-surname="<?= htmlspecialchars($p['surname'], ENT_QUOTES) ?>"
                                                data-login="<?= htmlspecialchars($p['login'] ?? '', ENT_QUOTES) ?>"
                                                data-mail="<?= htmlspecialchars($p['mail'], ENT_QUOTES) ?>"
                                                data-index="<?= htmlspecialchars($p['employees_index'] ?? '', ENT_QUOTES) ?>"
                                                data-country="<?= htmlspecialchars($p['country'] ?? '', ENT_QUOTES) ?>"
                                                data-access="<?= (int)($p['access'] ?? 0) ?>"
                                                title="Edytuj użytkownika">
                                                <i class="fa-solid fa-gear"></i>
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </table>
                    </form>
                <?php endif; ?>

                <div id="edit-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
                    <div id="edit-modal-form" style="background:white; padding:20px; max-width:500px; margin:50px auto; border-radius:8px;">
                        <div>
                        <button type="button" id="edit-cancel" title="Anuluj">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        </div>
                        <div>
                        <form id="edit-form" method="post" action="handler_edit_user.php">
                            <input type="hidden" name="user_id" id="edit-user-id">

                            <label>Imię: 
                            <input type="text" name="name" id="edit-name">
                            </label><br>

                            <label>Nazwisko: 
                            <input type="text" name="surname" id="edit-surname">
                            </label><br>

                            <label>Email: 
                            <input type="email" name="mail" id="edit-mail">
                            <span id="error-mail" style="color:red; display:none;">Email zajęty</span>
                            </label><br>

                            <label>Login: 
                            <input type="text" name="login" id="edit-login" readonly>
                            </label><br>

                            <label>Index: 
                            <input type="text" name="employees_index" id="edit-index" readonly>
                            <span id="error-index" style="color:red; display:none;">Index zajęty</span>
                            </label><br>

                            <label>Kraj:
                                <select name="country" id="edit-country">
                                    <option value="">-- Wybierz kraj --</option>
                                    <?php
                                    require_once 'country_map.php';
                                    foreach($countryMap as $code => $name){
                                        echo '<option value="'.htmlspecialchars($code).'">'.htmlspecialchars($name).'</option>';
                                    }
                                    ?>
                                </select>
                                <span id="error-country" style="color:red; display:none;">Kraj nie jest jeszcze dostępny</span>
                            </label><br>


                            <label>Poziom dostępu:
                            <select name="access_level" id="edit-access">
                                <option value="0">Pracownik dystrybutora</option>
                                <option value="1">Dystrybutor</option>
                                <option value="2">Export manager</option>
                                <option value="3">Administrator</option>
                                <option value="4">Super-administrator</option>
                            </select>
                            </label><br><br>

                            <button type="submit" title="Zatwierdź">
                            <i class="fa-solid fa-circle-check"></i>
                            </button>
                        </form>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </main>
    <footer>
        <div>Skill&Chill - II etap rekrutacji - Szulińska Weronika</div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const masterCheckbox = document.getElementById('master-checkbox');
            const deleteButton = document.getElementById("delete-all");
            const lockButton = document.getElementById("lock-all");
            const resetButton = document.getElementById("reset-all");
            const checkboxes = document.querySelectorAll('input.table-checkbox');
            const countryMap = <?= json_encode($countryMap) ?>;

            function toggleButtons() {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                deleteButton.style.display = anyChecked ? 'block' : 'none';
                lockButton.style.display = anyChecked ? 'block' : 'none';
                resetButton.style.display = anyChecked ? 'block' : 'none';
            }

            if (masterCheckbox) {
                masterCheckbox.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    toggleButtons();
                });
            }

            checkboxes.forEach(cb => cb.addEventListener('change', toggleButtons));
            toggleButtons();
            
            const editButtons = document.querySelectorAll('.edit-btn');
            const editModal = document.getElementById('edit-modal');
            const editForm = document.getElementById('edit-form');

            const fldName = document.getElementById('edit-name');
            const fldSurname = document.getElementById('edit-surname');
            const fldLogin = document.getElementById('edit-login');

            const errMail = document.getElementById('error-mail');
            const errIndex = document.getElementById('error-index');

            function normalizeString(str) {
                const map = {
                ą:"a", ć:"c", ę:"e", ł:"l", ń:"n", ó:"o", ś:"s", ź:"z", ż:"z",
                Ą:"a", Ć:"c", Ę:"e", Ł:"l", Ń:"n", Ó:"o", Ś:"s", Ź:"z", Ż:"z"
                };
                return str.split("").map(ch => map[ch] || ch).join("");
            }

            function generateLogin() {
                const name = fldName.value.trim();
                const surname = fldSurname.value.trim();
                if (name && surname) {
                const firstLetter = normalizeString(name.charAt(0).toLowerCase());
                const cleanSurname = normalizeString(surname.toLowerCase().replace(/\s+/g, ''));
                fldLogin.value = firstLetter + cleanSurname;
                } else {
                fldLogin.value = "";
                }
            }

            const fldIndex = document.getElementById('edit-index');
            const fldCountry = document.getElementById('edit-country');

            async function generateIndex() {
                const country = fldCountry.value.trim().toUpperCase();
                const oldIndex = fldIndex.value.trim();

                if (!country) {
                    fldIndex.value = "";
                    document.getElementById('error-country').style.display = "inline";
                    return;
                } else {
                    document.getElementById('error-country').style.display = "none";
                }

                const numberPart = oldIndex.match(/\d{4}/)?.[0] || ("0" + (new Date().getMonth() + 1)).slice(-2) + Math.floor(Math.random() * 90 + 10);

                let newIndex;
                let isUnique = false;

                while (!isUnique) {
                    const randomTwoDigits = Math.floor(Math.random() * 90 + 10);
                    newIndex = country + numberPart + randomTwoDigits;

                    // Sprawdzamy w bazie przez AJAX
                    const response = await fetch('handler_check_index.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({index: newIndex})
                    });
                    const data = await response.json();
                    if (data.unique) isUnique = true;
                }

                fldIndex.value = newIndex;
            }

            fldCountry.addEventListener('change', generateIndex);



            editButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const row = btn.closest('tr');
                    const id = btn.dataset.id;

                    document.getElementById('edit-user-id').value = id;
                    document.getElementById('edit-name').value = row.children[1].textContent.trim();
                    document.getElementById('edit-surname').value = row.children[2].textContent.trim();
                    document.getElementById('edit-mail').value = row.children[4].textContent.trim();
                    document.getElementById('edit-login').value = row.children[3].textContent.trim();
                    document.getElementById('edit-index').value = row.children[5].textContent.trim();
                    document.getElementById('edit-access').value = row.dataset.access || 0;

                    // Pobranie nazwy kraju z tabeli
                    const countryName = row.children[6].textContent.trim();
                    let countryCode = '';

                    // Szukamy skrótu kraju w countryMap
                    for (const code in countryMap) {
                        if (countryMap[code] === countryName) {
                            countryCode = code;
                            break;
                        }
                    }

                    const countrySelect = document.getElementById('edit-country');
                    countrySelect.value = countryCode;

                    generateLogin();
                    generateIndex(); // aktualizacja indeksu z nowym krajem
                    editModal.style.display = 'block';
                });
            });


            document.getElementById('edit-cancel').addEventListener('click', () => {
                editModal.style.display = 'none';
            });

            fldName.addEventListener('input', generateLogin);
            fldSurname.addEventListener('input', generateLogin);

            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                errMail.style.display = "none";
                errIndex.style.display = "none";

                const formData = new FormData(editForm);

                fetch(editForm.action, {
                method: 'POST',
                body: formData
                })
                .then(res => res.json())
                .then(data => {
                if (data.status === 'error') {
                    if (data.field === 'mail') errMail.style.display = "inline";
                    if (data.field === 'employees_index') errIndex.style.display = "inline";
                } else if (data.status === 'success') {
                    location.reload();
                }
                })
                .catch(err => console.error(err));
            });
        });
    </script>
</body>
</html>