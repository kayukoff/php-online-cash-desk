<?php

/**
 * Абстрактная позиция чека. Реализует общие для всех типов позиций чека методы интерфейса "позиция чека". Конкретные же
 * параметры (типа названия свойства, в котором хранится то или иное значение) задаются уже в наследниках, реализующих
 * API той или иной онлайн кассы.
 */

namespace OnlineCashDesk;


abstract class AbstractReceiptItem implements ReceiptItem
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array Данные для конвертации названия свойства в используемое конкретной онлайн кассой
     */
    protected $dataFieldNameConversion = [
        'name' => 'name',
        'price' => 'price',
        'quantity' => 'quantity',
        'total' => 'total',
        'vat_type' => 'vat_type',
        'vat_sum' => 'vat_sum',
        'type' => 'type',
        'measurement_unit' => 'measurement_unit',
        'payment_method' => 'payment_method',
    ];

    /**
     * @var array Данные для конвертации ID типа объекта позиции в значение используемое онлайн кассой (задается в наследниках)
     */
    protected $typeIdToInternal = [];

    /**
     * @var array Данные для конвертации значения типа объекта позиции, используемого онлайн кассой, в ID типа объекта позиции (задается в наследниках)
     */
    protected $typeInternalToId = [];

    /**
     * @var array Данные для конвертации ID ставки НДС позиции в значение используемое онлайн кассой (задается в наследниках)
     */
    protected $vatTypeIdToInternal = [];

    /**
     * @var array Данные для конвертации значения ставки НДС позиции, используемого онлайн кассой, в ID НДС позиции (задается в наследниках)
     */
    protected $vatTypeInternalToId = [];

    /**
     * @var array Данные для конвертации ID способа расчёта в значение используемое онлайн кассой (задается в наследниках)
     */
    protected $paymentMethodIdToInternal = [];

    /**
     * @var array Данные для конвертации значения способа расчёта, используемого онлайн кассой, в ID способа расчёта (задается в наследниках)
     */
    protected $paymentMethodInternalToId = [];


    public function __construct($data = null)
    {
        $this->init();

        if (is_array($data)) {
            $this->fromArray($data);
        } else {
            // Для упрощения работы с кодом задаем по умолчанию следующее: в позиции один объект типа штучный товар за который поступает полная предоплата
            $this->setQuantity(1)
                ->setType(ReceiptItem::TYPE_PIECE_GOODS)
                ->setPaymentMethod(ReceiptItem::PAYMENT_METHOD_FULL_PREPAYMENT)
            ;
        }
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function setName($name)
    {
        return $this->setData('name', $name);
    }

    public function getPrice()
    {
        return $this->getData('price');
    }

    public function setPrice($price)
    {
        return $this->setData('price', floatval($price));
    }

    public function getQuantity()
    {
        return $this->getData('quantity');
    }

    public function setQuantity($quantity)
    {
        return $this->setData('quantity', floatval($quantity));
    }

    public function getTotal()
    {
        $total = $this->getData('total');

        if (empty($total)) {
            $total = $this->getData('price', 0) * $this->getData('quantity', 0);
            $this->setData('total', $total);
        }

        return $total;
    }

    public function setTotal($total)
    {
        return $this->setData('total', $total);
    }

    public function getVatType()
    {
        $result = $this->getData('vat_type');

        if (!array_key_exists($result, $this->vatTypeInternalToId)) {
            throw new \LogicException('$vatTypeInternalToId does not contain all required VAT types');
        }

        return $this->vatTypeInternalToId[$result];
    }

    public function setVatType($vatType)
    {
        if (!array_key_exists($vatType, $this->vatTypeIdToInternal)) {
            throw new \LogicException('$vatTypeIdToInternal does not contain all required VAT types');
        }

        $vatType = $this->vatTypeIdToInternal[$vatType];

        return $this->setData('vat_type', $vatType);
    }

    public function getVatSum()
    {
        return $this->getData('vat_sum');
    }

    public function setVatSum($vatSum)
    {
        return $this->setData('vat_sum', $vatSum);
    }

    public function getType()
    {
        $result = $this->getData('type');

        if (!array_key_exists($result, $this->typeInternalToId)) {
            throw new \LogicException('Item Type either is not supported or not set in $typeInternalToId config property — "' . $result . '" key');
        }

        return $this->typeInternalToId[$result];
    }

    public function setType($type)
    {
        if (!array_key_exists($type, $this->typeIdToInternal)) {
            throw new \LogicException('Item Type either is not supported or not set in $typeIdToInternal config property — "' . $type . '" key');
        }

        $type = $this->typeIdToInternal[$type];

        return $this->setData('type', $type);
    }

    public function getMeasurementUnit()
    {
        return $this->getData('measurement_unit');
    }

    public function setMeasurementUnit($measurementUnit)
    {
        return $this->setData('measurement_unit', $measurementUnit);
    }

    public function getPaymentMethod()
    {
        $result = $this->getData('payment_method');

        if (!array_key_exists($result, $this->paymentMethodInternalToId)) {
            throw new \LogicException('Payment Method either is not supported or not set in $paymentMethodInternalToId config property — "' . $result . '" key');
        }

        return $this->paymentMethodInternalToId[$result];
    }

    public function setPaymentMethod($paymentMethod)
    {
        if (!array_key_exists($paymentMethod, $this->paymentMethodIdToInternal)) {
            throw new \LogicException('Payment Method either is not supported or not set in $paymentMethodIdToInternal config property — "' . $paymentMethod . '" key');
        }

        $paymentMethod = $this->paymentMethodIdToInternal[$paymentMethod];

        return $this->setData('payment_method', $paymentMethod);
    }

    public function toArray()
    {
        // Вызываем данный метод, чтобы расчиталась сумма по позиции
        $this->getTotal();

        return $this->data;
    }

    public function fromArray(array $data)
    {
        if (!empty($data)) {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Вызывается в конструкторе для инициализации объекта
     */
    abstract protected function init();

    /**
     * Возвращает свойство объекта
     *
     * @param $name
     * @param mixed|null $default
     *
     * @return mixed|null
     *
     * @throws \InvalidArgumentException
     */
    protected function getData($name, $default = null)
    {
        if (!array_key_exists($name, $this->dataFieldNameConversion)) {
            throw new \LogicException('ReceiptItem property ' . $name . ' is not declared in $dataFieldNameConversion');
        }

        $name = $this->dataFieldNameConversion[$name];

        if (false !== strpos($name, ':')) {
            list($name1, $name2) = explode(':', $name, 2);
            $result = isset($this->data[$name1][$name2]) ? $this->data[$name1][$name2] : $default;
        } else {
            $result = isset($this->data[$name]) ? $this->data[$name] : $default;
        }

        return $result;
    }

    /**
     * Устанавливает свойство объекта
     *
     * @param $name
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    protected function setData($name, $value)
    {
        $name = $this->dataFieldNameConversion[$name];
        if (false !== strpos($name, ':')) {
            list($name1, $name2) = explode(':', $name, 2);
            $this->data[$name1][$name2] = $value;
        } else {
            $this->data[$name] = $value;
        }

        return $this;
    }

}
