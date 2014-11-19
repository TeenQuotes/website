<?php namespace Codeception\Module;

use Codeception\Module;
use Illuminate\Http\Response;
use Auth, Input, Str;

class ApiHelper extends Module {
	
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

	/**
	 * Number of ressources to create
	 * @var integer
	 */
	protected $nbRessources = 3;

	/**
	 * Describes relations embedded
	 * @var array
	 */
	protected $embedsRelation = [];

	/**
	 * The content that we are dealing with
	 * @var string
	 */
	protected $contentType;

	/**
	 * Attributes that should be here
	 * @var array
	 */
	protected $requiredAttributes;

	public function assertResponseIsNotFound()
	{		
		$this->assertStatusCodeIs(Response::HTTP_NOT_FOUND);
		$this->assertObjectHasAttribute('status', $this->json);
		$this->assertObjectHasAttribute('error', $this->json);

		return $this;
	}

	private function assertObjectHasAttribute($attribute, $object)
	{
		$this->assertTrue(property_exists($object, $attribute));
	}

	private function assertObjectNotHasAttribute($attribute, $object)
	{
		$this->assertFalse(property_exists($object, $attribute));
	}

	public function generateString($length)
	{
		if ($length == 0)
			return '';
		
		return str_random($length);
	}

	public function logUserWithId($id)
	{
		$this->userIdLoggedIn = $id;
		Auth::loginUsingId($id);

		return Auth::user();
	}

	public function assertStatusCodeIs($code)
	{
		$this->assertEquals($code, $this->response->getStatusCode());
		
		return $this;
	}

	private function assertResponseHasAttributes($array)
	{
		$this->assertObjectHasAttributes($this->json, $array);
	}

	public function assertResponseHasRequiredAttributes()
	{
		$this->assertObjectHasAttributes($this->json, $this->requiredAttributes);
	}

	public function assertObjectHasRequiredAttributes($object)
	{
		return $this->assertObjectHasAttributes($object, $this->requiredAttributes);
	}

	private function assertObjectHasAttributes($object, $array)
	{
		foreach ($array as $value) {
			$this->assertObjectHasAttribute($value, $object);
			if (Str::contains($value, '_id'))
				$this->assertTrue(is_integer($object->$value));
		}
	}

	public function tryPaginatedContentNotFound($params = null)
	{
		$data = [
			'page' => $this->getIdNonExistingRessource(),
			'pagesize' => $this->nbRessources
		];

		$this->addInputReplace($data);

		$this->doRequest('index', $params);

		$this->assertResponseIsNotFound();

		return $this;
	}

	public function withStatusMessage($status)
	{
		$this->assertResponseKeyIs('status', $status);
		
		return $this;
	}

	public function withSuccessMessage($status)
	{
		$this->assertResponseKeyIs('success', $status);
		
		return $this;
	}

	public function withErrorMessage($error)
	{
		$this->assertResponseKeyIs('error', $error);
		
		return $this;
	}

	public function tryStore($method = 'store', $requestParams = null)
	{
		$this->doRequest($method, $requestParams);
		
		return $this;
	}

	public function bindJson($content)
	{
		$this->json = json_decode($content);
	}

	public function doRequest($method, $params = null)
	{
		Input::replace($this->addArray);

		if (is_null($params))
			$this->response = $this->controller->$method();
		else
			$this->response = call_user_func_array([$this->controller, $method], (array) $params);
		
		$this->bindJson($this->response->getContent());

		// Delete overriden request parameters
		// when the request has been made
		$this->addArray = [];

		return $this;
	}

	public function getIdNonExistingRessource()
	{
		return $this->nbRessources + 1;
	}

	public function tryShowNotFound()
	{
		$this->response = $this->controller->show($this->getIdNonExistingRessource());
		$this->bindJson($this->response->getContent());

		$this->assertResponseIsNotFound();

		return $this;
	}

	public function assertBelongsToLoggedInUser()
	{
		$this->assertEquals($this->json->user_id, $this->userIdLoggedIn);

		return $this;
	}

	public function tryShowFound($id)
	{
		$this->doRequest('show', $id);
		$this->bindJson($this->response->getContent());

		$this->assertStatusCodeIs(Response::HTTP_OK);

		$this->assertObjectMatchesExpectedSchema($this->json);
	}

	public function tryFirstPage($method = 'index', $requestParams = null)
	{
		$this->page = 1;
		$this->pagesize = $this->nbRessources;

		$this->replacePagesInput();

		$this->doRequest($method, $requestParams);
		
		$this->assertIsPaginatedResponse();

		$objectName = $this->contentType;
		$objects = $this->json->$objectName;
		for ($i = 0; $i < $this->nbRessources; $i++)
			$this->assertObjectMatchesExpectedSchema($objects[$i]);

		$this->assertNeighborsPagesMatch();
	}

	public function assertResponseMatchesExpectedSchema()
	{
		$this->assertObjectMatchesExpectedSchema($this->json);
	}

	private function assertObjectMatchesExpectedSchema($object)
	{
		$this->assertObjectHasAttributes($object, $this->requiredAttributes);
			
		if ($this->embedsSmallUser())
			$this->assertObjectContainsSmallUser($object);
		if ($this->embedsQuote())
			$this->assertObjectContainsQuote($object);
		if ($this->embedsCountry())
			$this->assertObjectContainsCountry($object);
		if ($this->embedsNewsletters())
			$this->assertObjectContainsNewsletters($object);
	}

	public function tryMiddlePage($method = 'index', $requestParams = null)
	{
		$this->page = max(2, ($this->nbRessources / 2));
		$this->pagesize = 1;

		$this->replacePagesInput();

		$this->doRequest($method, $requestParams);

		$this->assertIsPaginatedResponse();
		$this->assertHasNextAndPreviousPage();
		
		$objectName = $this->contentType;
		$objects = $this->json->$objectName;

		$this->assertObjectMatchesExpectedSchema(reset($objects));

		$this->assertNeighborsPagesMatch();
	}

	public function addInputReplace($addArray)
	{
		foreach ($addArray as $key => $value)
			$this->addArray[$key] = $value;
	}

	private function replacePagesInput()
	{
		$this->addInputReplace([
			'page'     => $this->page,
			'pagesize' => $this->pagesize,
		]);
	}

	private function assertObjectContainsSmallUser($object)
	{
		return $this->assertObjectIsSmallUser($object->user);
	}

	private function assertObjectContainsQuote($object)
	{
		return $this->assertObjectIsQuote($object->quote);
	}

	private function assertObjectContainsCountry($object)
	{
		return $this->assertObjectIsCountry($object->country_object);
	}

	private function assertObjectContainsNewsletters($object)
	{
		$newsletters = $object->newsletters;
		
		// Check the format for each newsletter
		foreach ($newsletters as $o) {
			$this->assertObjectIsNewsletter($o);
		}
	}

	public function assertResponseKeyIs($key, $value)
	{
		$this->assertEquals($this->json->$key, $value);
	}

	public function setContentType($value)
	{
		$this->contentType = $value;
	}

	public function setController($value)
	{
		$this->controller = $value;
	}

	public function setResponse($value)
	{
		$this->response = $value;
	}

	public function setEmbedsRelation(array $value)
	{
		$this->embedsRelation = $value;
	}

	public function setRequiredAttributes($value)
	{
		$this->requiredAttributes = $value;
	}

	public function getNbRessources()
	{
		return $this->nbRessources;
	}

	public function getController()
	{
		return $this->controller;
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function getDecodedJson()
	{
		return $this->json;
	}

	private function assertNeighborsPagesMatch()
	{
		$this->checkPagesAreSet();
		
		$nextPage = $this->page + 1;
		$previousPage = $this->page - 1;

		if ($nextPage < $this->computeTotalPages())
			$this->assertTrue(Str::contains($this->json->next_page, 'page='.$nextPage.'&pagesize='.$this->pagesize));
		if ($previousPage >= 1 AND $this->computeTotalPages() > 1)
			$this->assertTrue(Str::contains($this->json->previous_page, 'page='.$previousPage.'&pagesize='.$this->pagesize));
	}

	private function assertObjectIsSmallUser($object)
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

	private function assertObjectIsQuote($object)
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

	private function assertObjectIsCountry($object)
	{
		// Assert attributes
		$this->assertObjectHasAttribute('id', $object);
		$this->assertObjectHasAttribute('name', $object);
		
		// Assert types
		$this->assertTrue(is_integer($object->id));
		$this->assertTrue(is_string($object->name));
	}

	private function assertObjectIsNewsletter($object)
	{
		// Assert attributes
		$this->assertObjectHasAttribute('user_id', $object);
		$this->assertObjectHasAttribute('type', $object);
		$this->assertObjectHasAttribute('created_at', $object);
		
		// Assert types
		$this->assertTrue(is_integer($object->user_id));
		$this->assertTrue(is_string($object->type));
		$this->assertTrue(in_array($object->type, ['weekly', 'daily']));
		$this->assertTrue(is_string($object->created_at));
	}

	private function assertIsPaginatedResponse()
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

	private function assertHasNextAndPreviousPage()
	{		
		$this->assertTrue($this->json->has_next_page);
		$this->assertTrue($this->json->has_previous_page);
	}

	private function assertHasNextPage()
	{		
		$this->assertTrue($this->json->has_next_page);
		$this->assertFalse($this->json->has_previous_page);
	}

	private function assertHasPreviousPage()
	{		
		$this->assertFalse($this->json->has_next_page);
		$this->assertTrue($this->json->has_previous_page);
	}

	private function embedsSmallUser()
	{
		return in_array('small_user', $this->embedsRelation);
	}

	private function embedsQuote()
	{
		return in_array('quote', $this->embedsRelation);
	}

	private function embedsCountry()
	{
		return in_array('country', $this->embedsRelation);
	}

	private function embedsNewsletters()
	{
		return in_array('newsletters', $this->embedsRelation);
	}

	private function assertResponseHasSmallUser()
	{
		$this->assertObjectHasAttribute('user', $this->json);
		$this->assertObjectIsSmallUser($this->json->user);
	}
}