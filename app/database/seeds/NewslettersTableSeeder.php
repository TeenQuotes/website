<?php
use Faker\Factory as Faker;

class NewslettersTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Newsletter table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		Newsletter::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');

		Eloquent::unguard();
		$faker = Faker::create();

		$this->command->info('Seeding Newsletter table using Faker...');
		foreach(range(1, 80) as $index)
		{
			Newsletter::create([
				'user_id' => $faker->randomNumber(1, 100),
				'type' => $faker->randomElement(array('weekly', 'daily')),
			]);
		}
	}

}