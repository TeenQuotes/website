<?php namespace TeenQuotes\AdminPanel\Composers;

use Config, JavaScript, Lang;

class ModerationIndexComposer {

	public function compose($view)
	{
		$data = $view->getData();

		// The number of days required to publish waiting quotes
		$nbDays = $this->getNbdaysToPublishQuotes($data['nbQuotesPending'], $data['nbQuotesPerDay']);
		$view->with('nbDays', $nbDays);

		// The page title
		$view->with('pageTitle', 'Admin | '.Lang::get('layout.nameWebsite'));

		// Useful JS variables
		JavaScript::put([
			'nbQuotesPerDay' => Config::get('app.quotes.nbQuotesToPublishPerDay'),
			'quotesPlural'   => Lang::choice('quotes.quotesText', 2),
			'daysPlural'     => Lang::choice('quotes.daysText', 2),
		]);
	}

	/**
	 * Compute the number of days required to publish the current waiting number of quotes
	 * @param  int $nbPending
	 * @param  int $nbPublishedPerDay
	 * @return int
	 */
	private function getNbdaysToPublishQuotes($nbPending, $nbPublishedPerDay)
	{
		return ceil($nbPending / $nbPublishedPerDay);
	}
}