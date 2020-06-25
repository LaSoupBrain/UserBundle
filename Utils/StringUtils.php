<?php

namespace Dtw\UserBundle\Utils;

/**
 * This class is used for all operation on a string.
 *
 * @package Dtw\UserBundle\Utils
 *
 * @author  Richard Soliven
 */
class StringUtils
{
    /**
     * Explode a string using a pattern.
     *
     * @param string $string The string to explode.
     * @param string $delimiter The pattern to explode the string
     * @param integer $limit The maximum of elements returned in the result.
     *
     * @throws \Exception
     * @author Richard Soliven
     *
     * @return array
     */
    public function explodeToArray(
        string $string,
        string $delimiter,
        int $limit = PHP_INT_MAX
    ): array {
        $result = explode($delimiter, $string, $limit);

        if (empty($result) && !is_array($result)) {
            throw new \Exception;
        }

        return $result;
    }
}