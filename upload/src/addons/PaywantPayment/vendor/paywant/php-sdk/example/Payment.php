<?php
require_once '../Bootstrap.php';

use Paywant\Config;
use Paywant\Model\Buyer;
use Paywant\Model\PaymentChannel;
use Paywant\Model\Product;
use Paywant\Payment\Create;

$config = new Config();
$config->setAPIKey('TEST'); // API KEY
$config->setSecretKey('TEST'); // API SECRET
$config->setServiceBaseUrl('https://secure.paywant.com');

// credit - debit - prepaid card;
$request = new Create($config);

// create object for buyer info.
$buyer = new Buyer();

$buyer->setUserAccountName('username'); // if you don't have customers username, please use user email
$buyer->setUserEmail('customer@email.com'); // customer email address
$buyer->setUserID('1'); // immutable customer id

//create object for product info
$product = new Product();
$product->setName('Order ID: 50');
$product->setAmount(10.54);
$product->setExtraData(50);

// payment channels (optional) you can set one channel to show your customer
$paymentChannels = new PaymentChannel();
$paymentChannels->addPaymentChannel(PaymentChannel::MOBILE_OPERATOR);
$paymentChannels->addPaymentChannel(PaymentChannel::CREDIT_CARD);
$paymentChannels->addPaymentChannel(PaymentChannel::LOCAL_TR_BANK);
$paymentChannels->addPaymentChannel(PaymentChannel::MIKROCARD);
$paymentChannels->addPaymentChannel(PaymentChannel::GLOBAL_PAYMENT);

// or for All active channels on your store;
$paymentChannels = new PaymentChannel();
$paymentChannels->addPaymentChannel(PaymentChannel::ALL_CHANNELS);

$product->setPaymentChannel($paymentChannels);

// commissionType {
// TAKE_ALL, TAKE_PARTIAL, REFLECT_TO_CUSTOMER
//}
$product->setCommissionType(Product::TAKE_ALL);

// set objects to Request
$request->setBuyer($buyer);
$request->setProductData($product);

if ($request->execute())
{ // execute request
    try
    {
        $response = json_decode($request->getResponse());
        if ($response->status == true)
        {
            // for example included payment page in your application but also you can redirect user to $response->message;?>
                <div id="paywant-area">
                    <script src="//secure.paywant.com/public/js/paywant.js"></script>
                    <iframe src="<?php echo $response->message; ?>" id="paywantIframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>

                    <script type="text/javascript">
                        setTimeout(function(){ 
                            iFrameResize({ log: false },'#paywantIframe');
                        }, 1000);
                    </script>
                </div>
            <?php
        }
    }
    catch (Exception $ex)
    {
        echo $ex->getMessage();
    }
}
else
{
    echo $request->getError(); // got a error
}
