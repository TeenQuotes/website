<?php

class EditProfileCest
{
    /**
     * The authenticated user.
     *
     * @var \TeenQuotes\Users\Models\User
     */
    private $user;

    /**
     * Params used to create the user.
     *
     * @var array
     */
    private $userParams;

    public function _before(FunctionalTester $I)
    {
        $I->createSomePublishedQuotes();
        $this->userParams = [
            'about_me'     => 'Lorem',
            'birthdate'    => '2000-01-12',
            'city'         => 'Paris',
            // ID of Argentina
            'country'      => 10,
            'country_name' => 'Argentina',
            'gender'       => 'F',
        ];

        // Do not pass the country name to TestDummy
        // We just want the country name to assert that
        // the form is filled with the right values
        $overrides = $this->userParams;
        array_forget($overrides, 'country_name');

        $this->user = $I->logANewUser($overrides);
    }

    public function updateMyProfile(FunctionalTester $I)
    {
        $I->am('a logged in Teen Quotes member');
        $I->wantTo('update my profile');

        // Check that the form has got the values given
        // at "sign up"
        $I->navigateToMyEditProfilePage();
        $I->assertEditProfileFormIsFilledWith($this->userParams);

        // Edit the user's profile and assert that he has
        // got a new profile
        $newParams = [
            'about_me'     => 'I am a tester',
            'birthdate'    => '1993-12-01',
            'city'         => 'Rouen',
            'country_name' => 'France',
            'gender'       => 'M',
            'avatar'       => 'cage.jpg',
        ];

        $I->fillEditProfileFormWith($newParams);
        $I->assertProfileHasBeenChangedWithParams($newParams);
    }
}
