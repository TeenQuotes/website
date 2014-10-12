<?php

use Faker\Factory as Faker;
use TeenQuotes\Stories\Models\Story;

class StoriesTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Stories table ...');
		Story::truncate();

		$faker = Faker::create();

		$this->command->info('Seeding Stories table using Faker...');
		foreach(range(1, 10) as $index)
		{
			Story::create([
				'represent_txt' => $faker->paragraph(5),
				'frequence_txt' => $faker->paragraph(5),
				'user_id'       => $faker->numberBetween(1, 100),
				'created_at'    => $faker->dateTimeBetween('-2 years', 'now'),
			]);
		}
	}

}