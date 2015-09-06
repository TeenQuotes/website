<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ShowProfileCest
{
    /**
     * The authenticated user.
     *
     * @var \TeenQuotes\Users\Models\User
     */
    private $user;

    /**
     * Params used to create the user.
     *
     * @var array
     */
    private $userParams;

    public function _before(FunctionalTester $I)
    {
        $I->createSomePublishedQuotes();
        $this->userParams = [
            'about_me'     => 'Lorem',
            'birthdate'    => '2000-01-12',
            'city'         => 'Paris',
            // ID of Argentina
            'country'      => 10,
            'country_name' => 'Argentina',
            'gender'       => 'F',
        ];

        // Do not pass the country name to TestDummy
        // We just want the country name to assert that
        // the form is filled with the right values
        $overrides = $this->userParams;
        array_forget($overrides, 'country_name');

        $this->user = $I->logANewUser($overrides);
    }

    public function testProfileContainsFullInformation(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('verify that my profile contains all my statistics');

        // Expectations
        $data                           = $this->userParams;
        $data['quotes-published-count'] = 2;
        $data['fav-count']              = 5;
        $data['comments-count']         = 4;
        $data['added-fav-count']        = 3;

        // Set the database
        $quotes = $I->insertInDatabase($data['quotes-published-count'], 'Quote', ['user_id' => $this->user->id]);
        $I->insertInDatabase($data['fav-count'], 'FavoriteQuote', ['user_id' => $this->user->id]);
        $I->insertInDatabase($data['comments-count'], 'Comment', ['user_id' => $this->user->id]);
        $I->insertInDatabase($data['added-fav-count'], 'FavoriteQuote', ['quote_id' => $quotes[0]->id]);

        // Assert that these statistics are displayed
        $I->navigateToMyProfile();
        $I->assertProfileContainsInformation($data);
    }

    public function testWarningProfileHidden(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('see a warning message if my profile is hidden');

        $I->hideProfileForCurrentUser();
        $I->navigateToMyProfile();
        $I->see('Your profile is currently hidden.');
    }

    public function testRedirectToPublishedQuotesIfNoFavorites(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('be redirected to my published quotes if I have no favorites');

        $I->insertInDatabase(5, 'Quote', ['user_id' => $this->user->id]);

        $I->amOnRoute('users.show', [$this->user->login, 'favorites']);
        $I->seeCurrentRouteIs('users.show', $this->user->login);
    }

    public function testRedirectToCommentsIfNoFavoritesAndNoPublishedQuotes(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('be redirected to my comments if I have no favorites and no published quotes');

        $I->insertInDatabase(5, 'Comment', ['user_id' => $this->user->id]);

        $I->amOnRoute('users.show', [$this->user->login, 'favorites']);
        $I->seeCurrentRouteIs('users.show', [$this->user->login, 'comments']);
    }

    public function testRedirectToPublishedQuotesIfNoComments(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('be redirected to my published quotes if I have no comments');

        $I->insertInDatabase(5, 'Quote', ['user_id' => $this->user->id]);

        $I->amOnRoute('users.show', [$this->user->login, 'comments']);
        $I->seeCurrentRouteIs('users.show', $this->user->login);
    }

    public function testRedirectToFavoritesIfNoCommentsAndPublishedQuotes(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('be redirected to my published quotes if I have no comments and published quotes');

        $I->insertInDatabase(5, 'FavoriteQuote', ['user_id' => $this->user->id]);

        $I->amOnRoute('users.show', [$this->user->login, 'comments']);
        $I->seeCurrentRouteIs('users.show', [$this->user->login, 'favorites']);
    }

    public function testRedirectToFavoritesIfNoPublishedQuotes(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('be redirected to my favorites if I have no published quotes');

        $I->insertInDatabase(5, 'FavoriteQuote', ['user_id' => $this->user->id]);

        $I->navigateToMyProfile();
        $I->seeCurrentRouteIs('users.show', [$this->user->login, 'favorites']);
    }

    public function testRedirectToCommentsIfNoPublishedQuotesAndFavorites(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('be redirected to my comments if I have no published quotes and favorites');

        $I->insertInDatabase(5, 'Comment', ['user_id' => $this->user->id]);

        $I->navigateToMyProfile();
        $I->seeCurrentRouteIs('users.show', [$this->user->login, 'comments']);
    }
}
