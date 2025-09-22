<?php
    $error = $_GET['error'] ?? '';
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

    <link rel="stylesheet" href="styleSingIn.css?v=<?php echo filemtime('styleSingIn.css'); ?>">
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
                    <button onclick="window.location.href='index.php'" type="button" id="return_button" title="Powrót">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
            </div>
            <div class="action-container">
                <form action="handler_login.php" method="post">
                    <label>Login
                        <input type="text" name="login" required/>
                    </label>
                    <label>Hasło
                        <input type="password" name="password" autocomplete="new-password" required/>
                    </label>
                    <div class="button-row">
                        <button type="reset" id="reset_button" title="Wyczyść formularz logowania">
                            <i class="fa-solid fa-eraser"></i>
                        </button>
                        <button type="submit" id="login_button" title="Zaloguj się">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </button>
                    </div>
                    <div class="error_container">
                        <?php if ($error): ?>
                            <div><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                    <div>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <div>Skill&Chill - II etap rekrutacji - Szulińska Weronika</div>
    </footer>
</body>
</html>
