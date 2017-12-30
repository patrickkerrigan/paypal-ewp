<?php

namespace Pkerrigan\PaypalEwp;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/12/2017
 */
interface ButtonGeneratorInterface
{
    /**
     * @param PaypalCertificate $paypal
     * @param MerchantCertificate $merchant
     * @param array $buttonVariables
     * @return string
     */
    public function encrypt(PaypalCertificate $paypal, MerchantCertificate $merchant, array $buttonVariables);
}
