<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TagRepoCest
{
    /**
     * @var \TeenQuotes\Tags\Repositories\TagRepository
     */
    private $repo;

    public function _before()
    {
        $this->repo = App::make('TeenQuotes\Tags\Repositories\TagRepository');
    }

    public function testCreate(IntegrationTester $I)
    {
        $name = 'Foobar';

        $this->repo->create($name);

        $I->seeRecord('tags', compact('name'));
    }

    public function testGetByName(IntegrationTester $I)
    {
        $name = 'Foobar';

        $I->insertInDatabase(1, 'Tag', compact('name'));

        $tag = $this->repo->getByName($name);

        $I->assertEquals($tag->name, $name);

        // Non existing tag
        $I->assertNull($this->repo->getByName('notfound'));
    }

    public function testTagQuote(IntegrationTester $I)
    {
        $I->insertInDatabase(1, 'Quote');
        $quote = $I->insertInDatabase(1, 'Quote');
        $tag   = $I->insertInDatabase(1, 'Tag', compact('name'));

        $this->repo->tagQuote($quote, $tag);

        $I->seeRecord('quote_tag', ['quote_id' => $quote->id, 'tag_id' => $tag->id]);
    }

    public function testUntagQuote(IntegrationTester $I)
    {
        $I->insertInDatabase(1, 'Quote');
        $quote = $I->insertInDatabase(1, 'Quote');
        $tag   = $I->insertInDatabase(1, 'Tag', compact('name'));

        $this->repo->tagQuote($quote, $tag);

        // Assert quote was tagged
        $I->seeRecord('quote_tag', ['quote_id' => $quote->id, 'tag_id' => $tag->id]);

        // Untag the quote
        $this->repo->untagQuote($quote, $tag);
        $I->dontSeeRecord('quote_tag', ['quote_id' => $quote->id, 'tag_id' => $tag->id]);
    }

    public function testTagsForQuote(IntegrationTester $I)
    {
        $tags = ['love', 'family'];
        foreach ($tags as $name) {
            $I->insertInDatabase(1, 'Tag', compact('name'));
        }

        $quote = $I->insertInDatabase(1, 'Quote');

        // Add a first tag to the quote
        $this->repo->tagQuote($quote, $this->repo->getByName('love'));
        $I->assertEquals($this->repo->tagsForQuote($quote), ['love']);

        // Add another tag to the quote
        $this->repo->tagQuote($quote, $this->repo->getByName('family'));
        $tagsResult = $this->repo->tagsForQuote($quote);
        sort($tagsResult);

        $I->assertEquals($tagsResult, ['family', 'love']);

        // Remove a tag
        $this->repo->untagQuote($quote, $this->repo->getByName('family'));
        $I->assertEquals($this->repo->tagsForQuote($quote), ['love']);

        // We get an empty array if we have no tags
        $quoteTwo = $I->insertInDatabase(1, 'Quote');
        $I->assertEquals([], $this->repo->tagsForQuote($quoteTwo));
    }

    public function testTotalQuotesForTag(IntegrationTester $I)
    {
        $quotes = $I->insertInDatabase(2, 'Quote');
        $tag    = $I->insertInDatabase(1, 'Tag');

        $I->assertEquals(0, $this->repo->totalQuotesForTag($tag));

        // Tag a first quote
        $this->repo->tagQuote($quotes[0], $tag);
        $I->assertEquals(1, $this->repo->totalQuotesForTag($tag));

        // Add another quote
        $this->repo->tagQuote($quotes[1], $tag);
        $I->assertEquals(2, $this->repo->totalQuotesForTag($tag));

        // Untag a quote
        $this->repo->untagQuote($quotes[0], $tag);
        $I->assertEquals(1, $this->repo->totalQuotesForTag($tag));
    }

    public function testQuotesToTag(IntegrationTester $I)
    {
        $I->createSomeWaitingQuotes(['nb_quotes' => 1, 'content' => 'I love it']);
        $quotes = $I->createSomePublishedQuotes(['nb_quotes' => 3, 'content' => 'I love it']);
        $tag    = $I->insertInDatabase(1, 'Tag', ['name' => 'love']);

        // Tag only first quote
        $this->repo->tagQuote($quotes[0], $tag);
        $I->assertEquals(1, $this->repo->totalQuotesForTag($tag));

        // The 2 other published quotes should be tagged
        $I->assertEquals([$quotes[1]->id, $quotes[2]->id], $this->repo->quotesToTag($tag)->lists('id'));
    }

    public function testAllTags(IntegrationTester $I)
    {
        $tags  = $I->insertInDatabase(3, 'Tag');
        $names = array_map(function ($t) {
            return $t->name;
        }, $tags);

        $allTags  = $this->repo->allTags();
        $allNames = $allTags->map(function ($t) {
            return $t->name;
        })->toArray();

        $I->assertEquals(count($allTags), count($tags));
        $I->assertEquals($names, $allNames);
    }

    public function testRelatedQuotes(IntegrationTester $I)
    {
        $tags = $I->insertInDatabase(2, 'Tag');
        $quotes = $I->createSomePublishedQuotes(['nb_quotes' => 5]);
        foreach ($quotes as $quote) {
            if ($quote->id != $quotes[4]->id) {
                $this->repo->tagQuote($quote, $tags[0]);
                $this->repo->tagQuote($quote, $tags[1]);
            }
        }

        $relatedIDs = $this->repo->relatedQuotes($quotes[3], 2)->lists('id');
        $I->assertEquals([$quotes[2]->id, $quotes[1]->id], $relatedIDs);

        $relatedIDs = $this->repo->relatedQuotes($quotes[3], 1)->lists('id');
        $I->assertEquals([$quotes[2]->id], $relatedIDs);

        // No related quotes
        $I->assertEquals([$quotes[0]->id], $this->repo->relatedQuotes($quotes[1])->lists('id'));
        $I->assertTrue($this->repo->relatedQuotes($quotes[4])->isEmpty());
    }
}
