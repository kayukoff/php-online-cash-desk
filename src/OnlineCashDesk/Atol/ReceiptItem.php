<?php

/**
 * Абстрактный класс позиции чека АТОЛ, в котором находятся общие для всех версий API АТОЛ константы и методы.
 */

namespace OnlineCashDesk\Atol;


abstract class ReceiptItem extends \OnlineCashDesk\AbstractReceiptItem
{
    protected function init()
    {
        $this->vatTypeIdToInternal = [
            self::VAT_TYPE_NONE => 'none',
            self::VAT_TYPE_0 => 'vat0',
            self::VAT_TYPE_10 => 'vat10',
            self::VAT_TYPE_18 => 'vat18',
            self::VAT_TYPE_110 => 'vat110',
            self::VAT_TYPE_118 => 'vat118',
        ];

        $this->vatTypeInternalToId = [
            'none' => self::VAT_TYPE_NONE,
            'vat0' => self::VAT_TYPE_0,
            'vat10' => self::VAT_TYPE_10,
            'vat18' => self::VAT_TYPE_18,
            'vat110' => self::VAT_TYPE_110,
            'vat118' => self::VAT_TYPE_118,
        ];
    }
}
