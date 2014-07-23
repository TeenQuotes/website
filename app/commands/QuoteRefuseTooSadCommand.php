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
	protected $name = 'quote:learningTooSad';

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
		$bestTreshold = $thresholds[0];
		$minPercentageOfWrongClassification = INF;
		
		$quotes = Quote::all();
		$numberOfQuotes = count($quotes);

		$this->info('Analyzing '.$numberOfQuotes.' quotes');
		
		foreach ($thresholds as $treshold) {
			
			$tooNegative = 0;
			$wrongNumberClassification = 0;
			
			foreach ($quotes as $quote) {
				
				// If the quote is too negative with enough confidence
				if (SentimentAnalysis::isNegative($quote->content) AND SentimentAnalysis::score($quote->content) >= $treshold) {
					$tooNegative++;
					
					// We found that the quote was too negative but yet it was published
					// Count the wrong classification
					if ($quote->isPublished()) {
						$wrongNumberClassification++;
					}
				}
			}

			// Compute percentage and displays it
			$percentage = $this->getPercentage($wrongNumberClassification, $tooNegative);
			$this->info("Treshold ".$treshold.": ".$tooNegative." quotes with " .$wrongNumberClassification." wrong classifications (".$percentage." %).");

			if ($percentage < $minPercentageOfWrongClassification AND $tooNegative > 0)
				$bestTreshold = $treshold;
		}

		// Display best treshold found
		$this->info('Found best treshold by minimizing percentage: '.$bestTreshold);
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
