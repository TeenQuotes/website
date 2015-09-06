<?php

/*
 * This file is part of the Teen Quotes website.
 *
 * (c) Antoine Augusti <antoine.augusti@teen-quotes.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ShowStoryCest
{
    /**
     * The logged in user.
     *
     * @var \TeenQuotes\Users\Models\User
     */
    private $user;

    /**
     * The hidden story.
     *
     * @var \TeenQuotes\Stories\Models\Story
     */
    private $hiddenStory;

    public function _before(FunctionalTester $I)
    {
        // Create some published quotes and some stories
        $I->createSomePublishedQuotes();
        $I->createSomeStories();

        // Create a new user
        $this->user = $I->logANewUser();

        // Create a hidden story
        $this->hiddenStory = $I->createAStoryWithHiddenUser();

        $I->navigateToTheStoryPage();
    }

    public function browseHiddenStory(FunctionalTester $I)
    {
        $I->am('a member of Teen Quotes');
        $I->wantTo('browse a story with a hidden user');

        $I->click('#'.$this->hiddenStory->id);
        $I->seeCurrentRouteIs('story.show', $this->hiddenStory->id);

        // I don't see a link to the author's profile in the title
        $I->dontSeeElement('.story[data-id="'.$this->hiddenStory->id.'"] h3 a.author-link');
        $I->seeElement('.story[data-id="'.$this->hiddenStory->id.'"] h3');

        // I don't see a link to the author's profile on its avatar
        $I->dontSeeElement('.story[data-id="'.$this->hiddenStory->id.'"] a img.avatar');
        $I->seeElement('.story[data-id="'.$this->hiddenStory->id.'"] img.avatar');

        $this->seeStoryDisplaysRequiredInformation($I, $this->hiddenStory->id);
    }

    public function browseRegularStory(FunctionalTester $I)
    {
        $I->am('a member of Teen Quotes');
        $I->wantTo('browse a story');

        $id = 1;

        $I->click('#'.$id);
        $I->seeCurrentRouteIs('story.show', $id);

        // I see a link to the author's profile in the title
        $I->seeElement('.story[data-id="'.$id.'"] h3 a.author-link');

        // I see a link to the author's profile on its avatar
        $I->seeElement('.story[data-id="'.$id.'"] a img.avatar');

        $this->seeStoryDisplaysRequiredInformation($I, $id);
    }

    private function seeStoryDisplaysRequiredInformation(FunctionalTester $I, $id)
    {
        $I->seeElement('.story[data-id="'.$id.'"] .story-date');
        $I->seeElement('.story[data-id="'.$id.'"] .story-id');

        $I->see('Tell us your story', '.story[data-id="'.$id.'"]');
        $I->see('Tell us how you use', '.story[data-id="'.$id.'"]');

        $I->see('Go back');
    }
}
