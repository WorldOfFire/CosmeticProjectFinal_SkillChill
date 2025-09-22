<?php
session_start();

if (empty($_SESSION['user'])) {
    header("Location: sign_in.php");
    exit;
}

$user_first_login = $_SESSION['firstLogin'] ?? 0;

// Funkcja do pobrania plików z katalogu i podfolderów
function getFilesRecursive($dir, $sku = '') {
    $files = [];
    if (!is_dir($dir)) return $files;

    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        $filename = $file->getFilename();
        if ($sku && stripos($filename, $sku) === false) continue;
        $files[] = [
            'path' => str_replace(__DIR__ . '/', '', $file->getPathname()),
            'name' => $filename,
            'size' => $file->getSize(),
            'type' => mime_content_type($file->getPathname()),
            'date' => $file->getMTime()
        ];
    }
    return $files;
}

$sku_filter = $_GET['sku'] ?? '';
$sort_by = $_GET['sort'] ?? 'date';
$sort_order = $_GET['order'] ?? 'desc';

// Pobranie wszystkich folderów w MEDIA
$media_dir = __DIR__ . '/MEDIA';
$folders = array_filter(glob($media_dir.'/*'), 'is_dir');

$all_files = [];
foreach ($folders as $folder) {
    $folder_name = basename($folder);
    $files = getFilesRecursive($folder, $sku_filter);
    if ($sort_by) {
        usort($files, function($a, $b) use ($sort_by, $sort_order) {
            if ($a[$sort_by] == $b[$sort_by]) return 0;
            return ($sort_order === 'asc') ? ($a[$sort_by] < $b[$sort_by] ? -1 : 1) : ($a[$sort_by] > $b[$sort_by] ? -1 : 1);
        });
    }
    $all_files[$folder_name] = $files;
}
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
                    <section class="filer-section">
                        <form method="GET" class="filter-form">
                            <label>SKU: <input type="text" name="sku" value="<?= htmlspecialchars($sku_filter) ?>"></label>
                            <label>Sortuj według: 
                                <select name="sort">
                                    <option value="date" <?= $sort_by=='date'?'selected':'' ?>>Data</option>
                                    <option value="size" <?= $sort_by=='size'?'selected':'' ?>>Rozmiar</option>
                                    <option value="type" <?= $sort_by=='type'?'selected':'' ?>>Typ</option>
                                </select>
                            </label>
                            <label>Kolejność:
                                <select name="order">
                                    <option value="asc" <?= $sort_order=='asc'?'selected':'' ?>>Rosnąco</option>
                                    <option value="desc" <?= $sort_order=='desc'?'selected':'' ?>>Malejąco</option>
                                </select>
                            </label>
                            <button type="submit" class="filter-btn" title="Filtruj">
                                <i class="fa-solid fa-filter"></i>
                            </button>
                        </form>
                    </section>

                    <?php foreach ($all_files as $folder_name => $files): ?>
                    <?php if(count($files) > 0): ?>
                    <form method="POST" action="handler_download_zip.php">
                        <table>
                            <thead>
                                <tr class="table-header">
                                    <th colspan="6">
                                        <?= htmlspecialchars($folder_name) ?>
                                    </th>
                                </tr>
                                <tr class="tr-checbox table-actions">
                                    <th><input type="checkbox" class="select_all" data-folder="<?= $folder_name ?>"></th>
                                    <th colspan="5" class="button-download_bth">
                                        <button type="submit" id="download_btn_<?= $folder_name ?>" style="display:none">
                                            <i class="fa-solid fa-download"></i>
                                        </button>
                                    </th>
                                </tr>
                                <tr class="table-columns">
                                    <th></th>
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
                                    <td><input type="checkbox" class="file_checkbox_<?= $folder_name ?>" name="files[]" value="<?= htmlspecialchars($file['path']) ?>"></td>
                                    <td><?= htmlspecialchars($file['name']) ?></td>
                                    <td><?= htmlspecialchars($file['type']) ?></td>
                                    <td><?= round($file['size']/1024,2) ?></td>
                                    <td><?= date('Y-m-d H:i:s', $file['date']) ?></td>
                                    <td><a href="handler_download.php?file=<?= urlencode($file['path']) ?>"><i class="fa-solid fa-download"></i></a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </form>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                </div>
            </div>
        </main>

        <footer>
            <div>Skill&Chill - II etap rekrutacji - Szulińska Weronika</div>
        </footer>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                <?php foreach ($all_files as $folder_name => $files): ?>
                const master_<?= $folder_name ?> = document.querySelector('.select_all[data-folder="<?= $folder_name ?>"]');
                const downloadBtn_<?= $folder_name ?> = document.getElementById('download_btn_<?= $folder_name ?>');
                const checkboxes_<?= $folder_name ?> = Array.from(document.getElementsByClassName('file_checkbox_<?= $folder_name ?>'));

                function updateButton_<?= $folder_name ?>() {
                    const anyChecked = checkboxes_<?= $folder_name ?>.some(cb => cb.checked);
                    const allChecked = checkboxes_<?= $folder_name ?>.every(cb => cb.checked);
                    downloadBtn_<?= $folder_name ?>.style.display = anyChecked ? 'inline-block' : 'none';
                    master_<?= $folder_name ?>.checked = allChecked;
                }

                master_<?= $folder_name ?>.addEventListener('change', () => {
                    checkboxes_<?= $folder_name ?>.forEach(cb => cb.checked = master_<?= $folder_name ?>.checked);
                    updateButton_<?= $folder_name ?>();
                });

                checkboxes_<?= $folder_name ?>.forEach(cb => cb.addEventListener('change', updateButton_<?= $folder_name ?>));
                updateButton_<?= $folder_name ?>();
                <?php endforeach; ?>
            });
        </script>
    </body>
</html>
