<?php

namespace Pkerrigan\PaypalEwp;

use Pkerrigan\PaypalEwp\Exception\FileNotFoundException;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/12/2017
 */
class MerchantCertificate
{
    /**
     * @var string
     */
    private $certificateId;
    /**
     * @var string
     */
    private $certificatePath;
    /**
     * @var string
     */
    private $keyPath;
    /**
     * @var string
     */
    private $keyPassphrase;

    /**
     * @param string $certificateId
     * @param string $certificatePath
     * @param string $keyPath
     * @param string $keyPassphrase
     */
    public function __construct(
        string $certificateId,
        string $certificatePath,
        string $keyPath,
        string $keyPassphrase = ""
    ) {
        if (!is_file($certificatePath)) {
            throw new FileNotFoundException("No merchant certificate exists at the given path");
        }

        if (!is_file($keyPath)) {
            throw new FileNotFoundException("No merchant key exists at the given path");
        }

        $this->certificateId = $certificateId;
        $this->certificatePath = $certificatePath;
        $this->keyPath = $keyPath;
        $this->keyPassphrase = $keyPassphrase;
    }

    /**
     * @return string
     */
    public function getCertificateId(): string
    {
        return $this->certificateId;
    }

    /**
     * @return string
     */
    public function getCertificatePath(): string
    {
        return $this->certificatePath;
    }

    /**
     * @return string
     */
    public function getKeyPath(): string
    {
        return $this->keyPath;
    }

    /**
     * @return string
     */
    public function getKeyPassphrase(): string
    {
        return $this->keyPassphrase;
    }
}
