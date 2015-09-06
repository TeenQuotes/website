<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Newsletters\Console;

use Config;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Lang;
use Symfony\Component\Console\Input\InputArgument;
use TeenQuotes\Newsletters\NewsletterList;
use TeenQuotes\Quotes\Repositories\QuoteRepository;

class SendNewsletterCommand extends ScheduledCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'newsletter:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a newsletter to the subscribed users.';

    /**
     * Allowed event types.
     *
     * @var array
     */
    private $possibleTypes = ['daily', 'weekly'];

    /**
     * @var QuoteRepository
     */
    private $quoteRepo;

    /**
     * @var NewsletterList
     */
    private $newsletterList;

    /**
     * @param QuoteRepository $quoteRepo
     * @param NewsletterList  $newsletterList
     */
    public function __construct(QuoteRepository $quoteRepo, NewsletterList $newsletterList)
    {
        parent::__construct();

        $this->quoteRepo      = $quoteRepo;
        $this->newsletterList = $newsletterList;
    }

    /**
     * When a command should run.
     *
     * @param Schedulable
     *
     * @return array
     */
    public function schedule(Schedulable $scheduler)
    {
        return [
            $scheduler
                ->args(['daily'])
                ->daily()
                ->hours(12)
                ->minutes(0),

            $scheduler
                ->args(['weekly'])
                ->daysOfTheWeek([
                    Scheduler::MONDAY, ])
                ->hours(12)
                ->minutes(15),
        ];
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
     */
    public function fire()
    {
        if ($this->eventTypeIsValid()) {
            $type = $this->getType();

            $quotes = ($type == 'weekly') ? $this->retrieveWeeklyQuotes() : $this->retrieveDailyQuotes();

            // Send the newsletter only if we have at least 1 quote
            if (!$quotes->isEmpty()) {
                $this->newsletterList->sendCampaign(
                    $type.'Newsletter',
                    Lang::get('newsletters.'.$type.'SubjectEmail'),
                    'Teen Quotes\' member',
                    'emails.newsletters.'.$type,
                    compact('quotes')
                );
            }
        }
    }

    /**
     * Get some random published quotes.
     *
     * @return \Illuminate\Support\Collection
     */
    private function retrieveWeeklyQuotes()
    {
        return $this->quoteRepo->randomPublished($this->getNbQuotes());
    }

    /**
     * Get some quotes that were published today.
     *
     * @return \Illuminate\Support\Collection
     */
    private function retrieveDailyQuotes()
    {
        return $this->quoteRepo->randomPublishedToday($this->getNbQuotes());
    }

    /**
     * Get the type given when the command was launched.
     *
     * @return string
     */
    private function getType()
    {
        return $this->argument('type');
    }

    /**
     * Get the number of quotes to send.
     *
     * @return int
     */
    private function getNbQuotes()
    {
        $type = $this->getType();

        // Get the number of quotes to publish
        $nbQuotes = is_null($this->argument('nb_quotes')) ? Config::get('app.newsletters.nbQuotesToSend'.ucfirst($type)) : $this->argument('nb_quotes');

        return $nbQuotes;
    }

    private function eventTypeIsValid()
    {
        $type = $this->getType();

        if (is_null($type) or !in_array($type, $this->possibleTypes)) {
            $this->error('Wrong type for the newsletter! Can only be '.$this->presentPossibleTypes().'. '.$type.' was given.');

            return false;
        }

        return true;
    }

    private function presentPossibleTypes()
    {
        return implode('|', $this->possibleTypes);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['type', InputArgument::REQUIRED, 'The type of newsletter we will send. '.$this->presentPossibleTypes()],
            ['nb_quotes', InputArgument::OPTIONAL, 'The number of quotes to send.'],
        ];
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
