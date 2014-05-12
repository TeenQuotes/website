<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

class SendNewsletterCommand extends ScheduledCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'newsletter:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send a newsletter to the subscribed users.';

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
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{
		return [
			$scheduler
				->args(['daily'])
				->daily()
				->hours(12)
				->minutes(0),

			$scheduler
				->args(['weekly'])
				->daysOfTheWeek([
					Scheduler::MONDAY])
				->hours(12)
				->minutes(15),
		];
	}

	/**
	 * Choose the environment(s) where the command should run
	 * @return array Array of environments' name
	 */
	public function environment()
	{
		return ['production'];
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$type = $this->argument('type');
		if (is_null($type) OR !in_array($type, ['daily', 'weekly']))
			$this->error('Wrong type for the newsletter !');

		// Get the number of quotes to publish
		$nbQuotes = is_null($this->argument('nb_quotes')) ? Config::get('app.newsletters.nbQuotesToSend'.ucfirst($type)) : $this->argument('nb_quotes');

		// Get the quotes that will be published today
		if ($type == 'weekly') {
			$quotes = Quote::published()
						->with('user')
						->random()
						->take($nbQuotes)
						->get();
		}
		else {
			$quotes = Quote::published()
						->updatedToday()
						->random()
						->with('user')
						->take($nbQuotes)
						->get();
		}
		$quotesArray = $quotes->toArray();

		// Send the newsletter only if we have
		// at least 1 quote
		if (count($quotesArray) > 0) {

			// Get users that are subscribed to the newsletter
			$rowNewsletters = Newsletter::whereType($type)->with('user')->get();
			$rowNewsletters->each(function($newsletter) use($quotesArray, $type)
			{
				// Log this info
				$this->info("Send ".$type." newsletter to ".$newsletter->user->login." - ".$newsletter->user->email);
				Log::info("Send ".$type." newsletter to ".$newsletter->user->login." - ".$newsletter->user->email);

				$data = array();
				$data['newsletter'] = $newsletter->toArray();
				$data['quotes']     = $quotesArray;

				// Send the email to the user
				Mail::send('emails.newsletters.'.$type, $data, function($m) use($newsletter, $type)
				{
					$m->to($newsletter->user->email, $newsletter->user->login)->subject(Lang::get('newsletters.'.$type.'SubjectEmail'));
				});
			});
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('type', InputArgument::REQUIRED, 'The type of newsletter we will send. weekly|daily'),
			array('nb_quotes', InputArgument::OPTIONAL, 'The number of quotes to send.'),
		);
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
