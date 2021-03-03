<?php
namespace PptConverter;

use Exception;

class GetOutPdfController extends PptConverter {

    const URL = 'https://getoutpdf.com/api/convert/document-to-pdf';

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

    private function getResponse($response)
    {
        $result = file_get_contents(self::URL, false, $response);
        if ($result === FALSE) {
            throw new Exception('Empty response');
        }

        return $result;
    }

    private function decode(string $result)
    {
        return json_decode($result, true)['pdf_base64'];
    }

    private function save($data)
    {
        return file_put_contents($this->pdfdir . $this->name . '.pdf', $data);
    }
}