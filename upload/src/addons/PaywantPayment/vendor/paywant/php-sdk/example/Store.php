<?php
require_once '../Bootstrap.php';

use Paywant\Config;
use Paywant\Model\Buyer;
use Paywant\Store\Create;

$config = new Config();
$config->setAPIKey('TEST'); // API KEY
$config->setSecretKey('TEST'); // API SECRET
$config->setServiceBaseUrl('https://secure.paywant.com');

// credit - debit - prepaid card;
$request = new Create($config);

// buyer info.
$buyer = new Buyer();

$buyer->setUserAccountName('username');
$buyer->setUserEmail('customer@email.com');
$buyer->setUserID('1');

// set objects to Request
$request->setBuyer($buyer);

if ($request->execute())
{ // execute request
    try
    {
        $response = json_decode($request->getResponse());
        if ($response->status == true)
        {
            ?>
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
