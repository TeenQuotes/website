<?php namespace TeenQuotes\Presenters;

use Laracasts\Presenter\Presenter;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;

class QuotePresenter extends Presenter {

	/**
	 * The text that will be tweeted. Given to the Twitter sharer.
	 * @return The text for the Twitter sharer
	 */
	public function textTweet()
	{
		$content = $this->content;
		$maxLength = 115;
		$twitterUsername = Lang::get('layout.twitterUsername');
		$maxLengthAddTwitterUsername = $maxLength - strlen($twitterUsername);

		if (strlen($content) > $maxLength)  {
			$content = substr($content, 0, $maxLength);
			$lastSpace = strrpos($content, " ");

			// After the space, add …
			$content = substr($content, 0, $lastSpace).'…';
		}
		elseif (strlen($content) <= $maxLengthAddTwitterUsername)
			$content .= ' '.$twitterUsername;

		return urlencode($content.' '.URL::route('quotes.show', array($this->id), true));
	}

	/**
	 * Return the text for the Twitter Card. Displayed on single quote page.
	 * @return The text for the Twitter Card
	 */
	public function textTwitterCard()
	{
		$content = $this->content;
		$maxLength = 180;

		if (strlen($content) > $maxLength)  {
			$content = substr($content, 0, $maxLength);
			$lastSpace = strrpos($content, " ");

			// After the space, add …
			$content = substr($content, 0, $lastSpace).'…';
		}

		return $content;
	}
}