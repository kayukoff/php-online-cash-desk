<?php

namespace OnlineCashDesk\Atol\v3;


class Receipt extends \OnlineCashDesk\Atol\Receipt
{
    /**
     * Receipt constructor.
     *
     * @param $cashDesk|array
     * @param null $orderId
     */
    public function __construct($cashDesk, $orderId = null, $config = [])
    {
        // TODO

        parent::__construct();



        if (is_array($cashDesk)) {
            $this->fromArray($cashDesk);
            return;
        } elseif (!($cashDesk instanceof \OnlineCashDesk\CashDesk) || empty($orderId)) {
            throw new \InvalidArgumentException('Invalid receipt constructor params');
        }

        $config = array_merge($cashDesk->getConfig(), $config);
        if (empty($config['group_code'])
            || empty($config['inn'])
            || empty($config['payment_address'])
            || empty($config['sno'])
            || empty($config['vat'])
        ) {
            throw new \InvalidArgumentException('Online cash desk config does not contain required param(s): INN, payment address, tex');
        }

        $this->setCreationDateTime(new \DateTime('now'));
        $this->setType(isset($config['document_type']) ? $config['document_type'] : self::TYPE_SELL);
        $this->setCompanyInn($config['inn']);
        $this->setAddress($config['payment_address']);
        $this->setTaxSystem($config['sno']);

        if ($orderId) {
            $this->setOrderId($orderId);
        }

        $this->data['group_code'] = $config['group_code'];
        $this->data['service']['callback_url'] = '';
    }

    public function getCompanyInn()
    {
        return isset($this->data['service']['inn']) ? $this->data['service']['inn'] : null;
    }

    public function setCompanyInn($inn)
    {
        $this->data['service']['inn'] = $inn;

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
        return isset($this->data['service']['payment_address']) ? $this->data['service']['payment_address'] : null;
    }

    public function setAddress($address)
    {
        $this->data['service']['payment_address'] = $address;

        return $this;
    }

    public function getCustomerEmail()
    {
        return isset($this->data['receipt']['attributes']['email']) ? $this->data['receipt']['attributes']['email'] : null;
    }

    public function setCustomerEmail($email)
    {
        $this->data['receipt']['attributes']['email'] = $email;

        return $this;
    }

    public function getCustomerPhone()
    {
        return isset($this->data['receipt']['attributes']['phone']) ? $this->data['receipt']['attributes']['phone'] : null;
    }

    public function setCustomerPhone($phone)
    {
        $this->data['receipt']['attributes']['phone'] = preg_replace('/[^0-9]/', '', $phone);

        return $this;
    }

    public function getRawTaxSystem()
    {
        return isset($this->data['receipt']['attributes']['sno']) ? $this->data['receipt']['attributes']['sno'] : null;
    }

    public function setRawTaxSystem($taxSystem)
    {
        $this->data['receipt']['attributes']['sno'] = $taxSystem;
    }

}