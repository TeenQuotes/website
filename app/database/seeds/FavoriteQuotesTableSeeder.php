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
use TeenQuotes\Quotes\Models\FavoriteQuote;

class FavoriteQuotesTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Deleting existing FavoriteQuote table ...');
        FavoriteQuote::truncate();

        $faker = Faker::create();

        $this->command->info('Seeding FavoriteQuote table using Faker...');
        $i = 1;
        foreach (range(1, 2000) as $index) {
            FavoriteQuote::create([
                'quote_id' => $faker->numberBetween(50, 700),
                'user_id'  => $faker->numberBetween(1, 100),
            ]);

            $i++;
        }
    }
}
