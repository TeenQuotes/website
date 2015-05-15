<?php

use TeenQuotes\Quotes\Models\Quote;

class EditWaitingQuoteCest
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

    public function editQuoteWaitingForModeration(FunctionalTester $I)
    {
        $I->am("a Teen Quotes' administrator");
        $I->wantTo('edit and publish a quote waiting to be moderated');

        // Go to the admin panel
        $I->navigateToTheAdminPanel();

        $quote = $this->waitingQuotes[0];
        $I->seeModerationButtonsForQuote($quote);

        // Click the edit button and fill the form
        $newContent = str_repeat('abc', 20);
        $this->editContentOfQuote($quote, $newContent);
    }

    private function editContentOfQuote(Quote $quote, $newContent)
    {
        $this->tester->clickEditButtonFor($quote);
        $this->tester->fillNewContentWaitingQuoteForm($newContent);
        $this->tester->seeSuccessFlashMessage('The quote has been edited and approved!');
        $this->tester->seeQuoteIsPending($quote);
        $this->tester->seeAuthorOfQuoteHasBeenWarnedOfApproval($quote);
    }
}
