<?php

namespace TeenQuotes\Comments\Controllers;

use App;
use BaseController;
use Illuminate\Http\Response as ResponseClass;
use Input;
use Lang;
use Redirect;
use Request;
use Response;
use TeenQuotes\Comments\Models\Comment;
use View;

class CommentsController extends BaseController
{
    /**
     * The API controller.
     *
     * @var \TeenQuotes\Api\V1\Controllers\CommentsController
     */
    private $api;

    /**
     * @var \TeenQuotes\Comments\Validation\CommentValidator
     */
    private $commentValidator;

    public function __construct()
    {
        $this->beforeFilter('auth');
        $this->api = App::make('TeenQuotes\Api\V1\Controllers\CommentsController');
        $this->commentValidator = App::make('TeenQuotes\Comments\Validation\CommentValidator');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Response
     */
    public function store()
    {
        $data = Input::only('content', 'quote_id');

        $this->commentValidator->validatePosting($data);

        // Call the API - skip the API validator
        $response = $this->api->store($data['quote_id'], false);
        if ($response->getStatusCode() == 201) {
            return Redirect::route('quotes.show', $data['quote_id'])->with('success', Lang::get('comments.commentAddedSuccessfull'));
        }
    }

    /**
     * Show the form to edit a comment.
     *
     * @param int $id
     *
     * @return \Response
     */
    public function edit($id)
    {
        $comment = $this->api->show($id)->getOriginalData();

        // If the comment was not found or is not posted by the user
        if (is_null($comment) or !$comment->isPostedBySelf()) {
            return Redirect::home()->with('warning', Lang::get('comments.cantEditThisComment'));
        }

        $data = compact('comment');
        $data['pageTitle'] = Lang::get('comments.updateCommentPageTitle');

        return View::make('comments.edit', $data);
    }

    /**
     * Edit a comment.
     *
     * @param int $id
     *
     * @return \Response
     */
    public function update($id)
    {
        $data = Input::only('content', 'quote_id');

        $this->commentValidator->validateEditing($data);

        // Call the API
        $response = $this->api->update($id);
        if ($response->getStatusCode() != 200) {
            return Redirect::route('quotes.show', $data['quote_id'])->with('warning', Lang::get('comments.cantEditThisComment'));
        }

        return Redirect::route('quotes.show', $data['quote_id'])->with('success', Lang::get('comments.commentEditSuccessfull'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Response
     */
    public function destroy($id)
    {
        if (Request::ajax()) {
            $response = $this->api->destroy($id);

            return Response::json([
                'success' => ($response->getStatusCode() == ResponseClass::HTTP_OK),
            ]);
        }
    }
}
