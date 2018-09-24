<?php

namespace OnlineCashDesk\Dreamkas\v1;


class Receipt extends \OnlineCashDesk\AbstractReceipt
{
    protected static $typeIdToInternal = [
        self::TYPE_SELL => 'SALE',
        self::TYPE_SELL_REFUND => 'REFUND',
        self::TYPE_BUY => 'OUTFLOW',
        self::TYPE_BUY_REFUND => 'OUTFLOW_REFUND',
    ];

    protected static $typeInternalToId = [
        'SALE' => self::TYPE_SELL,
        'REFUND' => self::TYPE_SELL_REFUND,
        'OUTFLOW' => self::TYPE_BUY,
        'OUTFLOW_REFUND' => self::TYPE_BUY_REFUND,
    ];

    protected static $taxSystemIdToInternal = [
        self::TAX_SYSTEM_OSN => 'DEFAULT',
        self::TAX_SYSTEM_USN_INCOME => 'SIMPLE',
        self::TAX_SYSTEM_USN_INCOME_OUTCOME => 'SIMPLE_WO',
        self::TAX_SYSTEM_ENVD => 'ENVD',
        self::TAX_SYSTEM_PATENT => 'PATENT',
        self::TAX_SYSTEM_ESN => 'AGRICULT',
    ];

    protected static $taxSystemInternalToId = [
        'DEFAULT' => self::TAX_SYSTEM_OSN,
        'SIMPLE' => self::TAX_SYSTEM_USN_INCOME,
        'SIMPLE_WO' => self::TAX_SYSTEM_USN_INCOME_OUTCOME,
        'ENVD' => self::TAX_SYSTEM_ENVD,
        'PATENT' => self::TAX_SYSTEM_PATENT,
        'AGRICULT' => self::TAX_SYSTEM_ESN,
    ];

    protected static $paymentTypeIdToInternal = [
        self::PAYMENT_TYPE_ELECTRONIC => 'CASHLESS',
    ];

    protected static $paymentTypeInternalToId = [
        'CASHLESS' => self::PAYMENT_TYPE_ELECTRONIC,
    ];


    /**
     * Receipt constructor.
     *
     * @param \OnlineCashDesk\CashDesk|array $cashDesk
     */
    public function __construct($cashDesk)
    {
        parent::__construct($cashDesk);

        if (is_array($cashDesk)) {
            return;
        }

        $config = $cashDesk->getConfig();
        if (empty($config['deviceId'])
            || empty($config['timeout'])
            || empty($config['sno'])
        ) {
            throw new \LogicException('Online cash desk config does not contain required param(s): deviceId, timeout, sno');
        }

        $this->setTaxSystem($config['sno']);

        $this->data['deviceId'] = $config['deviceId'];
        $this->data['timeout'] = $config['timeout'];
    }

    public function getCreationDateTime()
    {
        return new \DateTime('now');
    }

    public function setCreationDateTime(\DateTime $dateTime)
    {
        return $this;
    }

    public function getOrderId()
    {
        return null;
    }

    public function setOrderId($orderId)
    {
        return $this;
    }

    public function getCompanyInn()
    {
        return null;
    }

    public function setCompanyInn($inn)
    {
        return $this;
    }

    public function getCompanyEmail()
    {
        return null;
    }

    public function setCompanyEmail($email)
    {
        return $this;
    }

    public function getAddress()
    {
        return null;
    }

    public function setAddress($address)
    {
        return $this;
    }

    public function getCustomerEmail()
    {
        return isset($this->data['attributes']['email']) ? $this->data['attributes']['email'] : null;
    }

    public function setCustomerEmail($email)
    {
        $this->data['attributes']['email'] = $email;

        return $this;
    }

    public function getCustomerPhone()
    {
        return isset($this->data['attributes']['phone']) ? $this->data['attributes']['phone'] : null;
    }

    public function setCustomerPhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if ('8' == $phone[0]) {
            $phone[0] = '7';
        }

        $this->data['attributes']['phone'] = '+' . $phone;

        return $this;
    }

    public function getTotal()
    {
        $this->recalculateTotals();

        return $this->data['total']['priceSum'] / 100;
    }

    public function toArray()
    {
        $this->recalculateTotals();

        foreach ($this->items as $item) {
            $this->data['positions'][] = $item->toArray();
        }

        return $this->data;
    }

    public function fromArray(array $data)
    {
        $this->data = $data;

        if (!empty($this->data['positions'])) {
            foreach ($this->data['positions'] as $itemArray) {
                $this->items[] = new ReceiptItem($itemArray);
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

        // Переводим сумму в копейки
        $total = round($total, 2) * 100;

        $this->data['payments'] = [[
            'sum' => $total,
            'type' => $this->paymentType,
        ]];
        $this->data['total']['priceSum'] = $total;
    }

    public function getRawType()
    {
        return isset($this->data['type']) ? $this->data['type'] : null;
    }

    public function setRawType($type)
    {
        $this->data['type'] = $type;
    }

    public function getRawTaxSystem()
    {
        return isset($this->data['taxMode']) ? $this->data['taxMode'] : null;
    }

    public function setRawTaxSystem($taxSystem)
    {
        $this->data['taxMode'] = $taxSystem;
    }

}
