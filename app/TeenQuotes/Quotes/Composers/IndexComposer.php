<?php namespace TeenQuotes\Quotes\Composers;

use Auth, Input, InvalidArgumentException, JavaScript, Lang, URL;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tools\Composers\Interfaces\QuotesColorsExtractor;

class IndexComposer implements QuotesColorsExtractor {

	private static $shouldDisplaySharingPromotion = null;

	public function compose($view)
	{
		$data = $view->getData();

		// The AdBlock disclaimer
		JavaScript::put([
			'moneyDisclaimer' => Lang::get('quotes.adblockDisclaimer'),
		]);

		// Build the associative array #quote->id => "color"
		// and store it in session
		$view->with('colors', $this->extractAndStoreColors($data['quotes']));

		// If we have an available promotion, display it
		$shouldDisplayPromotion = $this->shouldDisplayPromotion();
		$view->with('shouldDisplayPromotion', $shouldDisplayPromotion);
		if ($shouldDisplayPromotion)
			$view = $this->addPromotionToData($view);

		// Add stuff related to tops
		$view->with('possibleTopTypes', $this->getPossibleTopTypes());
		$view = $this->buildIconsForTops($view);
	}

	private function buildIconsForTops($view)
	{
		foreach ($this->getPossibleTopTypes() as $topType)
			$view->with('iconForTop'.ucfirst($topType), $this->getIconForTopType($topType));

		return $view;
	}

	private function getIconForTopType($topType)
	{
		switch ($topType)
		{
			case 'favorites':
				return 'fa-heart';

			case 'comments':
				return 'fa-comments';
		}

		throw new InvalidArgumentException("Can't find icon for view ".$viewName);
	}

	private function getPossibleTopTypes()
	{
		return ['favorites', 'comments'];
	}

	private function addPromotionToData($view)
	{
		$data = $this->getDataPromotion();

		foreach ($data as $key => $value) {
			$view->with($key, $value);
		}

		return $view;
	}

	public function extractAndStoreColors($quotes)
	{
		$colors = Quote::storeQuotesColors($quotes->lists('id'));

		return $colors;
	}

	private function getDataPromotion()
	{
		if ($this->shouldDisplaySignupPromotion())
			return $this->getDataSignupPromotion();

		if ($this->shouldDisplaySharingPromotion())
			return $this->getDataSharingPromotion();
	}

	private function getDataSharingPromotion()
	{
		return [
			'promotionTitle' => Lang::get('quotes.sharePromotionTitle'),
			'promotionText'  => Lang::get('quotes.sharePromotion'),
			'promotionIcon'  => 'fa-heart-o',
		];
	}

	private function getDataSignupPromotion()
	{
		return [
			'promotionTitle' => Lang::get('quotes.signupPromotionTitle'),
			'promotionText'  => Lang::get('quotes.signupPromotion', ['url' => URL::route('signup')]),
			'promotionIcon'  => 'fa-smile-o',
		];
	}

	private function shouldDisplaySharingPromotion()
	{
		if (is_null(self::$shouldDisplaySharingPromotion))
			self::$shouldDisplaySharingPromotion = (rand(1, 100) == 42);

		return self::$shouldDisplaySharingPromotion;
	}

	private function shouldDisplaySignupPromotion()
	{
		return Auth::guest() AND Input::get('page') >= 2;
	}

	private function shouldDisplayPromotion()
	{
		return $this->shouldDisplaySharingPromotion() OR $this->shouldDisplaySignupPromotion();
	}
}