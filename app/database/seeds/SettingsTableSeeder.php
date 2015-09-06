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
use TeenQuotes\Settings\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Deleting existing Settings table ...');
        Setting::truncate();

        $faker = Faker::create();

        $this->command->info('Seeding Settings table using Faker...');
        foreach (range(1, 100) as $userID) {
            // Colors for published quotes for each user
            Setting::create([
                'user_id' => $userID,
                'key'     => 'colorsQuotesPublished',
                'value'   => $faker->randomElement(['blue', 'green', 'purple', 'red', 'orange']),
            ]);
        }
    }
}
