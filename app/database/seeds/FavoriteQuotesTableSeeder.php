<?php

use Faker\Factory as Faker;
use TeenQuotes\Quotes\Models\FavoriteQuote;

class FavoriteQuotesTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing FavoriteQuote table ...');
		FavoriteQuote::truncate();

		$faker = Faker::create();

		$this->command->info('Seeding FavoriteQuote table using Faker...');
		$i = 1;
		foreach(range(1, 2000) as $index)
		{
			FavoriteQuote::create([
				'quote_id' => $faker->numberBetween(150, 750),
				'user_id'  => $faker->numberBetween(1, 100),
			]);

			$i++;
		}
	}

}