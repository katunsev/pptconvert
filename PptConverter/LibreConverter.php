<?php
namespace PptConverter;

class LibreConverter extends PptConverter {

    public function convert(string $fileName)
    {
        parent::convert($fileName);

        exec($this->createCommand($fileName));

        parent::saveImages();
    }

    private function createCommand($fileName)
    {
        return $this->config['libreoffice']['path'] . ' --headless --convert-to pdf ' . $fileName . ' --outdir ' . $this->pdfdir;
    }
}