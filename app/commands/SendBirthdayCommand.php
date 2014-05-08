<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendBirthdayCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'birthday:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Whish happy birthday to the concerned users.';

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
		$users = User::birthdayToday()->get();

		$users->each(function($user)
		{
			// Log this info
			$this->info("Wishing happy birthday to ".$user->login." - ".$user->email);
			Log::info("Wishing happy birthday to ".$user->login." - ".$user->email);

			$data = array();
			$data['user'] = $user->toArray();

			// Send the email to the user
			Mail::send('emails.birthday', $data, function($m) use($user)
			{
				$m->to($user->email, $user->login)->subject(Lang::get('email.happyBirthdaySubjectEmail'));
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
		);
	}
}
