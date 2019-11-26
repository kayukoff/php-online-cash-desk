<?php

namespace OnlineCashDesk;


interface ReceiptItem
{
    // Тип объекта позиции
    const TYPE_PIECE_GOODS    = 0x01;  // штучный товар
    const TYPE_WEIGHTED_GOODS = 0x02;  // весовой товар
    const TYPE_EXCISE_GOODS   = 0x03;  // подакцизный товар
    const TYPE_SERVICE        = 0x04;  // услуга
    const TYPE_JOB            = 0x05;  // работа
    const TYPE_PAYMENT        = 0x06;  // платеж (аванс, задаток, предоплата, кредит, взнос в счет оплаты и т.п.)
    const TYPE_COMPOSITE      = 0x07;  // составной товар
    const TYPE_ANOTHER        = 0x08;  // иной товар не попадающий ни под одну из имеющихся категорий

    // Ставка НДС
    const VAT_TYPE_NONE = 0x01;  // без НДС
    const VAT_TYPE_0    = 0x02;  // ставка 0%
    const VAT_TYPE_10   = 0x03;  // ставка 10%
    const VAT_TYPE_18   = 0x04;  // ставка 18%
    const VAT_TYPE_110  = 0x05;  // ставка 10/110
    const VAT_TYPE_118  = 0x06;  // ставка 18/118
    const VAT_TYPE_20   = 0x07;  // ставка 20%
    const VAT_TYPE_120  = 0x08;  // ставка 20/120

    // Способ расчёта за позицию
    const PAYMENT_METHOD_FULL_PREPAYMENT = 0x01;  // предоплата 100% (предмет расчета передается позже)
    const PAYMENT_METHOD_PREPAYMENT      = 0x02;  // предоплата
    const PAYMENT_METHOD_ADVANCE         = 0x03;  // аванс
    const PAYMENT_METHOD_FULL_PAYMENT    = 0x04;  // полный расчет (предмет расчета передается сразу после оплаты)
    const PAYMENT_METHOD_PARTIAL_PAYMENT = 0x05;  // частичный расчет и кредит
    const PAYMENT_METHOD_CREDIT          = 0x06;  // передача в кредит
    const PAYMENT_METHOD_CREDIT_PAYMENT  = 0x07;  // оплата кредита


    /**
     * Возвращает название позиции
     *
     * @return string
     */
    public function getName();

    /**
     * Устанавливает название позиции
     *
     * @param string $phone
     *
     * @return ReceiptItem
     */
    public function setName($name);

    /**
     * Возвращает стоимость одного объекта позиции
     *
     * @return float
     */
    public function getPrice();

    /**
     * Устанавливает стоимость одного объекта позиции
     *
     * @param float $price
     *
     * @return ReceiptItem
     */
    public function setPrice($price);

    /**
     * Возвращает количество объектов позиции
     *
     * @return int
     */
    public function getQuantity();

    /**
     * Устанавливает количество объектов позиции
     *
     * @param int $quantity
     *
     * @return ReceiptItem
     */
    public function setQuantity($quantity);

    /**
     * Возвращает общую стоимость позиции
     *
     * @return float
     */
    public function getTotal();

    /**
     * Устанавливает общую стоимость позиции
     *
     * @param float $total
     *
     * @return ReceiptItem
     */
    public function setTotal($total);

    /**
     * Возвращает тип НДС позиции
     *
     * @return int
     */
    public function getVatType();

    /**
     * Устанавливает тип НДС позиции
     *
     * @param int $vatType
     *
     * @return ReceiptItem
     */
    public function setVatType($vatType);

    /**
     * Возвращает сумму НДС позиции
     *
     * @return float
     */
    public function getVatSum();

    /**
     * Устанавливает сумму НДС позиции
     *
     * @param float $vatSum
     *
     * @return ReceiptItem
     */
    public function setVatSum($vatSum);

    /**
     * Возвращает тип объекта позиции
     *
     * @return int
     */
    public function getType();

    /**
     * Устанавливает тип объекта позиции
     *
     * @param int $type
     *
     * @return ReceiptItem
     */
    public function setType($type);

    /**
     * Возвращает единицы измерения объекта позиции
     *
     * @return string
     */
    public function getMeasurementUnit();

    /**
     * Устанавливает единицы измерения объекта позиции
     *
     * @param string $measurementUnit
     *
     * @return ReceiptItem
     */
    public function setMeasurementUnit($measurementUnit);

    /**
     * Возвращает способ расчёта за позицию
     *
     * @return int
     */
    public function getPaymentMethod();

    /**
     * Устанавливает способ расчёта за позицию
     *
     * @param int $paymentMethod
     *
     * @return ReceiptItem
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * Возвращает свойства позиции чека в виде массива
     *
     * @return array
     */
    public function toArray();

    /**
     * Создает объект позиции чека из его представления в виде массива
     *
     * @param array $data
     *
     * @return ReceiptItem
     */
    public function fromArray(array $data);
}
