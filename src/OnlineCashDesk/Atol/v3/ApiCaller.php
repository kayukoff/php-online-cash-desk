<?php

/**
 * Под АТОЛ API версии 3.0
 */

namespace OnlineCashDesk\Atol\v3;


use OnlineCashDesk\ApiResponse;

class ApiCaller implements \OnlineCashDesk\ApiCaller
{
    const API_ENDPOINT = 'https://online.atol.ru/possystem/v3/';

    private $lastError = null;

    private $token = null;
    private $login = null;
    private $password = null;


    public function __construct(array $config)
    {
        if (empty($config['login']) || empty($config['pass'])) {
            throw new \LogicException('Online cash desk config does not contain required login credentials');
        }

        $this->login = $config['login'];
        $this->password = $config['pass'];
    }

    public function sendReceipt(array $receiptData)
    {
        $methodResponse = new ApiResponse();

        if (empty($receiptData['group_code'])) {
            return $methodResponse->setError('Чек не содержит требуемого параметра "group_code" (идентификатор группы ККТ)');
        }

        if (empty($receiptData['document_type'])) {
            return $methodResponse->setError('Чек не содержит требуемого параметра "document_type" (тип операции)');
        }

        if (!$token = $this->getToken()) {
            if ($this->lastError) {
                return $methodResponse->setError($this->lastError);
            } else {
                return false;
            }
        }

        $apiResponse = $this->makeRequest($receiptData['group_code'] . '/' . $receiptData['document_type'] . '?tokenid=' . $token, $receiptData);

        if (!$apiResponse) {
            return false;
        }

        // Сохраняем ответ от АТОЛ
        $methodResponse->setResponseData($apiResponse);

        if (empty($apiResponse['status']) || !in_array($apiResponse['status'], ['fail', 'wait'])) {
            return $methodResponse->setError('Получен неожиданный статус результата запроса к API: '
                . (!empty($apiResponse['status']) ? $apiResponse['status'] : '-'));
        }

        if (empty($apiResponse['uuid'])) {
            return $methodResponse->setError('В ответе отсутствует уникальный идентификатор чека в система АТОЛ (параметр uuid)');
        }

        if ('wait' === $apiResponse['status']) {
            $methodResponse->setReceiptStatus(\OnlineCashDesk\Receipt::STATUS_PENDING);
        } else {
            $methodResponse->setError(!empty($response['error']['text']) ? $response['error']['text'] : 'Неизвестная ошибка при попытке регистрации чека');
        }

        return $methodResponse;
    }

    public function checkReceiptStatus(array $receiptData, array $lastResponseData)
    {
        $methodResponse = new ApiResponse();

        if (empty($receiptData['group_code'])) {
            return $methodResponse->setError('Чек не содержит требуемого параметра "group_code" (идентификатор группы ККТ)');
        }

        if (empty($lastResponseData['uuid'])) {
            return $methodResponse->setError('В данных чека не найден идентификатор чека в АТОЛ (параметр uuid)');
        }

        if (!$token = $this->getToken()) {
            if ($this->lastError) {
                return $methodResponse->setError($this->lastError);
            } else {
                return false;
            }
        }

        $apiResponse = $this->makeRequest($receiptData['group_code'] . '/report/' . $lastResponseData['uuid'] . '?tokenid=' . $token, null, 'GET');

        if (!$apiResponse) {
            return false;
        }

        // Сохраняем ответ от АТОЛ
        $methodResponse->setResponseData($apiResponse);

        if (empty($apiResponse['status']) || !in_array($apiResponse['status'], ['fail', 'wait', 'done'])) {
            return $methodResponse->setError('Получен неожиданный статус результата запроса к API: '
                . (!empty($apiResponse['status']) ? $apiResponse['status'] : '-'));
        }

        if ('done' === $apiResponse['status']) {
            $methodResponse->setReceiptStatus(\OnlineCashDesk\Receipt::STATUS_REGISTERED);
        } elseif ('fail' === $apiResponse['status']) {
            $methodResponse->setError(!empty($response['error']['text']) ? $response['error']['text'] : 'Неизвестная ошибка при попытке регистрации чека');
        }

        return $methodResponse;
    }

    private function makeRequest($path, $data, $method = 'POST')
    {
        $ch = curl_init();

        try {
            curl_setopt($ch, CURLOPT_URL, self::API_ENDPOINT . $path);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            if (defined('APPLICATION_ENV') && ('development' !== APPLICATION_ENV)) {
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 1000);
                curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10000);
            }

            if ('POST' === $method) {
                $params = json_encode($data);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($params),
                ]);
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

    private function getToken()
    {
        $this->lastError = null;

        if (!$this->token) {
            $response = $this->makeRequest('getToken', [
                'login' => $this->login,
                'pass' => $this->password,
            ]);

            if (isset($response['code'])) {
                if (intval($response['code']) <= 1) {
                    // Отсутствие токена в ответе при наличии верного кода считаем сбоем и игнорируем
                    $this->token = !empty($response['token']) ? $response['token'] : null;
                } elseif (intval($response['code']) >= 2) {
                    $this->lastError = (!empty($response['text']) ? $response['text'] : 'Неизвестная ошибка при попытке получения токена') . ' (TOKEN)';
                }
            }
        }

        return $this->token;
    }
}