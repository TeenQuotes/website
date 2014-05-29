<?php

class APIGlobalController extends BaseController {

	public function showWelcome()
	{
		$data = [
			'status'            => 'You have arrived',
			'message'           => 'Welcome to the Teen Quotes API',
			'version'           => '1.0alpha',
			'url_documentation' => 'https://github.com/TeenQuotes/api-documentation',
			'contact'           => 'antoine.augusti@teen-quotes.com',
		];

		return Response::json($data, 200);
	}

	/**
	 * Paginate content for the API after a search for example
	 * @param  int $page The current page number
	 * @param  int $pagesize The number of items per page
	 * @param  int $totalContent The total number of items for the search
	 * @param  Collection $content The content we searched for
	 * @param  string $contentName The name of the content. Example: quotes|users
	 * @return array A big array
	 */
	public static function paginateContent($page, $pagesize, $totalContent, $content, $contentName = 'quotes')
	{
        $totalPages = ceil($totalContent / $pagesize);
		
		$data = [
			$contentName          => $content->toArray(),
			'total_'.$contentName => $totalContent,
			'total_pages'         => $totalPages,
			'page'                => (int) $page,
			'pagesize'            => (int) $pagesize,
			'url'                 => URL::current()
        ];

        // Add next page URL
        if ($page < $totalPages) {
        	$data['has_next_page'] = true;
        	$data['next_page'] = $data['url'].'?page='.($page + 1).'&pagesize='.$pagesize;
        }
        else
        	$data['has_next_page'] = false;

        // Add previous page URL
        if ($page >= 2) {
        	$data['has_previous_page'] = true;
        	$data['previous_page'] = $data['url'].'?page='.($page - 1).'&pagesize='.$pagesize;
        }
        else
        	$data['has_previous_page'] = false;

        return $data;
	}
}