[![Build Status](https://img.shields.io/travis/patrickkerrigan/paypal-ewp.svg?style=flat-square)](https://travis-ci.org/patrickkerrigan/paypal-ewp) [![Code Climate](https://img.shields.io/codeclimate/maintainability/patrickkerrigan/paypal-ewp.svg?style=flat-square)](https://codeclimate.com/github/patrickkerrigan/paypal-ewp) [![Coverage](https://img.shields.io/codeclimate/c/patrickkerrigan/paypal-ewp.svg?style=flat-square)](https://codeclimate.com/github/patrickkerrigan/paypal-ewp) [![PHP 7.0](https://img.shields.io/badge/php-7.0-blue.svg?style=flat-square)](http://php.net/) [![Packagist](https://img.shields.io/packagist/v/pkerrigan/paypal-ewp.svg?style=flat-square)](https://packagist.org/packages/pkerrigan/paypal-ewp)

# paypal-ewp
A PHP library for generating encrypted PayPal buttons (EWP)

## Prerequisites
To use this library you should first follow the [instructions given by PayPal](https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/encryptedwebpayments/#id08A3I0P20E9) on generating a merchant certificate and key pair and obtaining PayPal's public certificate.

You'll need the following data to generate a button:
* Your Certificate ID issued by PayPal after you uploaded your certificate
* The path to your certificate in PEM format on disk
* The path to your private key in PEM format on disk
* The passphrase for your private key (if you set one)
* The path to PayPal's public certificate in PEM format on disk
* The [HTML Variables](https://developer.paypal.com/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/) you wish to add to your button

## Usage
Below is a complete example which generates an encrypted button for submitting a shopping cart:

```php
<?php
 
use Pkerrigan\PaypalEwp\PaypalCertificate;
use Pkerrigan\PaypalEwp\MerchantCertificate;
use Pkerrigan\PaypalEwp\ButtonGenerator;
 
$buttonGenerator = new ButtonGenerator();
 
$paypalCert = new PaypalCertificate('/path/to/certs/paypal-cert.pem');
 
$merchantCert = new MerchantCertificate(
    'MY_CERTIFICATE_ID',
    '/path/to/certs/merchant-cert.pem',
    '/path/to/certs/merchant-key.pem',
    'MY_KEY_PASSPHRASE' //This argument can be omitted if you have no passphrase
);
 
$buttonVariables = [
    'cmd' => '_cart',
    'upload' => '1',
    'amount_1' => '1.00',
    'item_name_1' => 'Test Item',
    'business' => 'test@example.org',
    'currency_code' => 'GBP'
];
 
$encryptedCart = $buttonGenerator->encrypt($paypalCert, $merchantCert, $buttonVariables);
 
?>
 
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="encrypted" value="<?= $encryptedCart; ?>">
    <input type="submit" value="Proceed to checkout">
</form>
```
