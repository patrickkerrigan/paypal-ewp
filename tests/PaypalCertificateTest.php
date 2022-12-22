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
    public function testGivenInvalidPathThrowsException()
    {
        $this->expectException(\Pkerrigan\PaypalEwp\Exception\FileNotFoundException::class);

        new PaypalCertificate(__DIR__ . '/certs/not-paypal-cert.pem');
    }

    public function testGivenValidPathReturnsFromGetter()
    {
        $certificatePath = __DIR__ . '/certs/paypal-cert.pem';
        $cert = new PaypalCertificate($certificatePath);

        $this->assertEquals($certificatePath, $cert->getCertificatePath());
    }
}
