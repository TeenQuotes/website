<?php

use Faker\Factory as Faker;
use Laracasts\TestDummy\DbTestCase;
use Illuminate\Http\Response;

abstract class ApiTest extends DbTestCase {

	/**
	 * The controller class to use for the current test case
	 * @var mixed
	 */
	protected $controller;
	
	/** 
	 * The response given by a controller
	 * @var Illuminate\Http\Response
	 */
	protected $response;

	/**
	 * The current page of an endpoint of the API
	 * @var int
	 */
	protected $page = null;

	/**
	 * The current pagesize of an endpoint of the API
	 * @var int
	 */
	protected $pagesize = null;

	/**
	 * Tells if an endpoint embed a small user in its response
	 * @var boolean
	 */
	protected $containsSmallUser = false;

	/**
	 * Number of ressources to create
	 * @var integer
	 */
	protected $nbRessources = 3;

	/**
	 * The Faker instance
	 * @var Faker\Factory
	 */
	protected $faker;

	/**
	 * The ID of the logged in user
	 * @var [type]
	 */
	protected $userIdLoggedIn;

	/**
	 * The JSON response in an object format
	 * @var stdClas
	 */
	protected $json;

	public function setUp()
	{
		parent::setUp();
		$this->faker = Faker::create();
	}

	protected function assertResponseIsNotFound()
	{		
		$this->assertStatusCodeIs(Response::HTTP_NOT_FOUND);
		$this->assertObjectHasAttribute('status', $this->json);
		$this->assertObjectHasAttribute('error', $this->json);
	}

	protected function generateString($length)
	{
		return str_repeat("a", $length);
	}

	protected function logUserWithId($id)
	{
		$this->userIdLoggedIn = $id;
		$this->be(User::find($id));
	}

	protected function assertStatusCodeIs($code)
	{
		$this->assertEquals($code, $this->response->getStatusCode());
		
		return $this;
	}

	protected function assertResponseHasAttributes($array)
	{
		$this->assertObjectHasAttributes($this->json, $array);
	}

	protected function assertObjectHasAttributes($object, $array)
	{
		foreach ($array as $value) {
			$this->assertObjectHasAttribute($value, $object);
			if (Str::contains($value, '_id'))
				$this->assertTrue(is_integer($object->$value));
		}
	}

	protected function tryPaginatedContentNotFound()
	{
		$this->page = $this->nbRessources + 1;
		$this->pagesize = $this->nbRessources;
		Input::replace([
			'page' => $this->page,
			'pagesize' => $this->pagesize
		]);

		$this->doRequest('index');

		$this->assertResponseIsNotFound();

		return $this;
	}

	protected function withStatusMessage($status)
	{
		$this->assertResponseKeyIs('status', $status);
		
		return $this;
	}

	protected function withErrorMessage($error)
	{
		$this->assertResponseKeyIs('error', $error);
		
		return $this;
	}

	protected function tryStore($method = 'store')
	{
		$this->doRequest($method);
		
		return $this;
	}

	private function bindJson()
	{
		$content = $this->response->getContent();
		$this->json = json_decode($content);
	}

	protected function doRequest($method)
	{
		$this->response = $this->controller->$method();
		$this->bindJson();
	}

	protected function tryMiddlePage()
	{
		$this->page = max(2, ($this->nbRessources / 2));
		$this->pagesize = 1;
		Input::replace([
			'page' => $this->page,
			'pagesize' => $this->pagesize
		]);

		$this->doRequest('index');
		$this->assertIsPaginatedResponse();
		$this->assertHasNextAndPreviousPage();
		
		$objectName = $this->contentType;
		$objects = $this->json->$objectName;
		if ($this->containsSmallUser)
			$this->assertObjectContainsSmallUser(reset($objects));
		$this->assertObjectHasAttributes(reset($objects), $this->requiredAttributes);

		$this->assertNeighborsPagesMatch();
	}

	protected function getIdNonExistingRessource()
	{
		return $this->nbRessources + 1;
	}

	protected function tryShowNotFound()
	{
		$this->response = $this->controller->show($this->getIdNonExistingRessource());
		$this->bindJson();

		$this->assertResponseIsNotFound();

		return $this;
	}

	protected function assertBelongsToLoggedInUser()
	{
		$this->assertEquals($this->json->user_id, $this->userIdLoggedIn);
	}

	protected function tryShowFound($id)
	{
		$this->response = $this->controller->show($id);
		$this->bindJson();

		if ($this->containsSmallUser)
			$this->assertResponseHasSmallUser();
		$this->assertResponseHasAttributes($this->requiredAttributes);	
	}

	protected function tryFirstPage()
	{
		$this->page = 1;
		$this->pagesize = $this->nbRessources;
		Input::replace([
			'page' => $this->page,
			'pagesize' => $this->pagesize
		]);

		$this->doRequest('index');
		$this->assertIsPaginatedResponse();

		$objectName = $this->contentType;
		$objects = $this->json->$objectName;
		for ($i = 0; $i < $this->nbRessources; $i++) { 
			if ($this->containsSmallUser)
				$this->assertObjectContainsSmallUser($objects[$i]);
			$this->assertObjectHasAttributes($objects[$i], $this->requiredAttributes);
		}

		$this->assertNeighborsPagesMatch();
	}

	protected function assertObjectContainsSmallUser($object)
	{
		return $this->assertObjectIsSmallUser($object->user);
	}

	protected function assertResponseKeyIs($key, $value)
	{
		$this->assertEquals($this->json->$key, $value);
	}

	protected function assertNeighborsPagesMatch()
	{
		$this->checkPagesAreSet();
		
		$nextPage = $this->page + 1;
		$previousPage = $this->page - 1;

		if ($nextPage < $this->computeTotalPages())
			$this->assertTrue(Str::contains($this->json->next_page, 'page='.$nextPage.'&pagesize='.$this->pagesize));
		if ($previousPage >= 1 AND $this->computeTotalPages() > 1)
			$this->assertTrue(Str::contains($this->json->previous_page, 'page='.$previousPage.'&pagesize='.$this->pagesize));
	}

	protected function assertObjectIsSmallUser($object)
	{
		// Assert attributes
		$this->assertObjectHasAttribute('id', $object);
		$this->assertObjectHasAttribute('is_admin', $object);
		$this->assertObjectHasAttribute('login', $object);
		$this->assertObjectHasAttribute('profile_hidden', $object);
		$this->assertObjectHasAttribute('url_avatar', $object);
		$this->assertObjectHasAttribute('wants_notification_comment_quote', $object);
		
		// Assert types
		$this->assertTrue(is_integer($object->id));
		$this->assertTrue(is_bool($object->is_admin));
		$this->assertTrue(is_bool($object->profile_hidden));
		$this->assertTrue(is_bool($object->wants_notification_comment_quote));
		$this->assertTrue(Str::startsWith($object->url_avatar, 'http'));
	}

	protected function assertIsPaginatedResponse()
	{
		$this->checkPagesAreSet();
		
		// Assert attributes
		$attributeName = 'total_'.$this->contentType;
		$this->assertObjectHasAttribute($attributeName, $this->json);
		$this->assertObjectHasAttribute('total_pages', $this->json);
		$this->assertObjectHasAttribute('page', $this->json);
		$this->assertObjectHasAttribute('pagesize', $this->json);
		$this->assertObjectHasAttribute('url', $this->json);
		$this->assertObjectHasAttribute('has_next_page', $this->json);
		$this->assertObjectHasAttribute('has_previous_page', $this->json);
		
		if ($this->json->has_next_page)
			$this->assertObjectHasAttribute('next_page', $this->json);
		
		if ($this->json->has_previous_page)
			$this->assertObjectHasAttribute('previous_page', $this->json);

		// Assert types
		$this->assertTrue(is_integer($this->json->$attributeName));
		$this->assertTrue(is_integer($this->json->total_pages));
		$this->assertTrue(is_integer($this->json->page));
		$this->assertTrue(is_integer($this->json->pagesize));
		$this->assertTrue(is_bool($this->json->has_next_page));
		$this->assertTrue(is_bool($this->json->has_previous_page));

		// Assert values
		$this->assertEquals($this->page, $this->json->page);
		$this->assertEquals($this->pagesize, $this->json->pagesize);
		$this->assertEquals($this->nbRessources, $this->json->$attributeName);
		$this->assertEquals($this->computeTotalPages(), $this->json->total_pages);

		// Check URL format		
		if ($this->json->has_next_page)
			$this->assertTrue(Str::startsWith($this->json->next_page, 'http'));
		else
			$this->assertObjectNotHasAttribute('next_page', $this->json);
		
		if ($this->json->has_previous_page)
			$this->assertTrue(Str::startsWith($this->json->previous_page, 'http'));
		else
			$this->assertObjectNotHasAttribute('previous_page', $this->json);
	}

	private function computeTotalPages()
	{
		return ceil($this->nbRessources / $this->pagesize);
	}

	private function checkPagesAreSet()
	{
		if (is_null($this->page) OR is_null($this->pagesize))
			throw new \InvalidArgumentException("Page and pagesize must be set before calling this method", 1);	
	}

	protected function assertHasNextAndPreviousPage()
	{		
		$this->assertTrue($this->json->has_next_page);
		$this->assertTrue($this->json->has_previous_page);
	}

	protected function assertHasNextPage()
	{		
		$this->assertTrue($this->json->has_next_page);
		$this->assertFalse($this->json->has_previous_page);
	}

	protected function assertHasPreviousPage()
	{		
		$this->assertFalse($this->json->has_next_page);
		$this->assertTrue($this->json->has_previous_page);
	}

	protected function assertResponseHasSmallUser()
	{
		$this->assertObjectHasAttribute('user', $this->json);
		$this->assertObjectIsSmallUser($this->json->user);
	}
}