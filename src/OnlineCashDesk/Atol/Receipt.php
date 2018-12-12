<?php

/**
 * Абстрактный класс чека АТОЛ, в котором находятся общие для всех версий API АТОЛ константы и методы.
 */

namespace OnlineCashDesk\Atol;


abstract class Receipt extends \OnlineCashDesk\AbstractReceipt
{
    protected static $typeIdToInternal = [
        self::TYPE_SELL => 'sell',
        self::TYPE_SELL_REFUND => 'sell_refund',
        self::TYPE_SELL_CORRECTION => 'sell_correction',
        self::TYPE_BUY => 'buy',
        self::TYPE_BUY_REFUND => 'buy_refund',
        self::TYPE_BUY_CORRECTION => 'buy_correction',
    ];

    protected static $typeInternalToId = [
        'sell' => self::TYPE_SELL,
        'sell_refund' => self::TYPE_SELL_REFUND,
        'sell_correction' => self::TYPE_SELL_CORRECTION,
        'buy' => self::TYPE_BUY,
        'buy_refund' => self::TYPE_BUY_REFUND,
        'buy_correction' => self::TYPE_BUY_CORRECTION,
    ];

    protected static $taxSystemIdToInternal = [
        self::TAX_SYSTEM_OSN => 'osn',
        self::TAX_SYSTEM_USN_INCOME => 'usn_income',
        self::TAX_SYSTEM_USN_INCOME_OUTCOME => 'usn_income_outcome',
        self::TAX_SYSTEM_ENVD => 'envd',
        self::TAX_SYSTEM_PATENT => 'patent',
        self::TAX_SYSTEM_ESN => 'esn',
    ];

    protected static $taxSystemInternalToId = [
        'osn' => self::TAX_SYSTEM_OSN,
        'usn_income' => self::TAX_SYSTEM_USN_INCOME,
        'usn_income_outcome' => self::TAX_SYSTEM_USN_INCOME_OUTCOME,
        'envd' => self::TAX_SYSTEM_ENVD,
        'patent' => self::TAX_SYSTEM_PATENT,
        'esn' => self::TAX_SYSTEM_ESN,
    ];

    protected static $paymentTypeIdToInternal = [
        self::PAYMENT_TYPE_ELECTRONIC => 1,
    ];

    protected static $paymentTypeInternalToId = [
        1 => self::PAYMENT_TYPE_ELECTRONIC,
    ];


    public function getCreationDateTime()
    {
        list($date, $time) = explode(' ', $this->data['timestamp']);
        $date = explode('.', $date);

        return new \DateTime($date[2] . '-' . $date[1] . '-' . $date[0] . ' ' . $time);
    }

    public function setCreationDateTime(\DateTime $dateTime)
    {
        $this->data['timestamp'] = $dateTime->format('d.m.Y H:i:s');

        return $this;
    }

    public function getOrderId()
    {
        return isset($this->data['external_id']) ? $this->data['external_id'] : null;
    }

    public function setOrderId($orderId)
    {
        $this->data['external_id'] = $orderId;

        return $this;
    }

    public function getTotal()
    {
        $this->recalculateTotals();

        return $this->data['receipt']['total'];
    }

    public function toArray()
    {
        $this->recalculateTotals();

        foreach ($this->items as $item) {
            $this->data['receipt']['items'][] = $item->toArray();
        }

        return $this->data;
    }

    public function fromArray(array $data)
    {
        $this->data = $data;

        if (!empty($this->data['receipt']['items'])) {
            $class = str_replace('Receipt', 'ReceiptItem', get_class($this));
            foreach ($this->data['receipt']['items'] as $itemArray) {
                $this->items[] = new $class($itemArray);
            }
        }

        return $this;
    }

    protected function recalculateTotals()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }

        $this->data['receipt']['payments'] = [[
            'sum' => $total,
            'type' => $this->paymentType,
        ]];
        $this->data['receipt']['total'] = $total;
    }

    public function getRawType()
    {
        return isset($this->data['document_type']) ? $this->data['document_type'] : null;
    }

    public function setRawType($type)
    {
        $this->data['document_type'] = $type;
    }

}
