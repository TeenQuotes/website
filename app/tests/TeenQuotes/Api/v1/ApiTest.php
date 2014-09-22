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
	 * Describes relations embedded
	 * @var array
	 */
	protected $embedsRelation = [];

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
	 * @var stdClass
	 */
	protected $json;

	/**
	 * Parameters that will be added when replacing inputs
	 * @var array
	 */
	protected $addArray = [];

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
		return str_random($length);
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

	protected function assertResponseHasRequiredAttributes()
	{
		$this->assertObjectHasAttributes($this->json, $this->requiredAttributes);
	}

	protected function assertObjectHasRequiredAttributes($object)
	{
		return $this->assertObjectHasAttributes($object, $this->requiredAttributes);
	}

	protected function assertObjectHasAttributes($object, $array)
	{
		foreach ($array as $value) {
			$this->assertObjectHasAttribute($value, $object);
			if (Str::contains($value, '_id'))
				$this->assertTrue(is_integer($object->$value));
		}
	}

	protected function tryPaginatedContentNotFound($param = null)
	{
		$data = [
			'page' => $this->getIdNonExistingRessource(),
			'pagesize' => $this->nbRessources
		];

		$this->addInputReplace($data);

		if (is_null($param))
			$this->doRequest('index');
		else
			$this->doRequest('index', $param);

		$this->assertResponseIsNotFound();

		return $this;
	}

	protected function withStatusMessage($status)
	{
		$this->assertResponseKeyIs('status', $status);
		
		return $this;
	}

	protected function withSuccessMessage($status)
	{
		$this->assertResponseKeyIs('success', $status);
		
		return $this;
	}

	protected function withErrorMessage($error)
	{
		$this->assertResponseKeyIs('error', $error);
		
		return $this;
	}

	protected function tryStore($method = 'store', $requestParam = null)
	{
		if (is_null($requestParam))
			$this->doRequest($method);
		else
			$this->doRequest($method, $requestParam);
		
		return $this;
	}

	protected function bindJson()
	{
		$content = $this->response->getContent();
		$this->json = json_decode($content);
	}

	protected function doRequest($method, $param = null)
	{
		Input::replace($this->addArray);
		
		if (is_null($param))
			$this->response = $this->controller->$method();
		else
			$this->response = $this->controller->$method($param);
		
		$this->bindJson();
	}

	protected function embedsSmallUser()
	{
		return in_array('small_user', $this->embedsRelation);
	}

	protected function embedsQuote()
	{
		return in_array('quote', $this->embedsRelation);
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
		$this->doRequest('show', $id);
		$this->bindJson();

		$this->assertStatusCodeIs(Response::HTTP_OK);

		$this->assertObjectMatchesExpectedSchema($this->json);
	}

	protected function getDecodedJson()
	{
		return $this->json;
	}

	protected function tryFirstPage($requestParam = null)
	{
		$this->page = 1;
		$this->pagesize = $this->nbRessources;

		$this->replacePagesInput();

		if (is_null($requestParam))
			$this->doRequest('index');
		else
			$this->doRequest('index', $requestParam);
		
		$this->assertIsPaginatedResponse();

		$objectName = $this->contentType;
		$objects = $this->json->$objectName;
		for ($i = 0; $i < $this->nbRessources; $i++)
			$this->assertObjectMatchesExpectedSchema($objects[$i]);

		$this->assertNeighborsPagesMatch();
	}

	protected function assertObjectMatchesExpectedSchema($object)
	{
		$this->assertObjectHasAttributes($object, $this->requiredAttributes);
			
		if ($this->embedsSmallUser())
			$this->assertObjectContainsSmallUser($object);
		if ($this->embedsQuote())
			$this->assertObjectContainsQuote($object);
	}

	protected function tryMiddlePage($requestParam = null)
	{
		$this->page = max(2, ($this->nbRessources / 2));
		$this->pagesize = 1;

		$this->replacePagesInput();

		if (is_null($requestParam))
			$this->doRequest('index');
		else
			$this->doRequest('index', $requestParam);

		$this->assertIsPaginatedResponse();
		$this->assertHasNextAndPreviousPage();
		
		$objectName = $this->contentType;
		$objects = $this->json->$objectName;

		$this->assertObjectMatchesExpectedSchema(reset($objects));

		$this->assertNeighborsPagesMatch();
	}

	protected function addInputReplace($addArray)
	{
		foreach ($addArray as $key => $value)
			$this->addArray[$key] = $value;
	}

	protected function replacePagesInput()
	{
		$data = [
			'page' => $this->page,
			'pagesize' => $this->pagesize,
		];

		$this->addInputReplace($data);
	}

	protected function assertObjectContainsSmallUser($object)
	{
		return $this->assertObjectIsSmallUser($object->user);
	}

	protected function assertObjectContainsQuote($object)
	{
		return $this->assertObjectIsQuote($object->quote);
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

	protected function assertObjectIsQuote($object)
	{
		// Assert attributes
		$this->assertObjectHasAttribute('id', $object);
		$this->assertObjectHasAttribute('content', $object);
		$this->assertObjectHasAttribute('user_id', $object);
		$this->assertObjectHasAttribute('approved', $object);
		$this->assertObjectHasAttribute('created_at', $object);
		$this->assertObjectHasAttribute('has_comments', $object);
		$this->assertObjectHasAttribute('total_comments', $object);
		$this->assertObjectHasAttribute('is_favorite', $object);
		
		// Assert types
		$this->assertTrue(is_integer($object->id));
		$this->assertTrue(is_string($object->content));
		$this->assertTrue(is_integer($object->user_id));
		$this->assertTrue(is_integer($object->approved));
		$this->assertTrue(is_string($object->created_at));
		$this->assertTrue(is_bool($object->has_comments));
		$this->assertTrue(is_integer($object->total_comments));
		$this->assertTrue(is_bool($object->is_favorite));
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