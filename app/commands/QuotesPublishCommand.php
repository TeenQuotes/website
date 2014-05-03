<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class QuotesPublishCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'quotes:publish';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Publish quotes for today.';

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
		// Get the number of quotes to publish
		$nbQuotes = is_null($this->argument('nb_quotes')) ? Config::get('app.nbQuotesToPublishPerDay') : $this->argument('nb_quotes');

		// Get the quotes that will be published today
		$quotes = Quote::pending()->orderAscending()->take($nbQuotes)->with('user')->get();
		$quotes->each(function($quote)
		{
			// Save the quote in storage
			$quote->approved = 2;
			$quote->save();

			$quoteArray = array('quote' => $quote->toArray());

			// Log this info
			$this->info("Published quote #".$quote->id);
			Log::info("Published quote #".$quote->id, array('quote' => $quoteArray));

			// Send an email to the author
			Mail::send('emails.quoteApproved', $quoteArray, function($m) use($quote)
			{
				$m->to($quote->user->email, $quote->user->login)->subject(Lang::get('quotes.quotePublishedSubjectEmail'));
			});
		});

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('nb_quotes', InputArgument::OPTIONAL, 'The number of quotes to publish.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
