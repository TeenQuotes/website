<?php

namespace TeenQuotes\Api\V1\Tools;

use TeenQuotes\Api\V1\Interfaces\PageBuilderInterface;

class PageBuilder implements PageBuilderInterface
{
    /**
     * Build page parameters for a paginated response.
     *
     * @param int    $page       The current page
     * @param int    $pagesize   The number of elements per page
     * @param int    $totalPages The total number of pages
     * @param string $url        The URL of the endpoint
     * @param string $getParams  Additional get parameters
     *
     * @return array Keys: has_next_page, has_previous_page[, next_page, previous_page]
     */
    public function buildPagesArray($page, $pagesize, $totalPages, $url, $getParams)
    {
        // Add next page URL
        list($has_next_page, $next_page) = $this->buildNextPage($page, $pagesize, $totalPages, $url, $getParams);

        // Add previous page URL
        list($has_previous_page, $previous_page) = $this->buildPreviousPage($page, $pagesize, $url, $getParams);

        $pagesArray = compact('has_next_page', 'next_page', 'has_previous_page', 'previous_page');

        // Remove null values from the array
        return array_filter($pagesArray, [$this, 'notNull']);
    }

    /**
     * Return true if the value is not null.
     *
     * @param mixed $v
     *
     * @return bool
     */
    private function notNull($v)
    {
        return !is_null($v);
    }

    /**
     * Build "next page" parameters for a paginated response.
     *
     * @param int    $page       The current page
     * @param int    $pagesize   The number of elements per page
     * @param int    $totalPages The total number of pages
     * @param string $url        The URL of the endpoint
     * @param string $getParams  Additional get parameters
     *
     * @return array The first element is a boolean. The second element,
     *               is the next page's URL if we've got a next page
     */
    private function buildNextPage($page, $pagesize, $totalPages, $url, $getParams)
    {
        $hasNextPage = ($page < $totalPages);
        $nextPage = null;

        if ($hasNextPage) {
            $nextPage = $url.'?page='.($page + 1).'&pagesize='.$pagesize.$getParams;
        }

        return [$hasNextPage, $nextPage];
    }

    /**
     * Build "previous page" parameters for a paginated response.
     *
     * @param int    $page      The current page
     * @param int    $pagesize  The number of elements per page
     * @param string $url       The URL of the endpoint
     * @param string $getParams Additional get parameters
     *
     * @return array The first element is a boolean. The second element,
     *               is the previous page's URL if we've got a previous page
     */
    private function buildPreviousPage($page, $pagesize, $url, $getParams)
    {
        $hasPreviousPage = ($page >= 2);
        $previousPage = null;

        if ($hasPreviousPage) {
            $previousPage = $url.'?page='.($page - 1).'&pagesize='.$pagesize.$getParams;
        }

        return [$hasPreviousPage, $previousPage];
    }
}
