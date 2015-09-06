<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeenQuotes\Api\V1\Controllers;

use App;
use Laracasts\Validation\FormValidationException;
use Queue;
use TeenQuotes\Http\Facades\Response;

class QuotesFavoriteController extends APIGlobalController
{
    /**
     * @var \TeenQuotes\Quotes\Validation\FavoriteQuoteValidator
     */
    private $favQuoteValidator;

    public function bootstrap()
    {
        $this->favQuoteValidator = App::make('TeenQuotes\Quotes\Validation\FavoriteQuoteValidator');
    }

    public function postFavorite($quote_id, $doValidation = true)
    {
        $user = $this->retrieveUser();

        if ($doValidation) {
            try {
                $this->favQuoteValidator->validatePostForQuote(compact('quote_id'));
            } catch (FormValidationException $e) {
                return $this->tellQuoteWasNotFound($quote_id);
            }

            // Check if the quote is published
            $quote = $this->quoteRepo->getById($quote_id);

            if (!$quote->isPublished()) {
                return Response::json([
                    'status' => 'quote_not_published',
                    'error'  => 'The quote #'.$quote_id.' is not published.',
                ], 400);
            }

            // Try to find if the user has this quote in favorite from cache
            $alreadyFavorited = $this->favQuoteRepo->isFavoriteForUserAndQuote($user, $quote_id);

            if ($alreadyFavorited) {
                return Response::json([
                    'status' => 'quote_already_favorited',
                    'error'  => 'The quote #'.$quote_id.' was already favorited.',
                ], 400);
            }
        }

        // Store the favorite
        $favorite = $this->favQuoteRepo->create($user, $quote_id);

        // Register in the recommendation system
        $data = [
            'quote_id' => $quote_id,
            'user_id'  => $user->id,
        ];
        Queue::push('TeenQuotes\Queues\Workers\EasyrecWorker@favoriteAQuote', $data);

        return Response::json($favorite, 201, [], JSON_NUMERIC_CHECK);
    }

    public function deleteFavorite($quote_id, $doValidation = true)
    {
        $user = $this->retrieveUser();

        if ($doValidation) {
            try {
                $this->favQuoteValidator->setUserForRemove($user)->validateRemoveForQuote(compact('quote_id'));
            } catch (FormValidationException $e) {
                return $this->tellQuoteWasNotFound($quote_id);
            }
        }

        // Delete the FavoriteQuote from database
        $this->favQuoteRepo->deleteForUserAndQuote($user, $quote_id);

        // Register in the recommendation system
        $data = [
            'quote_id' => $quote_id,
            'user_id'  => $user->id,
        ];
        Queue::push('TeenQuotes\Queues\Workers\EasyrecWorker@unfavoriteAQuote', $data);

        return Response::json([
            'status'  => 'favorite_deleted',
            'success' => 'The quote #'.$quote_id.' was deleted from favorites.',
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * Tell that a quote was not found.
     *
     * @param int $quote_id
     *
     * @return \TeenQuotes\Http\Facades\Response
     */
    private function tellQuoteWasNotFound($quote_id)
    {
        return Response::json([
            'status' => 'quote_not_found',
            'error'  => 'The quote #'.$quote_id.' was not found.',
        ], 400);
    }
}
