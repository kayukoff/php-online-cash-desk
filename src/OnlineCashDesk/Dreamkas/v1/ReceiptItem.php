<?php

namespace OnlineCashDesk\Dreamkas\v1;


class ReceiptItem extends \OnlineCashDesk\AbstractReceiptItem
{
    public function getPrice()
    {
        return parent::getPrice() / 100;
    }

    public function setPrice($price)
    {
        // Храним цену в копейках
        return parent::setPrice(round($price, 2) * 100);
    }

    public function getTotal()
    {
        return parent::getTotal() / 100;
    }

    public function setTotal($total)
    {
        return parent::setTotal(round($total, 2) * 100);
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
        $this->dataFieldNameConversion['total'] = 'priceSum';
        $this->dataFieldNameConversion['vat_type'] = 'tax';
        $this->dataFieldNameConversion['vat_sum'] = 'taxSum';

        $this->typeIdToInternal = [
            self::TYPE_PIECE_GOODS => 'COUNTABLE',
            self::TYPE_WEIGHTED_GOODS => 'SCALABLE',
            self::TYPE_EXCISE_GOODS => 'ALCOHOL',
        ];

        $this->typeInternalToId = [
            'COUNTABLE' => self::TYPE_PIECE_GOODS,
            'SCALABLE' => self::TYPE_WEIGHTED_GOODS,
            'ALCOHOL' => self::TYPE_EXCISE_GOODS,
        ];

        $this->vatTypeIdToInternal = [
            self::VAT_TYPE_NONE => 'NDS_NO_TAX',
            self::VAT_TYPE_0 => 'NDS_0',
            self::VAT_TYPE_10 => 'NDS_10',
            self::VAT_TYPE_18 => 'NDS_18',
            self::VAT_TYPE_110 => 'NDS_10_CALCULATED',
            self::VAT_TYPE_118 => 'NDS_18_CALCULATED',
        ];

        $this->vatTypeInternalToId = [
            'NDS_NO_TAX' => self::VAT_TYPE_NONE,
            'NDS_0' => self::VAT_TYPE_0,
            'NDS_10' => self::VAT_TYPE_10,
            'NDS_18' => self::VAT_TYPE_18,
            'NDS_10_CALCULATED' => self::VAT_TYPE_110,
            'NDS_18_CALCULATED' => self::VAT_TYPE_118,
        ];
    }

}
