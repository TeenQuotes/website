<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WeeklyNewsletterCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'newsletter:weekly';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send the weekly newsletter.';

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
		$nbQuotes = is_null($this->argument('nb_quotes')) ? Config::get('app.newsletters.nbQuotesToSendWeekly') : $this->argument('nb_quotes');

		// Get the quotes that will be published today
		$quotes = Quote::published()
					->with('user')
					->random()
					->take($nbQuotes)
					->get();
		$quotesArray = $quotes->toArray();

		// Get users that are subscribed to the weekly newsletter
		$rowNewsletters = Newsletter::whereType('weekly')->with('user')->get();
		$rowNewsletters->each(function($newsletter) use($quotesArray)
		{
			// Log this info
			$this->info("Send weekly newsletter to ".$newsletter->user->login." - ".$newsletter->user->email);
			Log::info("Send weekly newsletter to ".$newsletter->user->login." - ".$newsletter->user->email);

			$data = array();
			$data['newsletter'] = $newsletter->toArray();
			$data['quotes']     = $quotesArray;

			// Send the email to the user
			Mail::send('emails.newsletters.weekly', $data, function($m) use($newsletter)
			{
				$m->to($newsletter->user->email, $newsletter->user->login)->subject(Lang::get('newsletters.weeklySubjectEmail'));
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
