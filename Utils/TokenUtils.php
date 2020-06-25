<?php

namespace Dtw\UserBundle\Utils;

/**
 * This class is used for generating a token.
 *
 * @package DtwCoreBundle\Utils
 *
 * @author Richard Soliven
 */
class TokenUtils
{
    /**
     * Generate the token.
     *
     * @author Richard Soliven
     *
     * @return string
     */
    public function generateToken()
    {
        $token = bin2hex(openssl_random_pseudo_bytes(32));

        return $token;
    }
}