<?php
session_start();
if (empty($_SESSION['user'])) {
    header("Location: sign_in.php");
    exit;
}

if (!empty($_GET['file'])) {
    $file = __DIR__ . '/' . $_GET['file'];
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        die("Plik nie istnieje.");
    }
}

die("Brak pliku do pobrania.");
