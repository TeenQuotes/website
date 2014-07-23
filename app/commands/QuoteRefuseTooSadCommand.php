<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class QuoteRefuseTooSadCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'quotes:learningTooSad';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Try to determine the appropriate treshold that describes when we will refuse too sad quotes.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$thresholds = range(0.5, 0.99, 0.02);
		$wrongClassifications = array_fill_keys($thresholds, 0);
		$foundSadQuotes = $wrongClassifications;
		
		$quotes = Quote::all();
		$numberOfQuotes = count($quotes);

		$this->info('Analyzing '.$numberOfQuotes.' quotes...');
			
		// Process each quote
		foreach ($quotes as $quote) {

			// Display some info to know everything is working fine
			if ($quote->id % 2000 == 0)
				$this->info('Processing quote #'.$quote->id);
			
			// If the quote is too negative with enough confidence
			if (SentimentAnalysis::isNegative($quote->content)) {
				$score = SentimentAnalysis::score($quote->content);

				// Update number of sad quotes found for the appropriate thresholds
				foreach ($foundSadQuotes as $treshold => $value) {
					if ($score >= $treshold)
						$foundSadQuotes[$treshold] = $value + 1;
				}
				
				// We found that the quote was too negative but yet it was published
				// Count the wrong classification
				if ($quote->isPublished()) {
					
					// Update the number of wrong classification for the appropriate thresholds
					foreach ($wrongClassifications as $treshold => $value) {
						if ($score >= $treshold)
							$wrongClassifications[$treshold] = $value + 1;
					}
				}
			}
		}

		// Display the results
		foreach ($wrongClassifications as $treshold => $value) {
			// Both arrays have got the same keys
			$nbQuotes = $foundSadQuotes[$treshold];
			$wrongNbClassifications = $value;
			
			// Compute percentage and display some info
			$percentage = $this->getPercentage($wrongNbClassifications, $nbQuotes);
			$this->info('Threshold '.$treshold.': '.$nbQuotes.' quotes with '.$wrongNbClassifications.' wrong classification ('.$percentage.' %).');
		}
	}

	/**
	 * Returns a percentage with 2 digits
	 * @param  int $value The value
	 * @param  int $total The total number of items
	 * @return float The percentage
	 */
	private function getPercentage($value, $total)
	{
		return round($value / $total * 100, 2);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}
