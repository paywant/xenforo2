<?php

namespace Paywant\Model;

use Paywant\JsonSerializableModel;

class PaymentChannel extends JsonSerializableModel
{
    const ALL_CHANNELS = 0;
    const MOBILE_OPERATOR = 1;
    const CREDIT_CARD = 2;
    const LOCAL_TR_BANK = 3;
    const MIKROCARD = 5;
    const GLOBAL_PAYMENT = 10;

    private $paymentChannels = [];

    public function __construct()
    {
    }

    public function addPaymentChannel(int $paymentChannelId)
    {
        $this->paymentChannels[] = $paymentChannelId;
    }

    public function jsonSerialize()
    {
        return $this->getPaymentChannels();
    }

    /**
     * Get the value of paymentChannels.
     */
    public function getPaymentChannels()
    {
        return implode(',', $this->paymentChannels);
    }
}
