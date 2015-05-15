<?php

use Faker\Factory as Faker;
use TeenQuotes\Comments\Models\Comment;

class CommentsTableSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Deleting existing Comments table ...');
        Comment::truncate();

        $faker = Faker::create();

        $this->command->info('Seeding Comments table using Faker...');
        foreach (range(1, 1500) as $index) {
            Comment::create([
                'content'    => $faker->paragraph(3),
                'quote_id'   => $faker->numberBetween(150, 750),
                'user_id'    => $faker->numberBetween(1, 100),
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
            ]);
        }
    }
}
