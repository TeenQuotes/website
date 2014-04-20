<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('UsersTableSeeder');
		$this->call('QuotesTableSeeder');
		$this->call('CommentsTableSeeder');
		$this->call('FavoriteQuotesTableSeeder');
		$this->call('ProfileVisitorsTableSeeder');
		$this->call('NewslettersTableSeeder');
		$this->call('StoriesTableSeeder');
	}

}
