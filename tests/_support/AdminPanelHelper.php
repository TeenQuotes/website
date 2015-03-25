<?php namespace Codeception\Module;

use Codeception\Module;
use TeenQuotes\AdminPanel\Helpers\Moderation;
use TeenQuotes\Quotes\Models\Quote;

class AdminPanelHelper extends Module {

	/**
	 * I can see that the content of a quote is displayed
	 * @param  \TeenQuotes\Quotes\Models\Quote  $q
	 */
	public function seeContentForQuoteWaitingForModeration(Quote $q)
	{
		$parentClass = $this->getCssParentClass($q);

		$I = $this->getModule('Laravel4');

		$I->see($q->content, $parentClass);
	}

	/**
	 * I can see moderation buttons for each quote
	 * @param  \TeenQuotes\Quotes\Models\Quote  $q
	 */
	public function seeModerationButtonsForQuote(Quote $q)
	{
		$parentClass = $this->getCssParentClass($q);

		$I = $this->getModule('Laravel4');

		// I can see moderation decisions
		$moderationDecisions = Moderation::getAvailableTypes();
		foreach ($moderationDecisions as $decision)
		{
			$cssClass = $parentClass.' .quote-moderation[data-decision="'.$decision.'"]';
			$I->seeNumberOfElements($cssClass, 1);
		}

		// I can see the edit button
		$I->seeNumberOfElements($parentClass.' .fa-pencil-square-o', 1);
	}

	/**
	 * Get the CSS class for a quote
	 * @param  \TeenQuotes\Quotes\ModelsQuote  $q
	 */
	private function getCssParentClass(Quote $q)
	{
		return '.quote[data-id='.$q->id.']';
	}
}