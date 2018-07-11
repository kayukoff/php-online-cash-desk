<?php
/**
 * Абстрактный чек кассы. Реализует общие для всех типов чеков методы интерфейса "чек".
 */

namespace OnlineCashDesk;


abstract class AbstractReceipt implements Receipt
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var ReceiptItem[]
     */
    protected $items = [];

    /**
     * @var int Вид оплаты
     */
    protected $paymentType;


    protected static $typeIdToInternal = [];

    protected static $typeInternalToId = [];

    protected static $taxSystemIdToInternal = [];

    protected static $taxSystemInternalToId = [];

    protected static $paymentTypeIdToInternal = [];

    protected static $paymentTypeInternalToId = [];


    /**
     * Receipt constructor.
     *
     * @param CashDesk|array $cashDesk
     */
    public function __construct($cashDesk)
    {
        if (is_array($cashDesk)) {
            $this->fromArray($cashDesk);
            return;
        } elseif (!($cashDesk instanceof CashDesk)) {
            throw new \InvalidArgumentException('Invalid receipt constructor param');
        }

        $this->init();
    }

    public function getType()
    {
        $type = $this->getRawType();

        if (empty($type)) {
            return null;
        }

        if (!array_key_exists($type, static::$typeInternalToId)) {
            throw new \LogicException('Type either is not supported or not set in $typeInternalToId');
        }

        return static::$typeInternalToId[$type];
    }

    public function setType($type)
    {
        if (!array_key_exists($type, static::$typeIdToInternal)) {
            throw new \LogicException('Type either is not supported or not set in $typeIdToInternal');
        }

        $this->setRawType(static::$typeIdToInternal[$type]);

        return $this;
    }

    public function getTaxSystem()
    {
        $taxSystem = $this->getRawTaxSystem();

        if (empty($taxSystem)) {
            return null;
        }

        if (!array_key_exists($taxSystem, static::$taxSystemInternalToId)) {
            throw new \LogicException('Tax System either is not supported or not set in $taxSystemInternalToId');
        }

        return static::$taxSystemInternalToId[$taxSystem];
    }

    public function setTaxSystem($taxSystem)
    {
        if (!array_key_exists($taxSystem, static::$taxSystemIdToInternal)) {
            throw new \LogicException('Tax System either is not supported or not set in $taxSystemIdToInternal');
        }

        $this->setRawTaxSystem(static::$taxSystemIdToInternal[$taxSystem]);

        return $this;
    }

    public function getPaymentType()
    {
        $paymentType = $this->getRawPaymentType();

        if (!array_key_exists($paymentType, static::$paymentTypeInternalToId)) {
            throw new \LogicException('Payment Type either is not supported or not set in $paymentTypeInternalToId');
        }

        return static::$paymentTypeInternalToId[$paymentType];
    }

    public function setPaymentType($paymentType)
    {
        if (!array_key_exists($paymentType, static::$paymentTypeIdToInternal)) {
            throw new \LogicException('Payment Type either is not supported or not set in $paymentTypeIdToInternal');
        }

        $this->setRawPaymentType(static::$paymentTypeIdToInternal[$paymentType]);

        return $this;
    }

    public function addItem(ReceiptItem $item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    protected function init()
    {
        // Для упрощения работы с кодом задаем по умолчанию следующеие значения: дату создания чека (как текущее время),
        // тип чека продажа, вид оплаты электронный (безнал)
        $this->setCreationDateTime(new \DateTime('now'))
            ->setType(Receipt::TYPE_SELL)
            ->setPaymentType(Receipt::PAYMENT_TYPE_ELECTRONIC)
        ;
    }

    /**
     * Возвращает значение вида оплаты в том виде, в котором оно используется конкретной онлайн кассой
     *
     * @return mixed
     */
    protected function getRawPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Устанавливает значение вида оплаты в том виде, в котором оно используется конкретной онлайн кассой
     *
     * @param mixed $paymentType
     *
     * @return void
     */
    protected function setRawPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    }

    /**
     * Возвращает значение типа чека в том виде, в котором оно используется конкретной онлайн кассой
     *
     * @return mixed
     */
    abstract protected function getRawType();

    /**
     * Устанавливает значение типа чека в том виде, в котором оно используется конкретной онлайн кассой
     *
     * @param mixed $taxSystem
     *
     * @return void
     */
    abstract protected function setRawType($type);

    /**
     * Возвращает значение системы налогообложения в том виде, в котором оно используется конкретной онлайн кассой
     *
     * @return mixed
     */
    abstract protected function getRawTaxSystem();

    /**
     * Устанавливает значение системы налогообложения в том виде, в котором оно используется конкретной онлайн кассой
     *
     * @param mixed $taxSystem
     *
     * @return void
     */
    abstract protected function setRawTaxSystem($taxSystem);

}
