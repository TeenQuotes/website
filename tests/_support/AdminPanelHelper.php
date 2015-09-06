<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Codeception\Module;

use Codeception\Module;
use TeenQuotes\AdminPanel\Helpers\Moderation;
use TeenQuotes\Quotes\Models\Quote;

class AdminPanelHelper extends Module
{
    /**
     * I can see that the content of a quote is displayed.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $q
     */
    public function seeContentForQuoteWaitingForModeration(Quote $q)
    {
        $parentClass = $this->getCssParentClass($q);

        $I = $this->getModule('Laravel4');

        $I->see($q->content, $parentClass);
    }

    /**
     * I can see moderation buttons for each quote.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $q
     */
    public function seeModerationButtonsForQuote(Quote $q)
    {
        $parentClass = $this->getCssParentClass($q);

        $I = $this->getModule('Laravel4');

        // I can see moderation decisions
        $moderationDecisions = Moderation::getAvailableTypes();
        foreach ($moderationDecisions as $decision) {
            $cssClass = $parentClass.' .quote-moderation[data-decision="'.$decision.'"]';
            $I->seeNumberOfElements($cssClass, 1);
        }

        // I can see the edit button
        $I->seeNumberOfElements($this->getCssEditLink($q), 1);
    }

    /**
     * Click the edit button for a quote and assert that we've been redirected.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $q
     */
    public function clickEditButtonFor(Quote $q)
    {
        $I = $this->getModule('Laravel4');

        $I->click($this->getCssEditLink($q));
        $I->seeCurrentRouteIs('admin.quotes.edit', $q->id);
    }

    /**
     * Check that the author of a quote got an email telling him that one of its
     * quote was approved.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $quote
     */
    public function seeAuthorOfQuoteHasBeenWarnedOfApproval(Quote $quote)
    {
        $I = $this->getModule('MailCatcher');

        $I->seeInLastEmailTo($quote->user->email, 'Your quote has been approved!');
    }

    /**
     * Check that a quote is pending. Grab the quote from the DB.
     *
     * @param \TeenQuotes\Quotes\Models\Quote $q
     */
    public function seeQuoteIsPending(Quote $q)
    {
        $I = $this->getModule('Laravel4');

        $quote = $I->grabRecord('quotes', ['id' => $q->id]);

        $I->assertEquals($quote->approved, Quote::PENDING);
    }

    /**
     * Get the CSS class for the link to the edit form for a quote.
     *
     * @param \TeenQuotes\Quotes\ModelsQuote $q
     */
    private function getCssEditLink(Quote $q)
    {
        $parentClass = $this->getCssParentClass($q);

        return $parentClass.' .admin__quote__edit-button';
    }

    /**
     * Get the CSS class for a quote.
     *
     * @param \TeenQuotes\Quotes\ModelsQuote $q
     */
    private function getCssParentClass(Quote $q)
    {
        return '.quote[data-id='.$q->id.']';
    }
}
