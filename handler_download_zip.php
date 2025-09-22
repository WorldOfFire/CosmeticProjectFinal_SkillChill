<?php
    session_start();

    if (empty($_SESSION['user'])) {
        header("Location: sign_in.php");
        exit;
    }

    if (!empty($_POST['files'])) {
        $files_to_zip = $_POST['files'];

        if (count($files_to_zip) === 0) {
            die("Nie zaznaczono żadnych plików.");
        }

        $zip_name = 'download_' . date('Ymd_His') . '.zip';
        $zip = new ZipArchive();

        $tmp_file = tempnam(sys_get_temp_dir(), 'zip');
        if ($zip->open($tmp_file, ZipArchive::CREATE) !== TRUE) {
            die("Nie można utworzyć pliku ZIP.");
        }

        foreach ($files_to_zip as $file) {
            $full_path = __DIR__ . '/' . $file;
            if (file_exists($full_path) && is_file($full_path)) {
                $zip->addFile($full_path, basename($file));
            }
        }

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_name . '"');
        header('Content-Length: ' . filesize($tmp_file));

        readfile($tmp_file);
        unlink($tmp_file);
        exit;
    }

    die("Brak plików do pobrania.");
?>