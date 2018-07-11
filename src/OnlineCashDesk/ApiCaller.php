<?php

namespace OnlineCashDesk;


interface ApiCaller
{
    /**
     * Отправляет данные чека в облако онлайн кассы
     *
     * @param array $receiptData
     *
     * @return ApiResponse|false false в случае ошибки запроса в облако
     */
    public function sendReceipt(array $receiptData);

    /**
     * Проверяет статус чека, отправленного на обработку в онлайн кассу
     *
     * @param array $receiptData
     * @param array $lastResponseData
     *
     * @return ApiResponse|false false в случае ошибки запроса в облако
     */
    public function checkReceiptStatus(array $receiptData, array $lastResponseData);
}