<?php
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Users table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0'); 
		User::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 

		$faker = Faker::create();

		$this->command->info('Seeding Users table using Faker...');
		foreach(range(1, 100) as $index)
		{
			// Random user
			if ($index != 42) {
				User::create([
					'login' => $faker->userName,
					'password' => Hash::make("1234"),
					'email' => $faker->email,
					'ip' => $faker->ipv4,
					'birthdate' => $faker->date('Y-m-d', 'now'),
					'gender' => $faker->randomElement(array('M', 'W')),
					'country' => $faker->country,
					'city' => $faker->city,
					'avatar' => 'icon50.png',
					'about_me' => $faker->paragraph(3),
					'hide_profile' => $faker->randomNumber(0, 1),
					'notification_comment_quote' => $faker->randomNumber(0, 1),
					'last_visit' => $faker->dateTimeThisYear(),
				]);
			}
			// Admin account
			else {
				User::create([
					'login' => 'antoineaugusti',
					'password' => Hash::make("1234"),
					'email' => $faker->email,
					'security_level' => 1,
					'ip' => $faker->ipv4,
					'birthdate' => $faker->date('Y-m-d', 'now'),
					'gender' => 'M',
					'country' => $faker->country,
					'city' => $faker->city,
					'avatar' => 'icon50.png',
					'about_me' => $faker->paragraph(3),
					'hide_profile' => 0,
					'notification_comment_quote' => 1,
					'last_visit' => $faker->dateTimeThisYear(),
				]);
				$this->command->info('Admin account : #42 - mulshan - 1234');
			}
		}

	}

}