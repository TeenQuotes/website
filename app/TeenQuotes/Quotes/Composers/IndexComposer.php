<?php namespace TeenQuotes\Quotes\Composers;

use Auth, Input, JavaScript, Lang, URL;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tools\Composers\Interfaces\QuotesColorsExtractor;

class IndexComposer implements QuotesColorsExtractor {

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
		$view->with('shouldDisplayPromotion', $this->shouldDisplayPromotion());
		if ($this->shouldDisplayPromotion())
			$view = $this->addPromotionToData($view);
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
		return rand(1, 100) == 42;
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