<?php

namespace OnlineCashDesk\Dreamkas\v1;

use OnlineCashDesk\ApiResponse;

class ApiCaller implements \OnlineCashDesk\ApiCaller
{
    const API_ENDPOINT = 'https://kabinet.dreamkas.ru/api/';

    private $token = null;


    public function __construct(array $config)
    {
        if (empty($config['token'])) {
            throw new \LogicException('Online cash desk config does not contain required token credential');
        }

        $this->token = $config['token'];
    }

    public function sendReceipt(array $receiptData)
    {
        $methodResponse = new ApiResponse();

        $apiResponse = $this->makeRequest('receipts', $receiptData);

        if (!$apiResponse) {
            return false;
        }

        // Сохраняем ответ от Dreamkas
        $methodResponse->setResponseData($apiResponse);

        if (empty($apiResponse['status']) || !in_array($apiResponse['status'], ['ERROR', 'PENDING'])) {
            return $methodResponse->setError('Получен неожиданный статус результата запроса к API: '
                . (!empty($apiResponse['status']) ? $apiResponse['status'] : '-'));
        }

        if (empty($apiResponse['id'])) {
            return $methodResponse->setError('В ответе отсутствует уникальный идентификатор чека в система Dreamkas (параметр id)');
        }

        if ('PENDING' === $apiResponse['status']) {
            $methodResponse->setReceiptStatus(\OnlineCashDesk\Receipt::STATUS_PENDING);
        } else {
            $methodResponse->setError(!empty($response['data']) ? $response['data'] : 'Неизвестная ошибка при попытке регистрации чека');
        }

        return $methodResponse;
    }

    public function checkReceiptStatus(array $receiptData, array $lastResponseData)
    {
        $methodResponse = new ApiResponse();

        $apiResponse = $this->makeRequest('operations/' . $lastResponseData['id'], null, 'GET');

        if (!$apiResponse) {
            return false;
        }

        // Сохраняем ответ от Dreamkas
        $methodResponse->setResponseData($apiResponse);

        if (empty($apiResponse['status']) || !in_array($apiResponse['status'], ['ERROR', 'PENDING', 'SUCCESS'])) {
            return $methodResponse->setError('Получен неожиданный статус результата запроса к API: '
                . (!empty($apiResponse['status']) ? $apiResponse['status'] : '-'));
        }

        if ('SUCCESS' === $apiResponse['status']) {
            $methodResponse->setReceiptStatus(\OnlineCashDesk\Receipt::STATUS_REGISTERED);
        } elseif ('ERROR' === $apiResponse['status']) {
            $methodResponse->setError(!empty($response['data']) ? $response['data'] : 'Неизвестная ошибка при попытке регистрации чека');
        }

        return $methodResponse;
    }

    private function makeRequest($path, $data, $method = 'POST')
    {
        $ch = curl_init();

        try {
            curl_setopt($ch, CURLOPT_URL, self::API_ENDPOINT);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->token,
            ]);

            if (defined('APPLICATION_ENV') && ('development' !== APPLICATION_ENV)) {
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 1000);
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10000);
            }

            if ('POST' === $method) {
                $params = json_encode($data);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            }

            $response = curl_exec($ch);
            $curlErrorCode = curl_errno($ch);

            if ($curlErrorCode) {
                $result = null;
            } else {
                $result = json_decode($response, true);
            }
        } catch (\Exception $e) {
            $result = null;
        }

        curl_close($ch);

        return $result;
    }
}