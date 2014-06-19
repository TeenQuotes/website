<?php

class CommentsAPIv1Controller extends BaseController {

	public function index()
	{
		$page = Input::get('page', 1);
		$pagesize = Input::get('pagesize', Config::get('app.comments.nbCommentsPerPage'));

        if ($page <= 0)
			$page = 1;

		// Number of comments to skip
        $skip = $pagesize * ($page - 1);

		$totalComments = Comment::count();

        // Get comments
        $contentQuery = Comment::
			withSmallUser()
			->orderDescending();

		if (Input::has('quote'))
			$contentQuery = $contentQuery->with('quote');

		$content = $contentQuery
			->take($pagesize)
			->skip($skip)
			->get();

		// Handle no comments found
		if (is_null($content) OR $content->count() == 0) {
			$data = [
				'status' => 404,
				'error' => 'No comments have been found.'
			];

			return Response::json($data, 404);
		}

		$data = APIGlobalController::paginateContent($page, $pagesize, $totalComments, $content, 'comments');
		
		return Response::json($data, 200, [], JSON_NUMERIC_CHECK);
	}

	public function show($comment_id)
	{
		$commentQuery = Comment::where('id', '=', $comment_id)
			->withSmallUser();
		
		if (Input::has('quote'))
			$commentQuery = $commentQuery->with('quote');
		
		$comment = $commentQuery->first();

		// Handle not found
		if (is_null($comment)) {

			$data = [
				'status' => 'comment_not_found',
				'error'  => "The comment #".$comment_id." was not found",
			];

			return Response::json($data, 404);
		}
		else
			return Response::json($comment, 200, [], JSON_NUMERIC_CHECK);
	}
}