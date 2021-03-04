<?php
include 'autoload.php';

use PptConverter\LibreConverter;
use PptConverter\GetOutPdfController;

$uploaddir = __DIR__ . '/temp/upload/';
$fileName = time() . '.' . pathinfo($_FILES['file']['name'])['extension'];

if(!file_exists($uploaddir)) mkdir($uploaddir, 0777, true);

$uploadfile = $uploaddir . $fileName;

if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
    try {
        //$converter = new GetOutPdfController();
        $converter = new LibreConverter();
        $converter->convert($uploadfile);
    } catch (Exception $exception) {
        echo $exception->getMessage();
    }
}


