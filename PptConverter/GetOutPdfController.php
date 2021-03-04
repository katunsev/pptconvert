<?php
namespace PptConverter;

use Exception;

/**
 * Класс для конвертации файлов PPT(X) в PDF через getoutpdf.com api.
 *
 * @package     prodamus
 * @copyright   2021 Prodamus Ltd. http://prodamus.ru/
 * @author      Dmitriy Katunsev <katunsev@bk.ru>
 * @version     1.0
 * @since       04.03.2021
 */

class GetOutPdfController extends PptConverter {

    /**
     * URL для запроса API
     * @var string
     */
    const URL = 'https://getoutpdf.com/api/convert/document-to-pdf';

    /**
     * Сконвертировать входящий файл в PDF
     *
     * @param string $fileName путь к исходному файлу
     * @throws Exception
     */
    public function convert(string $fileName)
    {
        parent::convert($fileName);

        $base64file = base64_encode(file_get_contents($fileName));

        $response = $this->sendRequest($base64file);
        $result = $this->getResponse($response);
        $base64pdf = $this->decode($result);
        $this->save($base64pdf);

        parent::saveImages();
    }

    /**
     * Формирование и отправка запроса
     *
     * @param string $base64file base64 закодированный файл PPT(X)
     * @return resource
     */
    private function sendRequest(string $base64file)
    {
        $data = ['api_key' => $this->config['getoutpdf']['token'], 'document' => $base64file];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        return stream_context_create($options);
    }

    /**
     * Формирование и отправка запроса
     *
     * @param $response string ответ API
     * @return string
     * @throws Exception
     */
    private function getResponse($response)
    {
        $result = file_get_contents(self::URL, false, $response);
        if ($result === FALSE) {
            throw new Exception('Empty response');
        }

        return $result;
    }

    /**
     * Получить base64 PDF файл
     *
     * @param string $result json ответ API
     * @return string
     */
    private function decode(string $result)
    {
        return json_decode($result, true)['pdf_base64'];
    }

    /**
     * Сохранить файл PDF в заданный каталог
     *
     * @param $data string содержимое файла PDF
     * @return string
     */
    private function save(string $data)
    {
        return file_put_contents($this->pdfdir . $this->name . '.pdf', $data);
    }
}