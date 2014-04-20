<?php
use Faker\Factory as Faker;

class QuotesTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Quotes table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0'); 
		Quote::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 

		$faker = Faker::create();

		$this->command->info('Seeding Quotes table using Faker...');
		foreach(range(1, 250) as $index)
		{
			Quote::create([
				'content' => $faker->paragraph(3),
				'user_id' => $faker->randomNumber(1, 100),
				'approved' => $faker->randomNumber(1, 3),
				'created_at' => $faker->dateTimeBetween('-2 years', '-1 year'),
			]);
		}
	}

}