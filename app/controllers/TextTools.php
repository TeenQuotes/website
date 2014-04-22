<?php

class TextTools {
	
	/**
	 * Used to displays HTML in a form when an error occurs
	 * @param string $text The text to display
	 * @return string The HTML corresponding to the warning
	 */
	public static function warningTextForm($text)
	{
		echo '
		<div class="error-form animated shake">
					<i class="fa fa-warning red"></i>'.$text.'
		</div>';
	}
}