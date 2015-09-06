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
use TeenQuotes\Newsletters\Models\Newsletter;

class NewslettersTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Deleting existing Newsletter table ...');
        Newsletter::truncate();

        $faker = Faker::create();

        $this->command->info('Seeding Newsletter table using Faker...');
        foreach (range(1, 80) as $index) {
            Newsletter::create([
                'user_id' => $faker->numberBetween(1, 100),
                'type'    => $faker->randomElement(Newsletter::getPossibleTypes()),
            ]);
        }
    }
}
