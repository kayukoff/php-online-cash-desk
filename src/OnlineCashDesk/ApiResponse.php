<?php

namespace OnlineCashDesk;


class ApiResponse
{
    private $error = null;
    private $responseData = null;
    private $receiptStatus = null;

    public function getError()
    {
        return $this->error;
    }

    public function hasError()
    {
        return !empty($this->error);
    }

    public function setError($error)
    {
        $this->error = $error;
        $this->receiptStatus = Receipt::STATUS_ERROR;

        return $this;
    }

    public function getResponseData()
    {
        return $this->responseData;
    }

    public function setResponseData($data)
    {
        $this->responseData = $data;

        return $this;
    }

    public function getReceiptStatus()
    {
        return $this->receiptStatus;
    }

    public function setReceiptStatus($status)
    {
        $this->receiptStatus = $status;

        return $this;
    }
}