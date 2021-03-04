<?php
namespace PptConverter;

use Exception;

/**
 * Класс для конвертации файлов PDF и PPT(X) в массив изображений.
 *
 * @package     prodamus
 * @copyright   2021 Prodamus Ltd. http://prodamus.ru/
 * @author      Dmitriy Katunsev <katunsev@bk.ru>
 * @version     1.0
 * @since       04.03.2021
 */

class PptConverter {

    /**
     * Массив конфигураций конвертера
     * @var array
     */
    protected $config;

    /**
     * Сгенерированное название файла
     * @var string
     */
    protected $name;

    /**
     * Полный путь к файлу PDF
     * @var string
     */
    protected $pdfdir;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->checkConfig();
        $this->pdfdir = $this->config['paths']['pdf'];

        $this->checkCatalog($this->pdfdir);
    }

    /**
     * Сконвертировать файл в PDF
     *
     * В зависимости от входя
     * @param string $fileName путь к исходному файлу
     *
     * @throws Exception
     */
    public function convert(string $fileName)
    {
        $this->name = pathinfo($fileName)['filename'];
        $extension = pathinfo($fileName)['extension'];

        if(!in_array($extension, ['ppt', 'pptx', 'pdf'])) {
            throw new Exception('Need upload only *.ppt, *.pptx and *.pdf files');
        } elseif($extension == 'pdf') {
            copy($fileName, $this->pdfdir . $this->name . '.pdf');
            $this->saveImages();
            exit;
        }
    }

    /**
     * Сохранение изображений в заданный каталог
     *
     * @return PptConverter
     * @throws Exception
     */
    protected function saveImages()
    {
        $pathToPdf = $this->pdfdir . $this->name . '.pdf';
        if(!file_exists($pathToPdf)) throw new Exception('File not exists');

        if (!is_readable($pathToPdf)) {
            throw new Exception('File not readable');
        }
        $catalog = $this->config['paths']['images'] . $this->name;

        $this->checkCatalog($catalog);
        $command = '/usr/local/bin/gs -sDEVICE=pngalpha -o ' . $catalog . '/' . $this->config['fileNameTemplate'] . ' -r96 ' . $pathToPdf;

        shell_exec($command);

        return $this;
    }

    /**
     * Загрузка конфигурации из файла
     *
     * @return PptConverter
     * @throws Exception
     */
    private function checkConfig()
    {
        $this->config = require_once 'config.php';
        if(empty($this->config)) {
            throw new Exception('Configuration file does not exists');
        }
        return $this;
    }

    /**
     * Проверка каталога на существание, и создание его в случае отсутствия
     *
     * @return PptConverter
     * @throws Exception
     */
    private function checkCatalog(string $catalog)
    {
        if(!file_exists($catalog)) mkdir($catalog, 0777, true);
        return $this;
    }
}