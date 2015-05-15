<?php

use Illuminate\Http\Response;

class StoriesTest extends ApiTest
{
    protected $embedsRelation = ['user_small'];
    protected $requiredAttributes = ['id', 'represent_txt', 'frequence_txt', 'user_id', 'created_at', 'updated_at'];

    protected function _before()
    {
        parent::_before();

        $this->unitTester->insertInDatabase($this->unitTester->getNbRessources(), 'Story');

        $this->unitTester->setContentType('stories');

        $this->unitTester->setController(App::make('TeenQuotes\Api\V1\Controllers\StoriesController'));
    }

    public function testShowNotFound()
    {
        // Not found story
        $this->unitTester->tryShowNotFound()
            ->withStatusMessage('story_not_found')
            ->withErrorMessage('The story #'.$this->unitTester->getIdNonExistingRessource().' was not found.');
    }

    public function testShowFound()
    {
        // Regular story
        for ($i = 1; $i <= $this->unitTester->getNbRessources(); $i++) {
            $this->unitTester->tryShowFound($i);
        }
    }

    public function testIndex()
    {
        // Test with the middle page
        $this->unitTester->tryMiddlePage();

        // Test first page
        $this->unitTester->tryFirstPage();
    }

    /**
     * @expectedException        TeenQuotes\Exceptions\ApiNotFoundException
     * @expectedExceptionMessage stories
     */
    public function testIndexNotFound()
    {
        $this->unitTester->tryPaginatedContentNotFound();
    }

    /**
     * @expectedException Laracasts\Validation\FormValidationException
     * @expectedExceptionMessage The frequence txt field is required.
     */
    public function testStoreNoFrequence()
    {
        $this->hitStore(0, 200);
    }

    /**
     * @expectedException Laracasts\Validation\FormValidationException
     * @expectedExceptionMessage The frequence txt must be at least 100 characters.
     */
    public function testStoreTooSmallFrequence()
    {
        $this->hitStore(50, 200);
    }

    /**
     * @expectedException Laracasts\Validation\FormValidationException
     * @expectedExceptionMessage The frequence txt may not be greater than 1000 characters.
     */
    public function testStoreTooLargeFrequence()
    {
        $this->hitStore(1001, 200);
    }

    /**
     * @expectedException Laracasts\Validation\FormValidationException
     * @expectedExceptionMessage The represent txt field is required.
     */
    public function testStoreNoRepresent()
    {
        $this->hitStore(200, 0);
    }

    /**
     * @expectedException Laracasts\Validation\FormValidationException
     * @expectedExceptionMessage The represent txt must be at least 100 characters.
     */
    public function testStoreTooSmallRepresent()
    {
        $this->hitStore(200, 50);
    }

    /**
     * @expectedException Laracasts\Validation\FormValidationException
     * @expectedExceptionMessage The represent txt may not be greater than 1000 characters.
     */
    public function testStoreTooLargeRepresent()
    {
        $this->hitStore(200, 1001);
    }

    public function testStoreSuccess()
    {
        $this->hitStore(200, 200);

        $this->unitTester->assertStatusCodeIs(Response::HTTP_CREATED)
            ->assertBelongsToLoggedInUser();

        // Check that we can retrieve the new item
        $this->unitTester->tryShowFound($this->unitTester->getNbRessources() + 1);
    }

    /**
     * Hit the store endpoint.
     *
     * @param int $frequenceLength The length of the frequence_txt field
     * @param int $representLength The length of the reprensent_txt field
     */
    private function hitStore($frequenceLength, $representLength)
    {
        $this->unitTester->logUserWithId(1);

        $this->unitTester->addInputReplace([
            'frequence_txt' => $this->unitTester->generateString($frequenceLength),
            'represent_txt' => $this->unitTester->generateString($representLength),
        ]);

        $this->unitTester->tryStore();
    }
}
