<?php

namespace TeenQuotes\AdminPanel\Composers;

use Config;
use JavaScript;
use Lang;
use TeenQuotes\Tools\Colors\ColorGeneratorInterface;

class ModerationIndexComposer
{
    /**
     * @var ColorGeneratorInterface
     */
    private $colorGenerator;

    public function __construct(ColorGeneratorInterface $colorGenerator)
    {
        $this->colorGenerator = $colorGenerator;
    }

    public function compose($view)
    {
        $data = $view->getData();

        // The number of days required to publish waiting quotes
        $nbDays = $this->getNbdaysToPublishQuotes($data['nbQuotesPending'], $data['nbQuotesPerDay']);
        $view->with('nbDays', $nbDays);

        // The page title
        $view->with('pageTitle', 'Admin | '.Lang::get('layout.nameWebsite'));

        // The color generator
        $view->with('colorGenerator', $this->colorGenerator);

        // Useful JS variables
        JavaScript::put([
            'nbQuotesPerDay' => Config::get('app.quotes.nbQuotesToPublishPerDay'),
            'quotesPlural'   => Lang::choice('quotes.quotesText', 2),
            'daysPlural'     => Lang::choice('quotes.daysText', 2),
        ]);
    }

    /**
     * Compute the number of days required to publish the current waiting number of quotes.
     *
     * @param int $nbPending
     * @param int $nbPublishedPerDay
     *
     * @return int
     */
    private function getNbdaysToPublishQuotes($nbPending, $nbPublishedPerDay)
    {
        return ceil($nbPending / $nbPublishedPerDay);
    }
}
