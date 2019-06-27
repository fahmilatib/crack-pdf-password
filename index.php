<?php

use App\PdfPasswordCracker;

require __DIR__ . '/vendor/autoload.php';

$pdfPasswordCracker = new PdfPasswordCracker;

if ($pdfPasswordCracker->minAge(28)->maxAge(28)->crack()) {
    echo 'Password: ' . $pdfPasswordCracker->getPassword();
} else {
    echo 'Meh.';
}
