<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ListWaitingQuotesCest
{
    /**
     * The admin user.
     *
     * @var \TeenQuotes\Users\Models\User
     */
    private $admin;

    /**
     * @var \FunctionalTester
     */
    private $tester;

    /**
     * The number of quotes created, by approved type.
     *
     * @var array
     */
    private $nbQuotes;

    /**
     * Quotes that are waiting to be published.
     *
     * @var array
     */
    private $waitingQuotes;

    public function _before(FunctionalTester $I)
    {
        $this->tester = $I;

        $this->nbQuotes = [
            'pending' => 7,
            'waiting' => 26,
        ];

        $I->createSomePublishedQuotes();
        $this->waitingQuotes = $I->createSomeWaitingQuotes(['nb_quotes' => $this->nbQuotes['waiting']]);
        $I->createSomePendingQuotes(['nb_quotes' => $this->nbQuotes['pending']]);

        $this->admin = $I->logANewUser(['security_level' => 1]);
    }

    public function viewQuotesWaitingForModeration(FunctionalTester $I)
    {
        $I->am("a Teen Quotes' administrator");
        $I->wantTo('view quotes waiting for moderation');

        $nbDaysToPublish = $this->computeNbDaysToPublishQuotes($this->nbQuotes['pending']);

        // Go to the admin panel
        $I->navigateToTheAdminPanel();

        // Check that counters are properly set
        $this->seeNumberOfWaitingQuotesIs($this->nbQuotes['waiting']);
        $this->seeNumberOfPendingQuotesIs($this->nbQuotes['pending']);
        $this->seeNumberOfDaysToPublishIs($nbDaysToPublish);

        $this->seeRequiredElementsForQuotes($this->waitingQuotes);
    }

    /**
     * I can see that quotes have things properly being displayed.
     *
     * @param array $quotes
     */
    private function seeRequiredElementsForQuotes(array $quotes)
    {
        foreach ($this->waitingQuotes as $quote) {
            $this->tester->seeModerationButtonsForQuote($quote);
            $this->tester->seeContentForQuoteWaitingForModeration($quote);
        }
    }

    /**
     * Compute the number of days to publish the given number of quotes.
     *
     * @param int $nbQuotes The number of quotes to publish
     *
     * @return int
     */
    private function computeNbDaysToPublishQuotes($nbQuotes)
    {
        return ceil($nbQuotes / Config::get('app.quotes.nbQuotesToPublishPerDay'));
    }

    /**
     * Assert that the number of waiting quotes is the given value.
     *
     * @param int $nb
     */
    private function seeNumberOfWaitingQuotesIs($nb)
    {
        $this->tester->see($nb, '#nb-quotes-waiting');
    }

    /**
     * Assert that the number days to publish quotes is the given value.
     *
     * @param int $nb
     */
    private function seeNumberOfDaysToPublishIs($nb)
    {
        $this->tester->see($nb, '#nb-quotes-per-day');
    }

    /**
     * Assert that the number of pending quotes is the given value.
     *
     * @param int $nb
     */
    private function seeNumberOfPendingQuotesIs($nb)
    {
        $this->tester->see($nb, '#nb-quotes-pending');
    }
}
