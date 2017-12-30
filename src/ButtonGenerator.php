<?php

namespace Pkerrigan\PaypalEwp;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/12/2017
 */
class ButtonGenerator
{
    const TEMP_FILE_PREFIX = 'PPEWP';

    /**
     * @param PaypalCertificate $paypal
     * @param MerchantCertificate $merchant
     * @param array $buttonVariables
     * @return string
     */
    public function encrypt(PaypalCertificate $paypal, MerchantCertificate $merchant, array $buttonVariables)
    {
        list($rawDataFile, $signedDataFile, $encryptedDataFile) = $this->tempFiles(3);

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

        $this->cleanupFiles($rawDataFile, $signedDataFile, $encryptedDataFile);

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
     * @param string $mimeMessage
     * @return string
     */
    protected function stripMimeHeaders(string $mimeMessage): string
    {
        $mimeParts = explode("\n\n", $mimeMessage);

        return trim($mimeParts[1]);
    }

    /**
     * @param string $signedDataFile
     */
    protected function mimeToDer(string $signedDataFile)
    {
        $mimeSignature = file_get_contents($signedDataFile);
        file_put_contents($signedDataFile, base64_decode($this->stripMimeHeaders($mimeSignature)));
    }

    /**
     * @param string[] $files
     */
    protected function cleanupFiles(string ...$files)
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * @param int $number
     * @return string[]
     */
    protected function tempFiles(int $number): array
    {
        $files = [];

        for ($i = 0; $i < $number; $i++) {
            $files[] = tempnam(sys_get_temp_dir(), self::TEMP_FILE_PREFIX);
        }

        return $files;
    }
}
