<?php
require_once(dirname(__FILE__)).'/../vendor/Autoload.php';

//header('Content-Type: application/json');

$csm = new MindGeek\Board\Csm();
if (array_key_exists('file', $_FILES)) {
    echo $csm->execute(file_get_contents($_FILES['file']['tmp_name']));
}

die();
