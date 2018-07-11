<?php

namespace OnlineCashDesk;


interface Receipt
{
    // Статус чека
    const STATUS_NEW        = 0x00;   // чек только создан и ожидает отправки в онлайн кассу
    const STATUS_PENDING    = 0x01;   // отправлен в онлайн кассу, ожидается ответ о регистрации
    const STATUS_REGISTERED = 0x02;   // зарегистрирован в онлайн кассе, статус окончательный
    const STATUS_ERROR      = 0x03;   // та или иная ошибка регистрации чека в онлайн кассе

    // Тип чека
    const TYPE_SELL            = 0x01;  // приход
    const TYPE_SELL_REFUND     = 0x02;  // возврат прихода
    const TYPE_SELL_CORRECTION = 0x03;  // коррекция прихода
    const TYPE_BUY             = 0x04;  // расход
    const TYPE_BUY_REFUND      = 0x05;  // возврат расхода
    const TYPE_BUY_CORRECTION  = 0x06;  // коррекция расхода

    // Система налогообложения
    const TAX_SYSTEM_OSN                = 0x01;  // общая СН
    const TAX_SYSTEM_USN_INCOME         = 0x02;  // упрощнная СН (доход)
    const TAX_SYSTEM_USN_INCOME_OUTCOME = 0x03;  // упрощенная СН (доходы минус расходы)
    const TAX_SYSTEM_ENVD               = 0x04;  // единый налог на вмененный доход
    const TAX_SYSTEM_PATENT             = 0x05;  // патентная СН
    const TAX_SYSTEM_ESN                = 0x06;  // единый сельскохозяйственный налог

    // Вид оплаты
    const PAYMENT_TYPE_ELECTRONIC = 0x01;  // электронный (безнал)


    /**
     * Возвращает дату и время создания чека
     *
     * @return \DateTime
     */
    public function getCreationDateTime();

    /**
     * Устанавливает дату и время создания чека
     *
     * @param \DateTime $dateTime
     *
     * @return Receipt
     */
    public function setCreationDateTime(\DateTime $dateTime);

    /**
     * Возвращает тип чека
     *
     * @return int
     */
    public function getType();

    /**
     * Устанавливает тип чека
     *
     * @param int $type
     *
     * @return Receipt
     */
    public function setType($type);

    /**
     * Возвращает ID заказа, с которым связан данный чек
     *
     * @return int|string
     */
    public function getOrderId();

    /**
     * Устанавливает ID заказа, с которым связан данный чек
     *
     * @param int|string $orderId
     *
     * @return Receipt
     */
    public function setOrderId($orderId);

    /**
     * Возвращает ИНН организации
     *
     * @return string
     */
    public function getCompanyInn();

    /**
     * Устанавливает ИНН организации
     *
     * @param string $inn
     *
     * @return Receipt
     */
    public function setCompanyInn($inn);

    /**
     * Возвращает эл. почту организации (отправителя чека)
     *
     * @return string
     */
    public function getCompanyEmail();

    /**
     * Устанавливает эл. почту организации (отправителя чека)
     *
     * @param string $email
     *
     * @return Receipt
     */
    public function setCompanyEmail($email);

    /**
     * Возвращает место расчетов
     *
     * @return string
     */
    public function getAddress();

    /**
     * Устанавливает место расчетов
     *
     * @param string $address
     *
     * @return Receipt
     */
    public function setAddress($address);

    /**
     * Возвращает систему налогообложения, указываемую в чеке
     *
     * @return int
     */
    public function getTaxSystem();

    /**
     * Устанавливает систему налогообложения, указываемую в чеке
     *
     * @param int|string $taxSystem
     *
     * @return Receipt
     */
    public function setTaxSystem($taxSystem);

    /**
     * Возвращает эл. почту клиента, совершившего заказ, с которым связан данный чек
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * Устанавливает эл. почту клиента, совершившего заказ, с которым связан данный чек
     *
     * @param string $email
     *
     * @return Receipt
     */
    public function setCustomerEmail($email);

    /**
     * Возвращает телефон клиента, совершившего заказ, с которым связан данный чек
     *
     * @return string
     */
    public function getCustomerPhone();

    /**
     * Устанавливает телефон клиента, совершившего заказ, с которым связан данный чек
     *
     * @param string $phone
     *
     * @return Receipt
     */
    public function setCustomerPhone($phone);

    /**
     * Возвращает вид оплаты
     *
     * @return int
     */
    public function getPaymentType();

    /**
     * Устанавливает вид оплаты
     *
     * @param int $paymentType
     *
     * @return Receipt
     */
    public function setPaymentType($paymentType);

    /**
     * Возвращает общую сумму чека
     *
     * @return string
     */
    public function getTotal();

    /**
     * Добавляет позицию в чек
     *
     * @param ReceiptItem $item
     *
     * @return Receipt
     */
    public function addItem(ReceiptItem $item);

    /**
     * Возвращает массив с позициями чека
     *
     * @return ReceiptItem[]
     */
    public function getItems();

    /**
     * Возвращает свойства чека в виде массива, который в последствии можно отправить в онлайн кассу
     *
     * @return array
     */
    public function toArray();

    /**
     * Задает свойства объекта чека из его представления в виде массива
     *
     * @param array $data
     *
     * @return Receipt
     */
    public function fromArray(array $data);
}