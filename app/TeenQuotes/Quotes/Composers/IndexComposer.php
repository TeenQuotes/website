<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Quotes\Composers;

use Auth;
use Input;
use JavaScript;
use Lang;
use TeenQuotes\Quotes\Models\Quote;
use TeenQuotes\Tools\Composers\Interfaces\QuotesColorsExtractor;
use URL;

class IndexComposer implements QuotesColorsExtractor
{
    private static $shouldDisplaySharingPromotion = null;

    /**
     * Add data to the view.
     *
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data = $view->getData();

        // The AdBlock disclaimer
        JavaScript::put([
            'moneyDisclaimer' => Lang::get('quotes.adblockDisclaimer'),
        ]);

        // Build the associative array #quote->id => "color"
        // and store it in session
        $view->with('colors', $this->extractAndStoreColors($data['quotes']));

        // If we have an available promotion, display it
        $shouldDisplayPromotion = $this->shouldDisplayPromotion();
        $view->with('shouldDisplayPromotion', $shouldDisplayPromotion);
        if ($shouldDisplayPromotion) {
            $view = $this->addPromotionToData($view);
        }
    }

    private function addPromotionToData($view)
    {
        $data = $this->getDataPromotion();

        foreach ($data as $key => $value) {
            $view->with($key, $value);
        }

        return $view;
    }

    public function extractAndStoreColors($quotes)
    {
        $colors = Quote::storeQuotesColors($quotes->lists('id'));

        return $colors;
    }

    private function getDataPromotion()
    {
        if ($this->shouldDisplaySignupPromotion()) {
            return $this->getDataSignupPromotion();
        }

        if ($this->shouldDisplaySharingPromotion()) {
            return $this->getDataSharingPromotion();
        }
    }

    private function getDataSharingPromotion()
    {
        return [
            'promotionTitle' => Lang::get('quotes.sharePromotionTitle'),
            'promotionText'  => Lang::get('quotes.sharePromotion'),
            'promotionIcon'  => 'fa-heart-o',
        ];
    }

    private function getDataSignupPromotion()
    {
        return [
            'promotionTitle' => Lang::get('quotes.signupPromotionTitle'),
            'promotionText'  => Lang::get('quotes.signupPromotion', ['url' => URL::route('signup')]),
            'promotionIcon'  => 'fa-smile-o',
        ];
    }

    private function shouldDisplaySharingPromotion()
    {
        if (is_null(self::$shouldDisplaySharingPromotion)) {
            self::$shouldDisplaySharingPromotion = (rand(1, 100) == 42);
        }

        return self::$shouldDisplaySharingPromotion;
    }

    private function shouldDisplaySignupPromotion()
    {
        return Auth::guest() and Input::get('page') >= 2;
    }

    private function shouldDisplayPromotion()
    {
        return $this->shouldDisplaySharingPromotion() or $this->shouldDisplaySignupPromotion();
    }
}
