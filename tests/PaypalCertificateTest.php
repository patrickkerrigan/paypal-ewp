<?php

namespace Pkerrigan\PaypalEwp;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/12/2017
 */
class PaypalCertificateTest extends TestCase
{
    /**
     * @expectedException \Pkerrigan\PaypalEwp\Exception\FileNotFoundException
     */
    public function testGivenInvalidPathThrowsException()
    {
        new PaypalCertificate(__DIR__ . '/certs/not-paypal-cert.pem');
    }

    public function testGivenValidPathReturnsFromGetter()
    {
        $certificatePath = __DIR__ . '/certs/paypal-cert.pem';
        $cert = new PaypalCertificate($certificatePath);

        $this->assertEquals($certificatePath, $cert->getCertificatePath());
    }
}
