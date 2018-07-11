<?php

/**
 * Под АТОЛ API версии 3.0
 */

namespace OnlineCashDesk\Atol\v3;


class ReceiptItem extends \OnlineCashDesk\Atol\ReceiptItem
{

    public function getType()
    {
        return null;
    }

    public function setType($type)
    {
        return $this;
    }

    public function getMeasurementUnit()
    {
        return null;
    }

    public function setMeasurementUnit($measurementUnit)
    {
        return $this;
    }

    public function getPaymentMethod()
    {
        return null;
    }

    public function setPaymentMethod($paymentMethod)
    {
        return $this;
    }

    protected function init()
    {
        parent::init();

        $this->dataFieldNameConversion['total'] = 'sum';
        $this->dataFieldNameConversion['vat_type'] = 'tax';
        $this->dataFieldNameConversion['vat_sum'] = 'tax_sum';
    }

}
