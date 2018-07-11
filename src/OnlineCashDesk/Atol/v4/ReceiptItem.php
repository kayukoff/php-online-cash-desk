<?php

/**
 * Под АТОЛ API версии 4.0
 */

namespace OnlineCashDesk\Atol\v4;


class ReceiptItem extends \OnlineCashDesk\Atol\ReceiptItem
{
    /*public function getVatType()
    {
        return isset($this->data['vat']['type']) ? $this->data['vat']['type'] : null;
    }

    public function setVatType($vatType)
    {
        $this->data['vat']['type'] = $vatType;

        return $this;
    }

    public function getVatSum()
    {
        return isset($this->data['vat']['sum']) ? $this->data['vat']['sum'] : null;
    }

    public function setVatSum($vatSum)
    {
        $this->data['vat']['sum'] = $vatSum;

        return $this;
    }*/

    protected function init()
    {
        parent::init();

        $this->dataFieldNameConversion['total'] = 'sum';
        $this->dataFieldNameConversion['type'] = 'payment_object';
        $this->dataFieldNameConversion['vat_type'] = 'vat:type';
        $this->dataFieldNameConversion['vat_sum'] = 'vat:sum';

        $this->typeIdToInternal = [
            self::TYPE_PIECE_GOODS => 'commodity',
            self::TYPE_EXCISE_GOODS => 'excise',
        ];

        $this->typeInternalToId = [
            'commodity' => self::TYPE_PIECE_GOODS,
            'excise' => self::TYPE_EXCISE_GOODS,
        ];

        $this->paymentMethodIdToInternal = [
            self::PAYMENT_METHOD_FULL_PREPAYMENT => 'full_prepayment',
            self::PAYMENT_METHOD_PREPAYMENT => 'prepayment',
            self::PAYMENT_METHOD_ADVANCE => 'advance',
            self::PAYMENT_METHOD_FULL_PAYMENT => 'full_payment',
            self::PAYMENT_METHOD_PARTIAL_PAYMENT => 'partial_payment',
            self::PAYMENT_METHOD_CREDIT => 'credit',
            self::PAYMENT_METHOD_CREDIT_PAYMENT => 'credit_payment',
        ];

        $this->paymentMethodInternalToId = [
            'full_prepayment' => self::PAYMENT_METHOD_FULL_PREPAYMENT,
            'prepayment' => self::PAYMENT_METHOD_PREPAYMENT,
            'advance' => self::PAYMENT_METHOD_ADVANCE,
            'full_payment' => self::PAYMENT_METHOD_FULL_PAYMENT,
            'partial_payment' => self::PAYMENT_METHOD_PARTIAL_PAYMENT,
            'credit' => self::PAYMENT_METHOD_CREDIT,
            'credit_payment' => self::PAYMENT_METHOD_CREDIT_PAYMENT,
        ];
    }

}
