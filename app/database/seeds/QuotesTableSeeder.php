<?php
use Faker\Factory as Faker;

class QuotesTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Quotes table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0'); 
		Quote::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 

		Eloquent::unguard();
		$faker = Faker::create();

		$this->command->info('Seeding Quotes table using Faker...');
		$i = 1;
		$date = Carbon::createFromDate(2011, 12, 1);
		foreach(range(1, 750) as $index)
		{
			// Generate 50 quotes for each approved value
			// between -1 and 2
			if ($i < 50)
				$approved = -1;
			else {
				if ($i < 100)
					$approved = 0;
				else {
					if ($i < 150)
						$approved = 1;
					else
						$approved = 2;
				}
			}

			Quote::create([
				'content' => $faker->paragraph(3),
				'user_id' => $faker->randomNumber(1, 100),
				'approved' => $approved ,
				'created_at' => $date,
			]);

			$date = $date->addDay();
			$i++;
		}
	}

}