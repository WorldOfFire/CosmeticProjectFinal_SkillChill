<?php
session_start();

if (empty($_SESSION['user'])) {
    header("Location: sign_in.php");
    exit;
}

$quarter_filter = $_GET['quarter'] ?? '';

function getCsvFiles($dir, $quarter = '') {
    $files = [];
    if (!is_dir($dir)) return $files;

    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir.'/'.$file;
        if (!is_file($path)) continue;
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'csv') continue;

        if ($quarter && !preg_match('/Q'.$quarter.'_/i', $file)) continue;

        $files[] = [
            'name' => $file,
            'path' => str_replace(__DIR__.'/','',$path),
            'size' => filesize($path),
            'date' => filemtime($path),
            'type' => mime_content_type($path)
        ];
    }

    usort($files, fn($a,$b) => $b['date'] - $a['date']);
    return $files;
}

$date_dir = __DIR__ . '/DATE';
$all_files = [
    'PURCHASE_REPORT' => getCsvFiles($date_dir.'/PURCHASE_REPORT', $quarter_filter),
    'SALES_CHANNELS' => getCsvFiles($date_dir.'/SALES_CHANNELS', $quarter_filter)
];
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
    <link rel="stylesheet" href="styleMedia.css?v=<?php echo filemtime('styleMedia.css'); ?>">
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
            <!-- Panel przycisków -->
            <div class="return-container">
                <div>
                    <button onclick="window.location.href='main_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" title="Powrót">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
                <div>
                    <button onclick="window.location.href='settings_panel.php?user=<?= urlencode($_SESSION['user']) ?>'" title="Ustawienia konta">
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <button onclick="window.location.href='handler_logout.php'" title="Wyloguj">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </div>
            </div>

            <div class="action-container">
                <!-- Formularz filtra -->
                <section class="filter-section">
                    <form method="GET" class="filter-form">
                        <label>Kwartał:
                            <select name="quarter">
                                <option value="" <?= $quarter_filter==''?'selected':'' ?>>Wszystkie</option>
                                <option value="1" <?= $quarter_filter=='1'?'selected':'' ?>>Q1</option>
                                <option value="2" <?= $quarter_filter=='2'?'selected':'' ?>>Q2</option>
                                <option value="3" <?= $quarter_filter=='3'?'selected':'' ?>>Q3</option>
                                <option value="4" <?= $quarter_filter=='4'?'selected':'' ?>>Q4</option>
                            </select>
                        </label>
                        <button type="submit" class="filter-btn" title="Filtruj">
                            <i class="fa-solid fa-filter"></i>
                        </button>
                    </form>
                </section>

                <!-- Tabela CSV -->
                <?php foreach($all_files as $folder_name => $files): ?>
                    <?php if(count($files) > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr class="table-header">
                                        <th colspan="5"><?= $folder_name ?></th>
                                    </tr>
                                    <tr class="table-columns">
                                        <th>Nazwa pliku</th>
                                        <th>Typ</th>
                                        <th>Rozmiar (KB)</th>
                                        <th>Data dodania</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($files as $file): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($file['name']) ?></td>
                                        <td><?= htmlspecialchars($file['type']) ?></td>
                                        <td><?= round($file['size']/1024,2) ?></td>
                                        <td><?= date('Y-m-d H:i:s', $file['date']) ?></td>
                                        <td><a href="<?= htmlspecialchars($file['path']) ?>" download><i class="fa-solid fa-download"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>Brak plików CSV w folderze <?= $folder_name ?> dla wybranego kwartału.</p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <footer>
        <div>Skill&amp;Chill - II etap rekrutacji - Szulińska Weronika</div>
    </footer>
</body>
</html>
