<?php

namespace TeenQuotes\Quotes\Controllers;

use App;
use Auth;
use BaseController;
use Laracasts\Validation\FormValidationException;
use Request;
use Response;

class FavoriteQuoteController extends BaseController
{
    /**
     * The API controller.
     *
     * @var \TeenQuotes\Api\V1\Controllers\QuotesFavoriteController
     */
    private $api;

    /**
     * @var \TeenQuotes\Quotes\Validation\FavoriteQuoteValidator
     */
    private $favQuoteValidator;

    public function __construct()
    {
        $this->api = App::make('TeenQuotes\Api\V1\Controllers\QuotesFavoriteController');
        $this->favQuoteValidator = App::make('TeenQuotes\Quotes\Validation\FavoriteQuoteValidator');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Response
     */
    public function store($quote_id)
    {
        if (Request::ajax()) {
            // Call the API to store the favorite
            $response = $this->api->postFavorite($quote_id);

            return Response::json(['success' => ($response->getStatusCode() == 201)], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Response
     */
    public function destroy($quote_id)
    {
        if (Request::ajax()) {
            $user = Auth::user();
            $data = [
                'quote_id' => $quote_id,
                'user_id'  => $user->id,
            ];

            try {
                $this->favQuoteValidator->validateRemove($data);
            } catch (FormValidationException $e) {
                return Response::json([
                    'success' => false,
                    'errors'  => $e->getErrors(),
                ]);
            }

            // Call the API to delete the favorite
            $response = $this->api->deleteFavorite($quote_id, false);

            if ($response->getStatusCode() == 200) {
                return Response::json(['success' => true], 200);
            }
        }
    }
}
