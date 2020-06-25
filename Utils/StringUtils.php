<?php

namespace Dtw\UserBundle\Utils;

use Psr\Container\ContainerInterface;

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
     * @var ContainerInterface
     */
    private $container;

    /**
     * StringUtils constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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

    /**
     * Check the value of the email if it's a valid email.
     *
     * @throws \Exception
     * @param $emailQuestion
     *
     * @author Ali, Muamar
     */
    public function validateEmail($emailQuestion)
    {
        try {
            $emailQuestion->setValidator(function ($answer) {
                if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception('Invalid Email Address');
                } else {
                    if ($this->container->get('manager.user')->isEmailExist($answer) == true) {
                        throw new \Exception('Email Address already exists. Please try other email.');
                    } else {
                        return $answer;
                    }
                }
            });

        } catch (\Exception $e) {
            throw new \Exception('An error occured while checking if email is valid.');
        }
    }
}