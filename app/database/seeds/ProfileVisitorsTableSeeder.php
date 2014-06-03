<?php
use Faker\Factory as Faker;

class ProfileVisitorsTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing ProfileVisitor table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		ProfileVisitor::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');

		Eloquent::unguard();
		$faker = Faker::create();

		$this->command->info('Seeding ProfileVisitor table using Faker...');
		foreach(range(1, 400) as $index)
		{
			ProfileVisitor::create([
				'user_id'    => $faker->numberBetween(1, 100),
				'visitor_id' => $faker->numberBetween(1, 100),
			]);
		}
	}

}