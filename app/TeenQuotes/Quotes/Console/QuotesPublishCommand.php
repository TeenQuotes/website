<?php

namespace TeenQuotes\Quotes\Console;

use Config;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Log;
use Symfony\Component\Console\Input\InputArgument;
use TeenQuotes\Mail\UserMailer;
use TeenQuotes\Notifiers\AdminNotifier;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Quotes\Repositories\QuoteRepository;

class QuotesPublishCommand extends ScheduledCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'quotes:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish quotes for today.';

    /**
     * Users that have published quotes.
     *
     * @var array
     */
    protected $users = [];

    /**
     * Effective number of quotes published today.
     *
     * @var int
     */
    protected $nbQuotesPublished = 0;

    /**
     * @var \TeenQuotes\Quotes\Repositories\QuoteRepository
     */
    private $quoteRepo;

    /**
     * @var \TeenQuotes\Mail\UserMailer
     */
    private $userMailer;

    /**
     * @var \TeenQuotes\Notifiers\AdminNotifier
     */
    private $adminNotifier;

    /**
     * Create a new command instance.
     */
    public function __construct(QuoteRepository $quoteRepo, UserMailer $userMailer,
                                AdminNotifier $adminNotifier)
    {
        parent::__construct();

        $this->quoteRepo     = $quoteRepo;
        $this->userMailer    = $userMailer;
        $this->adminNotifier = $adminNotifier;
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
            ->minutes(0);
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

    private function getNbQuotesArgument()
    {
        if (is_null($this->argument('nb_quotes'))) {
            return Config::get('app.quotes.nbQuotesToPublishPerDay');
        }

        return $this->argument('nb_quotes');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        // Get the quotes that will be published today
        $quotes = $this->quoteRepo->lastPendingQuotes($this->getNbQuotesArgument());

        // Remember the effective number of published quotes
        $this->nbQuotesPublished = $quotes->count();

        $quotes->each(function ($quote) {
            // Save the quote in storage
            $this->quoteRepo->updateApproved($quote->id, Quote::PUBLISHED);

            $this->users[] = $quote->user;

            // Log this info
            $this->log('Published quote #'.$quote->id);

            // Send an email to the author
            $this->userMailer->tellQuoteWasPublished($quote);
        });

        // Notify the administrator about the remaining
        // number of days to published queued quotes
        $this->notifyAdministrator();
    }

    private function notifyAdministrator()
    {
        $nbDays = $this->getNbRemainingDaysPublication();

        $message = 'Number of days with quotes waiting to be published: '.$nbDays;

        $this->adminNotifier->notify($message);
    }

    /**
     * Compute the number of days required to publish
     * quotes waiting to be published.
     *
     * @return int
     */
    private function getNbRemainingDaysPublication()
    {
        $nbQuotesPending = $this->quoteRepo->nbPending();
        $quotesPerDay = Config::get('app.quotes.nbQuotesToPublishPerDay');

        return ceil($nbQuotesPending / $quotesPerDay);
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
        return [
            ['nb_quotes', InputArgument::OPTIONAL, 'The number of quotes to publish.'],
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
