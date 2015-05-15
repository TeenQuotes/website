<?php

namespace TeenQuotes\Quotes\Composers;

use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tools\Composers\Interfaces\QuotesColorsExtractor;

class ResultsComposer implements QuotesColorsExtractor
{
    public function compose($view)
    {
        $data = $view->getData();

        $view->with('colors', $this->extractAndStoreColors($data['quotes']));
    }

    public function extractAndStoreColors($quotes)
    {
        $colors = Quote::storeQuotesColors($quotes->lists('id'));

        return $colors;
    }
}
