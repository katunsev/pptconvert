<?php
namespace PptConverter;

use Exception;
use Imagick;

class PptConverter {

    protected $config;
    protected $name;
    protected $pdfdir;

    public function __construct()
    {
        $this->checkConfig();
        $this->checkImagick();
        $this->pdfdir = $this->config['paths']['pdf'];

        $this->checkCatalog($this->pdfdir);
    }

    public function convert(string $fileName)
    {
        $this->name = pathinfo($fileName)['filename'];
        if(!in_array(pathinfo($fileName)['extension'], ['ppt', 'pptx'])) {
            throw new Exception('Need upload only *.ppt and *.pptx files');
        }
    }

    protected function saveImages()
    {
        $pathToPdf = $this->pdfdir . $this->name . '.pdf';

        $imagick = new Imagick();
        $imagick->readImage($pathToPdf);

        $catalog = $this->config['paths']['images'] . $this->name;

        $this->checkCatalog($catalog);

        $imagick->writeImages($catalog . '/' . $this->config['fileNameTemplate'], false);

        return $this;
    }

    private function checkConfig()
    {
        $this->config = require_once 'config.php';
        if(empty($this->config)) {
            throw new Exception('Configuration file does not exists');
        }

        return $this;
    }

    private function checkImagick()
    {
        if (!extension_loaded('imagick')){
            throw new Exception('Imagick extension not found');
        }

        return $this;
    }

    private function checkCatalog(string $catalog)
    {
        if(!file_exists($catalog)) mkdir($catalog, 0777, true);

        return $this;
    }
}