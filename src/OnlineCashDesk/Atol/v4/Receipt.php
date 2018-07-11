<?php

/**
 * Под АТОЛ API версии 4.0
 */

namespace OnlineCashDesk\Atol\v4;


class Receipt extends \OnlineCashDesk\Atol\Receipt
{
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
        if (empty($config['group_code'])
            || empty($config['inn'])
            || empty($config['email'])
            || empty($config['payment_address'])
            || empty($config['sno'])
        ) {
            throw new \LogicException('Online cash desk config does not contain required param(s): INN, company email, payment address, tax system, group_code');
        }

        $this->setCompanyInn($config['inn']);
        $this->setCompanyEmail($config['email']);
        $this->setAddress($config['payment_address']);
        $this->setTaxSystem($config['sno']);

        $this->data['group_code'] = $config['group_code'];
        $this->data['service']['callback_url'] = '';
    }

    public function getCompanyInn()
    {
        return isset($this->data['receipt']['company']['inn']) ? $this->data['receipt']['company']['inn'] : null;
    }

    public function setCompanyInn($inn)
    {
        $this->data['receipt']['company']['inn'] = $inn;

        return $this;
    }

    public function getCompanyEmail()
    {
        return isset($this->data['receipt']['company']['email']) ? $this->data['receipt']['company']['email'] : null;
    }

    public function setCompanyEmail($email)
    {
        $this->data['receipt']['company']['email'] = $email;

        return $this;
    }

    public function getAddress()
    {
        return isset($this->data['receipt']['company']['payment_address']) ? $this->data['receipt']['company']['payment_address'] : null;
    }

    public function setAddress($address)
    {
        $this->data['receipt']['company']['payment_address'] = $address;

        return $this;
    }

    public function getCustomerEmail()
    {
        return isset($this->data['receipt']['client']['email']) ? $this->data['receipt']['client']['email'] : null;
    }

    public function setCustomerEmail($email)
    {
        $this->data['receipt']['client']['email'] = $email;

        return $this;
    }

    public function getCustomerPhone()
    {
        return isset($this->data['receipt']['client']['phone']) ? $this->data['receipt']['client']['phone'] : null;
    }

    public function setCustomerPhone($phone)
    {
        $this->data['receipt']['client']['phone'] = preg_replace('/[^0-9]/', '', $phone);

        return $this;
    }

    public function getRawTaxSystem()
    {
        return isset($this->data['receipt']['company']['sno']) ? $this->data['receipt']['company']['sno'] : null;
    }

    public function setRawTaxSystem($taxSystem)
    {
        $this->data['receipt']['company']['sno'] = $taxSystem;
    }

}