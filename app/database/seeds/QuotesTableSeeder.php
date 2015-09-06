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

class QuotesTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Deleting existing Quotes table ...');
        Quote::truncate();

        $faker = Faker::create();

        $this->command->info('Seeding Quotes table using Faker...');
        $i    = 1;
        $date = Carbon::createFromDate(2011, 12, 1);
        foreach (range(1, 750) as $index) {
            // Generate 50 quotes for each approved value
            // between -1 and 2

            Quote::create([
                'content'    => $faker->paragraph(3),
                'user_id'    => $faker->numberBetween(1, 100),
                'approved'   => $this->getApproveForNumber($i),
                'created_at' => $date,
            ]);

            $date = $date->addDay();
            $i++;
        }
    }

    private function getApproveForNumber($i)
    {
        if ($i < 50) {
            $approved = Quote::REFUSED;
        }
        if ($i >= 50 and $i <= 700) {
            $approved = Quote::PUBLISHED;
        }
        if ($i > 700 and $i <= 725) {
            $approved = Quote::PENDING;
        }
        if ($i > 725) {
            $approved = Quote::WAITING;
        }

        return $approved;
    }
}
