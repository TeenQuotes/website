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
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tags\Models\Tag;

class TagsTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Deleting existing Tags table ...');
        Tag::truncate();

        $faker = Faker::create();

        $this->command->info('Seeding Tags table using Faker...');
        foreach ($faker->words(15) as $tagName) {
            Tag::create([
                'name' => $tagName,
            ]);
        }

        $this->command->info('Associating tags for quotes...');
        DB::table('quote_tag')->truncate();

        foreach (range(500, 700) as $quote_id) {
            $q = Quote::find($quote_id);

            for ($i = 1; $i <= $faker->numberBetween(0, 3); $i++) {
                $q->tags()->attach($faker->numberBetween(1, 15));
            }
        }
    }
}
