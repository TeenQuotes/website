<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Controllers;

use BaseController;
use Config;
use Input;
use Lang;
use Paginator;
use Redirect;
use TeenQuotes\Countries\Models\Country;
use TeenQuotes\Countries\Repositories\CountryRepository;
use TeenQuotes\Exceptions\CountryNotFoundException;
use TeenQuotes\Exceptions\UserNotFoundException;
use TeenQuotes\Quotes\Repositories\QuoteRepository;
use TeenQuotes\Users\Repositories\UserRepository;
use View;

class SearchController extends BaseController
{
    /**
     * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
     */
    private $quoteRepo;

    /**
     * @var \TeenQuotes\Users\Repositories\UserRepository
     */
    private $userRepo;

    public function __construct(CountryRepository $countryRepo, QuoteRepository $quoteRepo, UserRepository $userRepo)
    {
        $this->beforeFilter('search.isValid', ['only' => ['getResults', 'dispatcher']]);

        $this->countryRepo = $countryRepo;
        $this->quoteRepo   = $quoteRepo;
        $this->userRepo    = $userRepo;
    }

    /**
     * Dispatch the search form to search results.
     *
     * @return \Response
     */
    public function dispatcher()
    {
        // filter search.isValid before
        return Redirect::route('search.results', Input::get('search'));
    }

    /**
     * Show the search form.
     *
     * @return \Response
     */
    public function showForm()
    {
        $data = [
            'pageTitle'       => Lang::get('search.formPageTitle'),
            'pageDescription' => Lang::get('search.formPageDescription'),
        ];

        return View::make('search.form', $data);
    }

    /**
     * Show results after a search.
     *
     * @var string The search query
     *
     * @return \Response
     */
    public function getResults($query)
    {
        $nbResultsPerCategory = $this->nbOfResultsPerCategory();

        // Search quotes
        $nbQuotes = $this->quoteRepo->searchCountPublishedWithQuery($query);
        $quotes   = $this->quoteRepo->searchPublishedWithQuery($query, 1, $nbResultsPerCategory);

        // Search users
        $nbUsers = 0;
        $users   = null;
        if ($this->shouldSearchForUsers($query)) {
            $nbUsers = $this->userRepo->countByPartialLogin($query);
            $users   = $this->userRepo->searchByPartialLogin($query, 1, $nbResultsPerCategory);
        }

        // Handle no results
        if ($this->resultsAreEmpty($quotes, $users)) {
            return Redirect::route('search.form')->with('warning', Lang::get('search.noResultsAtAll'));
        }

        $data                           = compact('quotes', 'users', 'nbQuotes', 'nbUsers', 'query');
        $data['maxNbResultPerCategory'] = Config::get('app.search.maxResultsPerCategory');
        $data['pageTitle']              = Lang::get('search.resultsPageTitle', compact('query'));
        $data['pageDescription']        = Lang::get('search.resultsPageDescription', compact('query'));

        return View::make('search.results', $data);
    }

    /**
     * Search users coming from a given country.
     *
     * @param int $country_id The ID of the country
     *
     * @throws \TeenQuotes\Exceptions\CountryNotFoundException If the country was not found
     * @throws \TeenQuotes\Exceptions\UserNotFoundException    If no users were found
     *
     * @return \Response
     */
    public function usersFromCountry($country_id)
    {
        $country = $this->countryRepo->findById($country_id);

        // Handle country not found
        if (is_null($country)) {
            throw new CountryNotFoundException();
        }

        $page     = Input::get('page', 1);
        $pagesize = $this->nbOfResultsPerCategory();

        $users = $this->userRepo->fromCountry($country, $page, $pagesize);
        if ($users->isEmpty() and $this->countryIsMostCommon($country_id)) {
            throw new UserNotFoundException();
        } elseif ($users->isEmpty()) {
            return $this->redirectToDefaultCountrySearch();
        }

        $totalResults = $this->userRepo->countFromCountry($country);

        $paginator = Paginator::make($users->toArray(), $totalResults, $pagesize);
        $data      = compact('users', 'paginator', 'country');

        return View::make('search.users', $data);
    }

    /**
     * Redirect to results from the most common country with a warning.
     *
     * @return \Response
     */
    private function redirectToDefaultCountrySearch()
    {
        return Redirect::route('search.users.country', Country::getDefaultCountry(), 302)
            ->with('redirectedToMostCommonCountry', true);
    }

    /**
     * Get the most common country ID for our users.
     *
     * @return int
     */
    private function getMostCommonCountryID()
    {
        return Country::getDefaultCountry();
    }

    /**
     * Tell if the given ID is the most common country.
     *
     * @param int $countryID
     *
     * @return bool
     */
    private function countryIsMostCommon($countryID)
    {
        return $countryID === $this->getMostCommonCountryID();
    }

    /**
     * Return the number of results per category.
     *
     * @return int
     */
    private function nbOfResultsPerCategory()
    {
        return Config::get('app.search.maxResultsPerCategory');
    }

    /**
     * Tell if search results are empty.
     *
     * @param \Illuminate\Database\Eloquent\Collection      $quotes
     * @param null|\Illuminate\Database\Eloquent\Collection $users
     *
     * @return bool
     */
    private function resultsAreEmpty($quotes, $users)
    {
        return $quotes->isEmpty() and (is_null($users) or $users->isEmpty());
    }

    /**
     * Determine if we should search for users from a search query.
     *
     * @param string $query
     *
     * @return bool
     */
    private function shouldSearchForUsers($query)
    {
        return $this->stringIsSingleWord($query);
    }

    /**
     * Tell if a string is only a single word.
     *
     * @param string $string
     *
     * @return bool
     */
    private function stringIsSingleWord($string)
    {
        return (str_word_count($string) == 1);
    }
}
