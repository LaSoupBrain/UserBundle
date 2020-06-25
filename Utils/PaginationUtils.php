<?php

namespace Dtw\UserBundle\Utils;

/**
 * This class is used for the pagination 
 *
 * @package Dtw\UserBundle\Utils
 *
 * @author Richard
 */
class PaginationUtils
{
    /**
     * Limit value for getting of user.
     */
    const BATCH_LIMIT = 10;

    /**
     * The default value of page.
     */
    const DEFAULT_PAGE = 1;

    /**
     * Dividing the counted data by batch limit and get the round up value.
     *
     * @param int $countedData the total of the data.
     *
     * @author Richard
     *
     * @return int
     */
    public function getTotalPages(int $countedData): int
    {
        try {
            return (int) abs(ceil($countedData/self::BATCH_LIMIT));
        } catch (\Exception $e) {
			return self::DEFAULT_PAGE;
		}
    }

    /**
     * Get the start of the batch.
     *
     * @param int $currentPage the current page of the pagination.
     *
     * @author Richard
     *
     * @return int
     */
    public function batchStartFrom(int $currentPage): int
    {
        try {
            return ($currentPage * self::BATCH_LIMIT) - self::BATCH_LIMIT;
        } catch (\Exception $e) {
            return self::DEFAULT_PAGE;
        }
    }
}