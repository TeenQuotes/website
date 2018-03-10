<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Pagination;

use Illuminate\Pagination\BootstrapPresenter;

class SeoPresenter extends BootstrapPresenter
{
    /**
     * Create a range of pagination links.
     *
     * @param int $start
     * @param int $end
     * @param int $range
     *
     * @return string
     */
    public function getPageRange($start, $end, $range=1)
    {
        $pages = [];

        for ($page = max($start, 1); $page <= min($end, $this->lastPage); $page += $range) {
            // If the current page is equal to the page we're iterating on, we will create a
            // disabled link for that page. Otherwise, we can create a typical active one
            // for the link. We will use this implementing class's methods to get HTML.
            if ($this->currentPage == $page) {
                $pages[] = $this->getActivePageWrapper($page);
            } else {
                $pages[] = $this->getLink($page);
            }
        }

        return implode('', $pages);
    }

    /**
     * Create a pagination slider link window.
     *
     * @return string
     */
    protected function getPageSlider()
    {
        $window = 6;

        // If the current page is very close to the beginning of the page range, we will
        // just render the beginning of the page range, followed by the last 2 of the
        // links in this list, since we will not have room to create a full slider.
        if ($this->currentPage <= $window) {
            $ending = $this->getFinish();

            $nearestTen = ceil($this->currentPage / 10) * 10;
            $middle     = $this->getPageRange($nearestTen, $nearestTen + 20, 10);

            return $this->getPageRange(1, $window + 2).$middle.$ending;
        }

        // If the current page is close to the ending of the page range we will just get
        // this first couple pages, followed by a larger window of these ending pages
        // since we're too close to the end of the list to create a full on slider.
        elseif ($this->currentPage >= $this->lastPage - $window) {
            $start           = $this->lastPage - ($window + 2);
            $nearestLowerTen = floor(($start - 1) / 10) * 10;
            $before          = $this->getPageRange($nearestLowerTen - 10, $nearestLowerTen, 10);
            $content         = $this->getPageRange($start, $this->lastPage);

            return $this->getStart().$before.$content;
        }

        // If we have enough room on both sides of the current page to build a slider we
        // will surround it with both the beginning and ending caps, with this window
        // of pages in the middle providing a Google style sliding paginator setup.
        else {
            $content = $this->getAdjacentRange();

            return $this->getStart().$content.$this->getFinish();
        }
    }

    /**
     * Get the page range for the current page window.
     *
     * @return string
     */
    public function getAdjacentRange()
    {
        $nearestUpperTen = ceil(($this->currentPage + 3) / 10) * 10;
        $nearestLowerTen = floor(($this->currentPage - 3) / 10) * 10;

        $before = $this->getPageRange($nearestLowerTen - 10, $nearestLowerTen, 10);
        $middle = $this->getPageRange($this->currentPage - 2, $this->currentPage + 2);
        $after  = $this->getPageRange($nearestUpperTen, $nearestUpperTen + 10, 10);

        return $before.$middle.$after;
    }
}
