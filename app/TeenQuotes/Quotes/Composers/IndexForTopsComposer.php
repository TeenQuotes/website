<?php

namespace TeenQuotes\Quotes\Composers;

use InvalidArgumentException;

class IndexForTopsComposer extends IndexComposer
{
    public function compose($view)
    {
        // Add stuff related to tops
        $view->with('possibleTopTypes', $this->getPossibleTopTypes());
        $view = $this->buildIconsForTops($view);

        // Delegate the difficult stuff to the parent
        parent::compose($view);
    }

    private function buildIconsForTops($view)
    {
        foreach ($this->getPossibleTopTypes() as $topType) {
            $view->with('iconForTop'.ucfirst($topType), $this->getIconForTopType($topType));
        }

        return $view;
    }

    private function getIconForTopType($topType)
    {
        switch ($topType) {
            case 'favorites':
                return 'fa-heart';

            case 'comments':
                return 'fa-comments';
        }

        $message = "Can't find icon for top type: ".$topType;

        throw new InvalidArgumentException($message);
    }

    private function getPossibleTopTypes()
    {
        return ['favorites', 'comments'];
    }
}
