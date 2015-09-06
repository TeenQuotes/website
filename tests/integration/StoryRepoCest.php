<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoryRepoCest
{
    /**
     * @var \TeenQuotes\Stories\Repositories\StoryRepository
     */
    private $repo;

    public function _before()
    {
        $this->repo = App::make('TeenQuotes\Stories\Repositories\StoryRepository');
    }

    public function testFindById(IntegrationTester $I)
    {
        $s = $I->insertInDatabase(1, 'Story');
        $I->insertInDatabase(2, 'Story');

        $story = $this->repo->findById($s->id);

        $I->assertEquals($s->content, $story->content);
        $I->assertEquals($s->user->id, $story->user->id);
    }

    public function testIndex(IntegrationTester $I)
    {
        $I->insertInDatabase(5, 'Story', ['created_at' => Carbon::now()->subMonth()]);
        $I->insertInDatabase(1, 'Story');

        $stories = $this->repo->index(1, 3);

        $I->assertIsCollection($stories);
        $I->assertEquals(3, count($stories));
        // It gets the latest story first
        $I->assertEquals(6, $stories->first()->id);
    }

    public function testTotal(IntegrationTester $I)
    {
        $I->assertEquals(0, $this->repo->total());

        $I->insertInDatabase(2, 'Story');
        $I->assertEquals(2, $this->repo->total());
    }

    public function testCreate(IntegrationTester $I)
    {
        $u = $I->insertInDatabase(1, 'User');

        $represent_txt = str_repeat('a', 20);
        $frequence_txt = str_repeat('a', 20);

        $s = $this->repo->create($u, $represent_txt, $frequence_txt);

        $story = $this->repo->findById($s->id);

        $I->assertEquals($represent_txt, $story->represent_txt);
        $I->assertEquals($frequence_txt, $story->frequence_txt);
    }
}
