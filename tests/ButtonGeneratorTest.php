<?php

namespace Pkerrigan\PaypalEwp;

use PHPUnit\Framework\TestCase;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/12/2017
 */
class ButtonGeneratorTest extends TestCase
{
    const ENCRYPTED_HEADER = "MIME-Version: 1.0\nContent-Disposition: attachment; filename=\"smime.p7m\"\nContent-Type: application/x-pkcs7-mime; smime-type=enveloped-data; name=\"smime.p7m\"\nContent-Transfer-Encoding: base64\n\n";
    const SIGNED_HEADER = "MIME-Version: 1.0\nContent-Disposition: attachment; filename=\"smime.p7m\"\nContent-Type: application/x-pkcs7-mime; smime-type=signed-data; name=\"smime.p7m\"\nContent-Transfer-Encoding: base64\n\n";
    const CERT_ID = 'CERT123';

    public function testEncrypt()
    {
        $paypalCert = new PaypalCertificate(__DIR__ . '/certs/paypal-cert.pem');
        $merchantCert = new MerchantCertificate(
            self::CERT_ID,
            __DIR__ . '/certs/merchant-cert.pem',
            __DIR__ . '/certs/merchant-key.pem'
        );

        $button = [
            'cmd' => '_cart',
            'upload' => '1',
            'amount_1' => '1.00',
            'item_name_1' => 'testing',
            'business' => 'test@example.org',
            'currency_code' => 'GBP'
        ];

        $generator = new ButtonGenerator();

        $data = $generator->encrypt($paypalCert, $merchantCert, $button);

        $this->assertEquals(array_merge($button, ['cert_id' => self::CERT_ID]), $this->paypalDecrypt($data));
    }

    protected function paypalDecrypt($data): array
    {
        $inFile = tempnam(sys_get_temp_dir(), 'TEST');
        $outFile = tempnam(sys_get_temp_dir(), 'TEST');
        $nullFile = tempnam(sys_get_temp_dir(), 'TEST');

        file_put_contents($inFile, self::ENCRYPTED_HEADER . $data);

        openssl_pkcs7_decrypt(
            $inFile,
            $outFile,
            "file://" . __DIR__ . '/certs/paypal-cert.pem',
            "file://" . __DIR__ . '/certs/paypal-key.pem'
        );

        $decrypted = chunk_split(base64_encode(file_get_contents($outFile)));
        file_put_contents($inFile, self::SIGNED_HEADER . $decrypted);

        $verified = openssl_pkcs7_verify(
            $inFile,
            PKCS7_NOVERIFY,
            $nullFile,
            [],
            __DIR__ . '/certs/merchant-cert.pem',
            $outFile
        );

        $rawData = file_get_contents($outFile);

        unlink($inFile);
        unlink($outFile);
        unlink($nullFile);

        $variables = $this->parseDataBlob($rawData);

        return $verified ? $variables : [];
    }

    /**
     * @param $rawData
     * @return array
     */
    protected function parseDataBlob($rawData): array
    {
        $lines = explode("\n", $rawData);
        $variables = [];
        foreach ($lines as $line) {
            list($key, $value) = explode("=", $line, 2);
            $variables[$key] = $value;
        }

        return $variables;
    }
}
