<?php

namespace PaywantPayment\Payment;

use Exception;
use Paywant\Config;
use Paywant\Model\Buyer;
use Paywant\Model\PaymentChannel;
use Paywant\Model\Product;
use Paywant\Payment\Create;
use Paywant\Utilities\Helper;
use XF\Entity\PaymentProfile;
use XF\Entity\PurchaseRequest;
use XF\Mvc\Controller;
use XF\Payment\AbstractProvider;
use XF\Payment\CallbackState;
use XF\Purchasable\Purchase;

class Paywant extends AbstractProvider
{
    protected $supportedCurrencies = [
        'TRY',
        'USD',
    ];

    private $paywant;

    public function getTitle()
    {
        return 'Paywant';
    }

    public function verifyConfig(array &$options, &$errors = [])
    {
        if (empty($options['paywant_api_key']) or empty($options['paywant_secret_key']) or empty($options['usd_try']))
        {
            $errors[] = \XF::phrase('you_need_to_provide_your_api_and_secret_key');

            return false;
        }

        if (!is_numeric($options['usd_try']))
        {
            $errors[] = \XF::phrase('usd_try_must_be_numeric');

            return false;
        }

        $error = '';
        $this->_setupPaywant($options, $error);
        if ($error)
        {
            $errors[] = $error;

            return false;
        }

        return true;
    }

    public function initiatePayment(Controller $controller, PurchaseRequest $purchaseRequest, Purchase $purchase)
    {
        $paymentProfile = $purchase->paymentProfile;
        $this->_setupPaywant($paymentProfile->options);

        $viewParams = $this->getPaymentParams($purchaseRequest, $purchase);
        $viewParams['paywant'] = [
            'status' => false,
            'message' => 'Ödeme Ekranı oluşturulamadı.',
        ];

        $currency = $purchase->currency;

        if (!$this->verifyCurrency($paymentProfile, $currency))
        {
            $viewParams['paywant']['message'] = 'Desteklenen Para Birimleri: ' . implode(',', $this->supportedCurrencies);

            return $controller->view('PaywantPayment:Payment\Initiate', 'payment_initiate_paywant', $viewParams);
        }

        $buyer = new Buyer();

        $buyer->setUserAccountName($purchase->purchaser->email); // if you don't have customers username, please use user email
        $buyer->setUserEmail($purchase->purchaser->email); // customer email address
        $buyer->setUserID($purchase->purchaser->user_id); // immutable customer id

        $product = new Product();
        $product->setName($purchase->title);
        $product->setAmount($viewParams['cost']);
        $product->setExtraData($purchaseRequest->purchase_request_id);

        $paymentChannels = new PaymentChannel();
        $paymentChannels->addPaymentChannel(PaymentChannel::ALL_CHANNELS);

        $product->setPaymentChannel($paymentChannels);
        $product->setCommissionType(Product::TAKE_ALL);

        $this->paywant->setUserIPAddress(Helper::getIPAddress());
        $this->paywant->setBuyer($buyer);
        $this->paywant->setProductData($product);
        $this->paywant->setRedirect_url($purchase->returnUrl);

        if ($this->paywant->execute())
        { // execute request
            try
            {
                $response = json_decode($this->paywant->getResponse());
                $viewParams['paywant']['status'] = $response->status;
                $viewParams['paywant']['message'] = $response->message;
            }
            catch (Exception $ex)
            {
                $viewParams['paywant']['message'] = $ex->getMessage();
            }
        }
        else
        {
            $viewParams['paywant']['message'] = $this->paywant->getError(); // got a error
        }

        $paymentRepo = \XF::repository('XF:Payment');

        $paymentRepo->logCallback(
            $purchaseRequest->request_key,
            $this->providerId,
            '0',
            'info',
            'Customer and plan/charge created',
            [
                'plan' => $purchase->title,
                'customer' => $purchase->purchaser->user_id,
                'cost' => $purchase->cost,
                'currency' => $purchase->currency,
                'try' => $viewParams['cost'],
                'purchase_request_id' => $purchaseRequest->purchase_request_id,
            ],
            0
        );

        return $controller->view('PaywantPayment:Payment\Initiate', 'payment_initiate_paywant', $viewParams);
    }

    public function processPayment(Controller $controller, PurchaseRequest $purchaseRequest, PaymentProfile $paymentProfile, Purchase $purchase)
    {
    }

    public function supportsRecurring(PaymentProfile $paymentProfile, $unit, $amount, &$result = self::ERR_NO_RECURRING)
    {
        return false;
    }

    /**
     * @return CallbackState
     */
    public function setupCallback(\XF\Http\Request $request)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $state = new CallbackState();
        $state->transactionId = $request->filter('transactionID', 'str');
        $state->signature = $_POST['hash'];
        $state->costAmount = $request->filter('paymentTotal', 'str');
        $state->taxAmount = 0;
        $state->netProfit = $request->filter('netProfit', 'str');
        $state->costCurrency = 'TRY';
        $state->paymentStatus = $request->filter('status', 'str');
        $state->purchase_request_id = $request->filter('extraData', 'str');

        $state->userID = $request->filter('userID', 'str');
        $state->userAccountName = $request->filter('userAccountName', 'str');
        $state->paymentChannel = $request->filter('paymentChannel', 'str');

        $state->ip = $request->getIp();
        $state->_POST = $_POST;

        $db = \XF::db();
        $request_key = $db->fetchOne('SELECT request_key FROM xf_purchase_request WHERE purchase_request_id = ?', $state->purchase_request_id);

        if (!$request_key)
        {
            return false;
        }

        $state->requestKey = $request_key;
        /*
        $matchingLogsFinder = $paymentRepo->findLogsByTransactionId($state->transactionId);
        if ($matchingLogsFinder->total())
        {
            $state->logType = 'info';
            $state->logMessage = 'OK';

            return false;
        }*/

        return $state;
    }

    public function validateCallback(CallbackState $state)
    {
        if (!$state->signature || empty($state->signature))
        {
            $state->logType = 'error';
            $state->logMessage = 'hash yok';
            $state->httpCode = 400;

            return false;
        }

        $paymentProfile = $state->getPaymentProfile();
        $purchaseRequest = $state->getPurchaseRequest();

        if (!$paymentProfile || !$purchaseRequest)
        {
            $state->logType = 'error';
            $state->logMessage = 'payment profile error.';

            return false;
        }

        $hashCheck = base64_encode(hash_hmac('sha256', "{$state->transactionId}|{$state->purchase_request_id}|{$state->userID}|{$state->userAccountName}|{$state->paymentStatus}|{$state->paymentChannel}|{$state->costAmount}|{$state->netProfit}" . $paymentProfile->options['paywant_api_key'], $paymentProfile->options['paywant_secret_key'], true));
        if ($state->signature != $hashCheck)
        {
            $state->logType = 'error';
            $state->logMessage = 'hash failure';
            $state->httpCode = 400;

            return false;
        }

        return $state;
    }

    public function validateTransaction(CallbackState $state)
    {
        if (!$state->transactionId)
        {
            $state->logType = 'info';
            $state->logMessage = 'Transaction id yok.';

            return false;
        }

        $paymentRepo = \XF::repository('XF:Payment');
        $matchingLogsFinder = $paymentRepo->findLogsByTransactionId($state->transactionId);
        if ($matchingLogsFinder->total())
        {
            $state->logType = 'info';
            $state->logMessage = 'OK';

            return false;
        }

        return parent::validateTransaction($state);
    }

    public function validateCost(CallbackState $state)
    {
        return true;
    }

    public function completeTransaction(CallbackState $state)
    {
        if ($state->paymentStatus == '100')
        {
            parent::completeTransaction($state);
            $state->logType = 'info';
            $state->logMessage = 'OK';
        }
        else
        {
            $state->logType = 'error';
            $state->logMessage = 'Payment Cancelled';
        }
    }

    public function getPaymentResult(CallbackState $state)
    {
        switch ($state->paymentStatus)
        {
            case '100':
                $state->paymentResult = CallbackState::PAYMENT_RECEIVED;
                break;
        }
    }

    public function prepareLogData(CallbackState $state)
    {
        $state->logDetails = $state->_POST;
    }

    public function verifyCurrency(PaymentProfile $paymentProfile, $currencyCode)
    {
        return in_array($currencyCode, $this->supportedCurrencies);
    }

    protected function getPaymentParams(PurchaseRequest $purchaseRequest, Purchase $purchase)
    {
        $paymentProfile = $purchase->paymentProfile;

        return [
            'purchaseRequest' => $purchaseRequest,
            'paymentProfile' => $paymentProfile,
            'purchaser' => $purchase->purchaser,
            'purchase' => $purchase,
            'purchasableTypeId' => $purchase->purchasableTypeId,
            'purchasableId' => $purchase->purchasableId,
            'publicKey' => $paymentProfile->options['publicKey'],
            'cost' => $this->prepareCost($paymentProfile->options, $purchase->cost, $purchase->currency),
        ];
    }

    protected function prepareCost(array $options, $cost, $currency)
    {
        if ($currency == 'USD')
        {
            return bcmul($cost, $options['usd_try'], 2);
        }
        if ($currency == 'TRY')
        {
            return bcmul($cost, 1, 2);
        }
    }

    private function _setupPaywant(array $options, &$error = '')
    {
        if ($this->paywant instanceof Paywant)
        {
            return;
        }

        require_once __DIR__ . '/../vendor/autoload.php';

        try
        {
            $config = new Config();
            $config->setAPIKey($options['paywant_api_key']); // API KEY
            $config->setSecretKey($options['paywant_secret_key']); // API SECRET
            $config->setServiceBaseUrl('https://secure.paywant.com');

            $this->paywant = new Create($config);
        }
        catch (\Exception $e)
        {
            $error = 'Paywant error: ' . $e->getMessage();
        }
    }
}
