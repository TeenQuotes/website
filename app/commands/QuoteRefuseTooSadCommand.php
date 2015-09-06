<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class QuoteRefuseTooSadCommand extends Command
{
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
        // Set lower bound
        $lowerBound = is_null($this->argument('lowerBound')) ? 0.5 : $this->argument('lowerBound');
        if ($lowerBound > 1 or $lowerBound < 0) {
            throw new InvalidArgumentException('Lower bound must be between 0 and 1.', 1);
        }

        // Set upper bound
        $upperBound = is_null($this->argument('upperBound')) ? 0.99 : $this->argument('upperBound');
        if ($upperBound > 1 or $upperBound < 0) {
            throw new InvalidArgumentException('Upper bound must be between 0 and 1.', 1);
        }

        // Set step value for the sequence
        $step = is_null($this->argument('step')) ? 0.02 : $this->argument('step');

        // Build initial arrays with thresholds, initialized at 0
        $thresholds           = range($lowerBound, $upperBound, $step);
        $wrongClassifications = array_fill_keys($thresholds, 0);
        $foundSadQuotes       = $wrongClassifications;

        $confidenceScoresNotPublished = array_fill_keys($thresholds, []);
        $confidenceScoresPublished    = array_fill_keys($thresholds, []);

        $quotes         = Quote::all();
        $numberOfQuotes = count($quotes);

        $this->info('Analyzing '.$numberOfQuotes.' quotes...');

        // Process each quote
        foreach ($quotes as $quote) {

            // Display some info to know everything is working fine
            if ($quote->id % 2000 == 0) {
                $this->info('Processing quote #'.$quote->id);
            }

            // If the quote is too negative with enough confidence
            if (SentimentAnalysis::isNegative($quote->content)) {
                $scores = SentimentAnalysis::scores($quote->content);
                rsort($scores);
                $score         = $scores[0];
                $confidenceGap = $this->computeConfidenceGap($scores);

                // Update number of sad quotes found for the appropriate thresholds
                foreach ($foundSadQuotes as $threshold => $value) {
                    if ($score >= $threshold) {
                        $foundSadQuotes[$threshold] = $value + 1;
                    }
                }

                // Update the number of wrong classification for the appropriate thresholds
                foreach ($wrongClassifications as $threshold => $value) {
                    if ($score >= $threshold) {
                        // We found that the quote was too negative but yet it was published
                        // Count the wrong classification
                        if ($quote->isPublished()) {
                            $wrongClassifications[$threshold] = $value + 1;
                            array_push($confidenceScoresPublished[$threshold], $confidenceGap);
                        } else {
                            array_push($confidenceScoresNotPublished[$threshold], $confidenceGap);
                        }
                    }
                }
            }
        }

        // Display the results
        foreach ($wrongClassifications as $threshold => $value) {
            // Both arrays have got the same keys
            $nbQuotes               = $foundSadQuotes[$threshold];
            $wrongNbClassifications = $value;
            $gapNotPublished        = $this->arrayAverage($confidenceScoresNotPublished[$threshold]);
            $gapPublished           = $this->arrayAverage($confidenceScoresPublished[$threshold]);

            // Compute percentage and display some info
            $percentage = $this->getPercentage($wrongNbClassifications, $nbQuotes);
            $this->info('Threshold '.$threshold.': '.$nbQuotes.' quotes with '.$wrongNbClassifications.' wrong classifications ('.$percentage.' %)');
            $this->info('Average gap for published: '.$gapPublished);
            $this->info('Average gap for not published: '.$gapNotPublished);
        }
    }

    /**
     * Returns a percentage with 2 digits.
     *
     * @param int $value The value
     * @param int $total The total number of items
     *
     * @return float The percentage
     */
    private function getPercentage($value, $total)
    {
        return round($value / $total * 100, 2);
    }

    /**
     * Compute the confidence score for a given classification, that is to say the difference between the max score and the following score.
     *
     * @param array $scores The scores array, must be reversed ordered!
     *
     * @return float The difference for the two max scores, with 2 digits
     */
    private function computeConfidenceGap($scores)
    {
        return round($scores[0] - $scores[1], 2);
    }

    /**
     * Compute the average of an array of values.
     *
     * @param array $array The array
     *
     * @return float The average value with 2 digits
     */
    private function arrayAverage($array)
    {
        if (count($array) === 0) {
            return 0;
        }

        return round(array_sum($array) / count($array), 2);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['lowerBound', InputArgument::OPTIONAL, 'The lower bound for the treshold.'],
            ['upperBound', InputArgument::OPTIONAL, 'The upper bound for the treshold.'],
            ['step', InputArgument::OPTIONAL, 'Increment value between elements in the sequence.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
