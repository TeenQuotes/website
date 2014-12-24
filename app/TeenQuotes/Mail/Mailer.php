<?php
namespace TeenQuotes\Mail;

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class Mailer extends \Illuminate\Mail\Mailer {

	/**
	 * Render the given view.
	 *
	 * @param  string  $view
	 * @param  array   $data
	 * @return \Illuminate\View\View
	 */
	protected function getView($view, $data)
	{
		$cssInline = new CssToInlineStyles();

		$view = $this->views->make($view, $data)->render();

		// Inline CSS
		$cssInline->setUseInlineStylesBlock();
		$cssInline->setStripOriginalStyleTags();
		$cssInline->setCleanup();
		$cssInline->setHTML($view);

		return $cssInline->convert();
	}
}