<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Faker\Factory as Faker;
use TeenQuotes\Users\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Deleting existing Users table ...');
        User::truncate();

        $faker = Faker::create();

        $this->command->info('Seeding Users table using Faker...');
        foreach (range(1, 100) as $index) {
            // Random user
            if ($index != 42) {
                User::create([
                    'login'                      => $faker->bothify('?????##'),
                    'password'                   => '1234',
                    'email'                      => $faker->email,
                    'ip'                         => $faker->ipv4,
                    'birthdate'                  => $faker->date('Y-m-d', 'now'),
                    'gender'                     => $faker->randomElement(['M', 'F']),
                    'country'                    => $faker->numberBetween(1, 237),
                    'city'                       => $faker->city,
                    'avatar'                     => null,
                    'about_me'                   => $faker->paragraph(3),
                    // Profile not hidden at 80 %
                    'hide_profile'               => ($faker->numberBetween(1, 100) >= 80) ? 1 : 0,
                    'notification_comment_quote' => $faker->numberBetween(0, 1),
                    'last_visit'                 => $faker->dateTimeThisYear()->format('Y-m-d H:i:s'),
                ]);
            }
            // Admin account
            else {
                User::create([
                    'login'                      => 'antoineaugusti',
                    'password'                   => '123456',
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
