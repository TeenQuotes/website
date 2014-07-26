<?php
use Faker\Factory as Faker;

class SettingsTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Settings table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Setting::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');

		Eloquent::unguard();
		$faker = Faker::create();

		$this->command->info('Seeding Settings table using Faker...');
		foreach(range(1, 100) as $userID)
		{
			// Colors for published quotes for each user
			Setting::create([
				'user_id' => $userID,
				'key'     => 'colorsQuotesPublished',
				'value'   => $faker->randomElement(['blue', 'green', 'purple', 'red', 'orange'])
			]);
		}
	}
}