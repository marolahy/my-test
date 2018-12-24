<?php
require_once(dirname(__FILE__)).'/../vendor/Autoload.php';

header('Content-Type: application/xml');

$csmb = new MindGeek\Board\Csmb();
if (array_key_exists('file', $_FILES)) {
    echo $csmb->execute(file_get_contents($_FILES['file']['tmp_name']));
}

die();
