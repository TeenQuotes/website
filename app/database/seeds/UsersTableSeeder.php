<?php
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Users table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0');
		User::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');

		Eloquent::unguard();
		$faker = Faker::create();

		$this->command->info('Seeding Users table using Faker...');
		foreach(range(1, 100) as $index)
		{
			// Random user
			if ($index != 42) {
				User::create([
					'login'                      => $faker->bothify('?????##'),
					'password'                   => Hash::make("1234"),
					'email'                      => $faker->email,
					'ip'                         => $faker->ipv4,
					'birthdate'                  => $faker->date('Y-m-d', 'now'),
					'gender'                     => $faker->randomElement(array('M', 'F')),
					'country'                    => $faker->numberBetween(1, 237),
					'city'                       => $faker->city,
					'avatar'                     => NULL,
					'about_me'                   => $faker->paragraph(3),
					'hide_profile'               => $faker->numberBetween(0, 1),
					'notification_comment_quote' => $faker->numberBetween(0, 1),
					'last_visit'                 => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
				]);
			}
			// Admin account
			else {
				User::create([
					'login'                      => 'antoineaugusti',
					'password'                   => Hash::make("123456"),
					'email'                      => 'antoine.augusti@gmail.com',
					'security_level'             => 1,
					'ip'                         => $faker->ipv4,
					'birthdate'                  => $faker->date('Y-m-d', 'now'),
					'gender'                     => 'M',
					'country'                    => $faker->numberBetween(1, 237),
					'city'                       => $faker->city,
					'avatar'                     => '42.png',
					'about_me'                   => $faker->paragraph(3),
					'hide_profile'               => 0,
					'notification_comment_quote' => 1,
					'last_visit'                 => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
				]);
				$this->command->info('Admin account : #42 - antoineaugusti - 123456');
			}
		}
	}
}