<?php

use Faker\Factory as Faker;
use TeenQuotes\Tags\Models\Tag;
use TeenQuotes\Quotes\Models\Quote;

class TagsTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Tags table ...');
		Tag::truncate();

		$faker = Faker::create();

		$this->command->info('Seeding Tags table using Faker...');
		foreach(range(1, 15) as $index)
		{
			Tag::create([
				'name' => $faker->word
			]);
		}

		$this->command->info('Associating tags for quotes...');
		DB::table('quote_tag')->truncate();

		foreach (range(500, 700) as $quote_id)
		{
			$q = Quote::find($quote_id);

			for ($i = 1; $i <= $faker->numberBetween(0, 3); $i++)
			{
				$q->tags()->attach($faker->numberBetween(1, 15));
			}
		}
	}
}