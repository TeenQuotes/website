<?php namespace TeenQuotes\Tools;

class TextTools {

	/**
	 * Used to displays HTML in a form when an error occurs
	 * @param string $text The text to display
	 * @return string Echo the HTML corresponding to the warning
	 */
	public static function warningTextForm($text)
	{
		echo '
		<div class="error-form animated shake">
			<i class="fa fa-warning red"></i>'.$text.'
		</div>';
	}

	/**
	 * Used to build a campagin link
	 * @param string $url The URL
	 * @param string $name Name of the campagin. e.g. spring promo
	 * @param string $medium Medium of the campagin. e.g. email / cpc
	 * @param string $source Source of the campagin. e.g. newsletter name
	 * @param string $content Used to differentiate ads or links that point to the same URL. e.g textlink / logolink
	 */
	public static function linkCampaign($url, $name, $medium, $source, $content)
	{
		return $url.'?utm_name='.$name.'&utm_medium='.$medium.'&utm_source='.$source.'&utm_content='.$content;
	}

	/**
	 * Go-To action that will be displayed in some email clients
	 * @param string $url URL to navigate to when user executes the action.
	 * @param string $name A user visible name that is shown in the user interface associated with the action.
	 * @param string $description Snippet of text describing the contents of the email.
	 */
	public static function textViewAction($url, $name, $description)
	{
		$html = '
		<div itemscope itemtype="http://schema.org/EmailMessage">
			<div itemprop="action" itemscope itemtype="http://schema.org/ViewAction">
				<link itemprop="url" href="'.$url.'"/>
				<meta itemprop="name" content="'.$name.'"/>
			</div>
			<meta itemprop="description" content="'.$description.'"/>
		</div>';

		return preg_replace("/\s+/", " ", $html);
	}
}