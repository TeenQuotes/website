<?php

namespace TeenQuotes\Quotes\Composers;

use JavaScript;
use Lang;
use Session;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tools\Composers\AbstractDeepLinksComposer;

class ShowComposer extends AbstractDeepLinksComposer
{
    public function compose($view)
    {
        $data = $view->getData();

        // The ID of the current quote
        $id = $data['quote']->id;

        // Put some useful variables for the JS
        JavaScript::put([
            'contentShortHint' => Lang::get('comments.contentShortHint'),
            'contentGreatHint' => Lang::get('comments.contentGreatHint'),
        ]);

        // Load colors for the quote
        if (Session::has('colors.quote') and array_key_exists($id, Session::get('colors.quote'))) {
            $colors = Session::get('colors.quote');
        } else {
            // Fall back to the default color
            $colors = [];
            $colors[$id] = 'color-1';
        }

        $view->with('colors', $colors);

        // Deep links
        $view->with('deepLinksArray', $this->createDeepLinks('quotes/'.$id));

        // Perform translation on tags
        $view->with('tagsName', $this->transformTags($data['quote']));
    }

    /**
     * Transform a list of tags for a quote.
     *
     * @param \TeenQuotes\Quotes\Model\Quote $quote
     *
     * @return array A key value array like ['love' => 'Love']
     */
    private function transformTags(Quote $quote)
    {
        $tagsName = [];

        foreach ($quote->tagsList as $tag) {
            $tagsName[$tag] = Lang::get('tags.'.$tag);
        }

        return $tagsName;
    }
}
