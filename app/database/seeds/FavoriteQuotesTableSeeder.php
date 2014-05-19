<?php
use Faker\Factory as Faker;

class FavoriteQuotesTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing FavoriteQuote table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		FavoriteQuote::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');

		Eloquent::unguard();
		$faker = Faker::create();

		$this->command->info('Seeding FavoriteQuote table using Faker...');
		$i = 1;
		foreach(range(1, 2000) as $index)
		{
			FavoriteQuote::create([
				'quote_id' => $faker->randomNumber(150, 750),
				'user_id'  => $faker->randomNumber(1, 100),
			]);

			$i++;
		}
	}

}