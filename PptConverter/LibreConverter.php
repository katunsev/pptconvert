<?php
namespace PptConverter;

/**
 * Класс для конвертации файлов PPT(X) в PDF через libreoffice.
 *
 * @package     prodamus
 * @copyright   2021 Prodamus Ltd. http://prodamus.ru/
 * @author      Dmitriy Katunsev <katunsev@bk.ru>
 * @version     1.0
 * @since       04.03.2021
 */

class LibreConverter extends PptConverter {

    /**
     * Сконвертировать входящий файл в PDF
     *
     * @param string $fileName путь к исходному файлу
     */
    public function convert(string $fileName)
    {
        parent::convert($fileName);
        exec($this->createCommand($fileName));
        parent::saveImages();
    }

    /**
     * Сформировать команду для конвертации
     *
     * @param string $fileName путь к исходному файлу
     *
     * @return string
     */
    private function createCommand($fileName)
    {
        return $this->config['libreoffice']['path'] . ' --headless --convert-to pdf ' . $fileName . ' --outdir ' . $this->pdfdir;
    }
}