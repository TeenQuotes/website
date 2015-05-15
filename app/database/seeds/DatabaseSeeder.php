<?php

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Eloquent::unguard();

        $this->disableForeignKeyChecks();

        // Reset and rerun all migrations
        Artisan::call('migrate:refresh');

        // Do not send any e-mails when creating ressources
        Config::set('mail.pretend', true);

        $this->call('UsersTableSeeder');
        $this->call('QuotesTableSeeder');
        $this->call('CommentsTableSeeder');
        $this->call('FavoriteQuotesTableSeeder');
        $this->call('ProfileVisitorsTableSeeder');
        $this->call('NewslettersTableSeeder');
        $this->call('StoriesTableSeeder');
        $this->call('CountriesTableSeeder');
        $this->call('SettingsTableSeeder');
        $this->call('TagsTableSeeder');

        $this->enableForeignKeyChecks();

        // Flush the cache
        Artisan::call('cache:clear');
    }

    private function disableForeignKeyChecks()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    }

    private function enableForeignKeyChecks()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
