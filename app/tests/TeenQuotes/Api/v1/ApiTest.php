<?php

use Laracasts\TestDummy\DbTestCase;
use Faker\Factory as Faker;

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

	public function setUp()
	{
		parent::setUp();
		$this->faker = Faker::create();
	}

	protected function assertResponseIsNotFound()
	{		
		$this->assertStatusCodeIs(404);
		
		$json = $this->retrieveJson($this->response);

		$this->assertObjectHasAttribute('status', $json);
		$this->assertObjectHasAttribute('error', $json);
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
		$this->assertObjectHasAttributes($this->retrieveJson(), $array);
	}

	protected function retrieveJson()
	{
		$content = $this->response->getContent();
		return json_decode($content);
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

		$this->response = $this->controller->index();
		
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
		$this->response = $this->controller->$method();
		return $this;
	}

	protected function tryMiddlePage()
	{
		$this->page = max(2, ($this->nbRessources / 2));
		$this->pagesize = 1;
		Input::replace([
			'page' => $this->page,
			'pagesize' => $this->pagesize
		]);

		$this->response = $this->controller->index();
		$this->assertIsPaginatedResponse();
		$this->assertHasNextAndPreviousPage();
		
		$objectName = $this->contentType;
		$objects = $this->retrieveJson()->$objectName;
		if ($this->containsSmallUser)
			$this->assertObjectContainsSmallUser(reset($objects));
		$this->assertObjectHasAttributes(reset($objects), $this->requiredAttributes);

		$this->assertNeighborsPagesMatch();
	}

	protected function tryShowNotFound()
	{
		$this->response = $this->controller->show($this->nbRessources + 1);

		$this->assertResponseIsNotFound();

		return $this;
	}

	protected function assertBelongsToLoggedInUser()
	{
		$json = $this->retrieveJson();
		$this->assertEquals($json->user_id, $this->userIdLoggedIn);
	}

	protected function tryShowFound($id)
	{
		$this->response = $this->controller->show($id);
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

		$this->response = $this->controller->index();
		$this->assertIsPaginatedResponse();

		$objectName = $this->contentType;
		$objects = $this->retrieveJson()->$objectName;
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
		$object = $this->retrieveJson();
		$this->assertEquals($object->$key, $value);
	}

	protected function assertNeighborsPagesMatch()
	{
		$this->checkPagesAreSet();

		$object = $this->retrieveJson();
		
		$nextPage = $this->page + 1;
		$previousPage = $this->page - 1;

		if ($nextPage < $this->computeTotalPages())
			$this->assertTrue(Str::contains($object->next_page, 'page='.$nextPage.'&pagesize='.$this->pagesize));
		if ($previousPage >= 1 AND $this->computeTotalPages() > 1)
			$this->assertTrue(Str::contains($object->previous_page, 'page='.$previousPage.'&pagesize='.$this->pagesize));
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
		
		$object = $this->retrieveJson();

		// Assert attributes
		$attributeName = 'total_'.$this->contentType;
		$this->assertObjectHasAttribute($attributeName, $object);
		$this->assertObjectHasAttribute('total_pages', $object);
		$this->assertObjectHasAttribute('page', $object);
		$this->assertObjectHasAttribute('pagesize', $object);
		$this->assertObjectHasAttribute('url', $object);
		$this->assertObjectHasAttribute('has_next_page', $object);
		$this->assertObjectHasAttribute('has_previous_page', $object);
		
		if ($object->has_next_page)
			$this->assertObjectHasAttribute('next_page', $object);
		
		if ($object->has_previous_page)
			$this->assertObjectHasAttribute('previous_page', $object);

		// Assert types
		$this->assertTrue(is_integer($object->$attributeName));
		$this->assertTrue(is_integer($object->total_pages));
		$this->assertTrue(is_integer($object->page));
		$this->assertTrue(is_integer($object->pagesize));
		$this->assertTrue(is_bool($object->has_next_page));
		$this->assertTrue(is_bool($object->has_previous_page));

		// Assert values
		$this->assertEquals($this->page, $object->page);
		$this->assertEquals($this->pagesize, $object->pagesize);
		$this->assertEquals($this->nbRessources, $object->$attributeName);
		$this->assertEquals($this->computeTotalPages(), $object->total_pages);

		// Check URL format		
		if ($object->has_next_page)
			$this->assertTrue(Str::startsWith($object->next_page, 'http'));
		else
			$this->assertObjectNotHasAttribute('next_page', $object);
		
		if ($object->has_previous_page)
			$this->assertTrue(Str::startsWith($object->previous_page, 'http'));
		else
			$this->assertObjectNotHasAttribute('previous_page', $object);
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
		$object = $this->retrieveJson();
		
		$this->assertTrue($object->has_next_page);
		$this->assertTrue($object->has_previous_page);
	}

	protected function assertHasNextPage()
	{
		$object = $this->retrieveJson();
		
		$this->assertTrue($object->has_next_page);
		$this->assertFalse($object->has_previous_page);
	}

	protected function assertHasPreviousPage()
	{
		$object = $this->retrieveJson();
		
		$this->assertFalse($object->has_next_page);
		$this->assertTrue($object->has_previous_page);
	}

	protected function assertResponseHasSmallUser()
	{
		$json = $this->retrieveJson();
		$this->assertObjectHasAttribute('user', $json);
		$this->assertObjectIsSmallUser($json->user);
	}
}