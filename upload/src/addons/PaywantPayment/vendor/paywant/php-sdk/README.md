# paywant/php-sdk
[![Latest Stable Version](https://poser.pugx.org/paywant/php-sdk/v/stable)](https://packagist.org/packages/paywant/php-sdk)
[![Build Status](https://travis-ci.org/paywant/php-sdk.svg?branch=master)](https://travis-ci.org/paywant/php-sdk)

To use this library, you must have an approved Paywant merchant account and store. You can access Paywant at [https://www.paywant.com](https://www.paywant.com).

The document for this service is at [https://developer.paywant.com](https://developer.paywant.com)
# Requirement

PHP 5.6 and above.
Curl

# Install

### Composer

Execute this command to use via [Composer](http://getcomposer.org/) :

```bash
composer require paywant/php-sdk
```

Call composer [autoload](https://getcomposer.org/doc/00-intro.md#autoloading) file for define:

```php
require_once('vendor/autoload.php');
```

### Composer Olmadan

If you don't want to use Composer, you can download the latest version of sdk from [latest release](https://github.com/paywant/php-sdk/releases).

To use, it will be sufficient to include the `Bootstrap.php` file in the folder you downloaded to your project.

```php
require_once('/path/to/php-sdk/Bootstrap.php');
```

# Example
## Usage for Create Store Url 
```php
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
        echo $request->getResponse(); // json 
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

```

To create a direct payment screen, [you can check the example here](https://github.com/paywant/php-sdk/blob/master/example/Payment.php).

For IPN [you can check the example here](https://github.com/paywant/php-sdk/blob/master/example/IPN.php).