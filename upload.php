<?php
include 'autoload.php';

use PptConverter\LibreConverter;
use PptConverter\GetOutPdfController;

$tempDir = 'temp';

$uploaddir = __DIR__ . '/' . $tempDir . '/upload/';
$file = pathinfo($_FILES['file']['name']);
$fileName = time() . '.' . $file['extension'];

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


