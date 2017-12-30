<?php

namespace Pkerrigan\PaypalEwp;

use Pkerrigan\PaypalEwp\Exception\FileNotFoundException;

/**
 *
 * @author Patrick Kerrigan (patrickkerrigan.uk)
 * @since 30/12/2017
 */
class PaypalCertificate
{
    /**
     * @var string
     */
    private $certificatePath;

    /**
     * @param string $certificatePath
     */
    public function __construct(string $certificatePath)
    {
        if (!is_file($certificatePath)) {
            throw new FileNotFoundException("No PayPal certificate exists at the given path");
        }

        $this->certificatePath = $certificatePath;
    }

    /**
     * @return string
     */
    public function getCertificatePath(): string
    {
        return $this->certificatePath;
    }
}
