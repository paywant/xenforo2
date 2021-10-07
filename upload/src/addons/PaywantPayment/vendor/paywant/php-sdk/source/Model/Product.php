<?php

namespace Paywant\Model;

use Paywant\JsonSerializableModel;

class Product extends JsonSerializableModel
{
    const TAKE_ALL = 1;
    const TAKE_PARTIAL = 2;
    const REFLECT_TO_CUSTOMER = 3;

    private $name;
    private $amount;
    private $extraData;
    private $paymentChannel;
    private $commissionType;

    public function __construct()
    {
        $this->currency = 'TRY'; // default;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * Get the value of name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name.
     *
     * @param mixed $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of amount.
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount.
     *
     * @param mixed $amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = bcmul(bcmul($amount, 1, 2), 100);

        return $this;
    }

    /**
     * Get the value of extraData.
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * Set the value of extraData.
     *
     * @param mixed $extraData
     *
     * @return self
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;

        return $this;
    }

    /**
     * Get the value of paymentChannel.
     */
    public function getPaymentChannel()
    {
        return $this->paymentChannel;
    }

    /**
     * Set the value of paymentChannels.
     *
     * @return self
     */
    public function setPaymentChannel(PaymentChannel $paymentChannel)
    {
        $this->paymentChannel = $paymentChannel->getPaymentChannels();

        return $this;
    }

    /**
     * Get the value of commissionType.
     */
    public function getCommissionType()
    {
        return $this->commissionType;
    }

    /**
     * Set the value of commissionType.
     *
     * @param mixed $commissionType
     *
     * @return self
     */
    public function setCommissionType($commissionType)
    {
        $this->commissionType = $commissionType;

        return $this;
    }
}
