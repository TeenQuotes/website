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
		foreach(range(1, 1500) as $index)
		{
			Comment::create([
				'content'    => $faker->paragraph(3),
				'quote_id'   => $faker->numberBetween(150, 750),
				'user_id'    => $faker->numberBetween(1, 100),
				'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
			]);
		}
	}

}