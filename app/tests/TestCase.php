<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase {

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
	 * Creates the application.
	 *
	 * @return \Symfony\Component\HttpKernel\HttpKernelInterface
	 */
	public function createApplication()
	{
		$unitTesting = true;

		$testEnvironment = 'testing';

		return require __DIR__.'/../../bootstrap/start.php';
	}

	protected function assertResponseIsNotFound()
	{		
		$this->assertEquals(404, $this->response->getStatusCode());
		
		$json = $this->retrieveJson($this->response);

		$this->assertObjectHasAttribute('status', $json);
		$this->assertObjectHasAttribute('error', $json);
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

	protected function assertObjectContainsSmallUser($object)
	{
		return $this->assertObjectIsSmallUser($object->user);
	}

	protected function assertNeighborsPagesMatch($currentPage, $pagesize)
	{
		$object = $this->retrieveJson();
		
		$nextPage = $currentPage + 1;
		$previousPage = $currentPage - 1;

		$this->assertTrue(Str::contains($object->next_page, 'page='.$nextPage.'&pagesize='.$pagesize));
		$this->assertTrue(Str::contains($object->previous_page, 'page='.$previousPage.'&pagesize='.$pagesize));
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
		$object = $this->retrieveJson();

		// Assert attributes
		$attributeName = 'total_'.$this->contentType;
		$this->assertObjectHasAttribute($attributeName, $object);
		$this->assertObjectHasAttribute('total_pages', $object);
		$this->assertObjectHasAttribute('page', $object);
		$this->assertObjectHasAttribute('pagesize', $object);
		$this->assertObjectHasAttribute('url', $object);
		$this->assertObjectHasAttribute('has_next_page', $object);
		$this->assertObjectHasAttribute('next_page', $object);
		$this->assertObjectHasAttribute('has_previous_page', $object);
		$this->assertObjectHasAttribute('previous_page', $object);

		// Assert types
		$this->assertTrue(is_integer($object->$attributeName));
		$this->assertTrue(is_integer($object->total_pages));
		$this->assertTrue(is_integer($object->page));
		$this->assertTrue(is_integer($object->pagesize));
		$this->assertTrue(is_bool($object->has_next_page));
		$this->assertTrue(is_bool($object->has_previous_page));

		if ($object->has_next_page)
			$this->assertTrue(Str::startsWith($object->next_page, 'http'));
		
		if ($object->has_previous_page)
			$this->assertTrue(Str::startsWith($object->previous_page, 'http'));
	}

	protected function assertHasNextAndPreviousPage()
	{
		$object = $this->retrieveJson();
		
		$this->assertTrue($object->has_next_page);
		$this->assertTrue($object->has_previous_page);
	}

	protected function assertResponseHasSmallUser()
	{
		$json = $this->retrieveJson();
		$this->assertObjectHasAttribute('user', $json);
		$this->assertObjectIsSmallUser($json->user);
	}
}
