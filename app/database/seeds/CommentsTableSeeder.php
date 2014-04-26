<?php
use Faker\Factory as Faker;

class CommentsTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Comments table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0'); 
		Comment::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 

		Eloquent::unguard();
		$faker = Faker::create();

		$this->command->info('Seeding Comments table using Faker...');
		$date = Carbon::createFromDate(2011, 12, 1);
		foreach(range(1, 400) as $index)
		{
			Comment::create([
				'content' => $faker->paragraph(3),
				'quote_id' => $faker->randomNumber(150, 250),
				'user_id' => $faker->randomNumber(1, 100),
				'created_at' => $date,
			]);

			$date = $date->addDay();
		}
	}

}