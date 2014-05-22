<?php

class APIv1Controller extends BaseController {

	public function getSingleQuote($quote_id)
	{
		$quote = Quote::whereId($quote_id)
		->with('comments')
		->with(array('comments.user' => function($query)
		{
		    $query->addSelect(array('id', 'login', 'avatar'));
		}))
		->with(array('user' => function($q)
		{
		    $q->addSelect(array('id', 'login'));
		}))
		->get();

		// Handle not found
		if (empty($quote) OR $quote->count() == 0) {

			$data = [
				'status' => 404,
				'error' => 'Quote not found.'
			];

			return Response::json($data, 404);
		}
		else
			return $quote;
	}
}