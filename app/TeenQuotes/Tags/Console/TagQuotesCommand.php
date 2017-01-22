<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Tags\Console;

use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Log;
use TeenQuotes\Tags\Repositories\TagRepository;

class TagQuotesCommand extends ScheduledCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'quotes:tag';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tag quotes that are not tagged yet.';

    /**
     * @var \TeenQuotes\Tags\Repositories\TagRepository
     */
    private $tagRepo;

    /**
     * Create a new command instance.
     */
    public function __construct(TagRepository $tagRepo)
    {
        parent::__construct();

        $this->tagRepo = $tagRepo;
    }

    /**
     * When a command should run.
     *
     * @param  \Indatus\Dispatcher\Scheduling\Schedulable
     *
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
    public function schedule(Schedulable $scheduler)
    {
        return $scheduler
            ->daily()
            ->hours(11)
            ->minutes(10);
    }

    /**
     * Choose the environment(s) where the command should run.
     *
     * @return array Array of environments' name
     */
    public function environment()
    {
        return ['production'];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        foreach ($this->tagRepo->allTags() as $tag) {
            foreach ($this->tagRepo->quotesToTag($tag) as $quote) {
                $this->log('Tagging quote #'.$quote->id.' with tag '.$tag->name);
                $this->tagRepo->tagQuote($quote, $tag);
            }
        }
    }

    private function log($string)
    {
        $this->info($string);
        Log::info($string);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
