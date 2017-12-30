<?php

namespace Pkerrigan\PaypalEwp;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/12/2017
 */
class ButtonGenerator
{
    function encrypt(PaypalCertificate $paypal, MerchantCertificate $merchant, array $buttonVariables)
    {
        $rawDataFile = tempnam(sys_get_temp_dir(), 'PPEWP');
        $signedDataFile = tempnam(sys_get_temp_dir(), 'PPEWP');
        $encryptedDataFile = tempnam(sys_get_temp_dir(), 'PPEWP');

        $buttonVariables['cert_id'] = $merchant->getCertificateId();

        file_put_contents($rawDataFile, $this->buildDataBlob($buttonVariables));

        openssl_pkcs7_sign(
            $rawDataFile,
            $signedDataFile,
            "file://{$merchant->getCertificatePath()}",
            ["file://{$merchant->getKeyPath()}", $merchant->getKeyPassphrase()],
            [],
            PKCS7_BINARY
        );

        $this->mimeToDer($signedDataFile);

        openssl_pkcs7_encrypt(
            $signedDataFile,
            $encryptedDataFile,
            "file://{$paypal->getCertificatePath()}",
            [],
            PKCS7_BINARY,
            OPENSSL_CIPHER_3DES
        );

        $mimeData = file_get_contents($encryptedDataFile);
        $data = "-----BEGIN PKCS7-----\n{$this->stripMimeHeaders($mimeData)}\n-----END PKCS7-----";

        if (file_exists($rawDataFile)) {
            unlink($rawDataFile);
        }
        if (file_exists($signedDataFile)) {
            unlink($signedDataFile);
        }
        if (file_exists($encryptedDataFile)) {
            unlink($encryptedDataFile);
        }

        return $data;
    }

    /**
     * @param array $buttonVariables
     * @return string
     */
    protected function buildDataBlob(array $buttonVariables): string
    {
        $data = "";
        foreach ($buttonVariables as $key => $value) {
            if ($value != "") {
                $data .= "$key=$value\n";
            }
        }

        return trim($data, "\n");
    }

    /**
     * @param $mimeMessage
     * @return string
     */
    protected function stripMimeHeaders($mimeMessage): string
    {
        $mimeParts = explode("\n\n", $mimeMessage);

        return trim($mimeParts[1]);
    }

    /**
     * @param $signedDataFile
     */
    protected function mimeToDer($signedDataFile)
    {
        $mimeSignature = file_get_contents($signedDataFile);
        file_put_contents($signedDataFile, base64_decode($this->stripMimeHeaders($mimeSignature)));
    }
}
