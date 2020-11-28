<?php

namespace Pkerrigan\PaypalEwp;

use PHPUnit\Framework\TestCase;
use Pkerrigan\PaypalEwp\Exception\FileNotFoundException;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/12/2017
 */
class MerchantCertificateTest extends TestCase
{
    const CERT_ID = 'CERT123';

    public function testGivenInvalidCertificatePathThrowsException()
    {
        $this->expectException(FileNotFoundException::class);

        new MerchantCertificate(
            self::CERT_ID,
            __DIR__ . '/certs/not-merchant-cert.pem',
            __DIR__ . '/certs/merchant-key.pem'
        );
    }

    public function testGivenInvalidKeyPathThrowsException()
    {
        $this->expectException(FileNotFoundException::class);

        new MerchantCertificate(
            self::CERT_ID,
            __DIR__ . '/certs/merchant-cert.pem',
            __DIR__ . '/certs/not-merchant-key.pem'
        );
    }

    public function testGivenValidPathsReturnsFromGetters()
    {
        $certificatePath = __DIR__ . '/certs/paypal-cert.pem';
        $keyPath = __DIR__ . '/certs/paypal-cert.pem';
        $cert = new MerchantCertificate(self::CERT_ID, $certificatePath, $keyPath);

        $this->assertEquals(self::CERT_ID, $cert->getCertificateId());
        $this->assertEquals($certificatePath, $cert->getCertificatePath());
        $this->assertEquals($keyPath, $cert->getKeyPath());
        $this->assertEquals("", $cert->getKeyPassphrase());
    }
}
