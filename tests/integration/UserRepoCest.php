<?php


class UserRepoCest
{
    /**
     * @var \TeenQuotes\Users\Repositories\UserRepository
     */
    private $repo;

    /**
     * @var \TeenQuotes\Newsletters\Repositories\NewsletterRepository
     */
    private $newsletterRepo;

    /**
     * @var \TeenQuotes\Countries\Repositories\CountryRepository
     */
    private $countryRepo;

    public function _before()
    {
        $this->repo           = App::make('TeenQuotes\Users\Repositories\UserRepository');
        $this->newsletterRepo = App::make('TeenQuotes\Newsletters\Repositories\NewsletterRepository');
        $this->countryRepo    = App::make('TeenQuotes\Countries\Repositories\CountryRepository');
    }

    public function testGetById(IntegrationTester $I)
    {
        $I->insertInDatabase(1, 'User');
        $u = $I->insertInDatabase(1, 'User');

        $user = $this->repo->getById($u->id);

        $I->assertEquals($u->login, $user->login);
        $I->assertEquals($u->email, $user->email);
    }

    public function testGetByEmail(IntegrationTester $I)
    {
        $I->insertInDatabase(1, 'User');
        $u = $I->insertInDatabase(1, 'User');

        $user = $this->repo->getByEmail($u->email);

        $I->assertEquals($u->login, $user->login);
        $I->assertEquals($u->email, $user->email);
    }

    public function testGetByEmails(IntegrationTester $I)
    {
        $I->insertInDatabase(2, 'User');
        $user = $I->insertInDatabase(1, 'User', ['email' => 'foo@bar.com']);

        $users = $this->repo->getByEmails(['foo@bar.com']);

        $I->assertIsCollection($users);
        $I->assertEquals(1, count($users));
        $I->assertEquals($user->login, $users->first()->login);
    }

    public function testGetByLogin(IntegrationTester $I)
    {
        $I->insertInDatabase(1, 'User');
        $u = $I->insertInDatabase(1, 'User');

        $user = $this->repo->getByLogin($u->login);

        $I->assertEquals($u->login, $user->login);
        $I->assertEquals($u->email, $user->email);
    }

    public function testCountByPartialLogin(IntegrationTester $I)
    {
        $partial = 'foo';

        $count = $this->insertUsersForSearch($I, $partial);

        $I->assertEquals($count, $this->repo->countByPartialLogin($partial));
    }

    public function testUpdatePassword(IntegrationTester $I)
    {
        $u = $I->insertInDatabase(1, 'User');
        $newPassword = 'foobar22';
        $credentials = ['login' => $u->login, 'password' => $newPassword];

        $this->repo->updatePassword($u, $newPassword);

        $I->assertTrue(Auth::validate($credentials));
    }

    public function testUpdateEmail(IntegrationTester $I)
    {
        $u = $I->insertInDatabase(1, 'User');
        $newEmail = 'foobar@bar.com';

        $this->repo->updateEmail($u, $newEmail);

        $user = $this->repo->getById(1);

        $I->assertEquals($newEmail, $user->email);
    }

    public function testUpdateProfile(IntegrationTester $I)
    {
        $gender = 'F';
        $country = 10; // Argentina
        $city = 'Foobar';
        $about_me = 'Enjoying integration tests';
        $birthdate = '1900-12-01';
        $avatar = null;

        $u = $I->insertInDatabase(1, 'User');

        $this->repo->updateProfile($u, $gender, $country, $city, $about_me, $birthdate, $avatar);

        $user = $this->repo->getById($u->id);

        $I->assertTrue($user->isFemale());
        $I->assertEquals('Argentina', $user->country_object->name);
        $I->assertEquals($city, $user->city);
        $I->assertEquals($about_me, $user->about_me);
        $I->assertEquals($birthdate, $user->birthdate);
        // TODO: test the avatar
    }

    public function testUpdateSettings(IntegrationTester $I)
    {
        $notification_comment_quote = false;
        $hide_profile = false;

        $u = $I->insertInDatabase(1, 'User', ['notification_comment_quote' => true, 'hide_profile' => true]);

        $this->repo->updateSettings($u, $notification_comment_quote, $hide_profile);

        $user = $this->repo->getById($u->id);
        $I->assertFalse($user->isHiddenProfile());
        $I->assertFalse($user->wantsEmailComment());
    }

    public function testGetAll(IntegrationTester $I)
    {
        $I->insertInDatabase(2, 'User');
        $I->insertInDatabase(1, 'User', ['hide_profile' => true]);

        $users = $this->repo->getAll();

        $I->assertIsCollection($users);
        $I->assertEquals(3, count($users));
    }

    public function testBirthdayToday(IntegrationTester $I)
    {
        $birthDate = Carbon::now()->subYears(20)->format('Y-m-d');
        $notBirthDate = Carbon::now()->subYears(2)->subDays(5)->format('Y-m-d');
        $I->insertInDatabase(2, 'User', ['birthdate' => $notBirthDate]);
        $u = $I->insertInDatabase(1, 'User', ['birthdate' => $birthDate]);

        $users = $this->repo->birthdayToday();

        $I->assertIsCollection($users);
        $I->assertEquals(1, count($users));
        $I->assertEquals($u->login, $users->first()->login);
    }

    public function testShowByLoginOrId(IntegrationTester $I)
    {
        $u = $I->insertInDatabase(1, 'User');

        // Get by ID
        $user = $this->repo->showByLoginOrId($u->id);
        $I->assertEquals($u->login, $user->login);

        // Get by login
        $user = $this->repo->showByLoginOrId($u->login);
        $I->assertEquals($u->login, $user->login);
    }

    public function testGetLoggedInSince(IntegrationTester $I)
    {
        $I->insertInDatabase(1, 'User', ['last_visit' => Carbon::now()->subMonths(2)]);
        $I->insertInDatabase(2, 'User', ['last_visit' => Carbon::now()->subDays(10)]);

        $users = $this->repo->getLoggedInSince(Carbon::now()->subDays(15), 1, 10);

        $I->assertIsCollection($users);
        $I->assertEquals(2, count($users));
    }

    public function testSearchByPartialLogin(IntegrationTester $I)
    {
        $partial = 'foo';

        $count = $this->insertUsersForSearch($I, $partial);
        $users = $this->repo->searchByPartialLogin($partial, 1, 20);

        $I->assertIsCollection($users);
        $I->assertEquals($count, count($users));
    }

    public function testCreate(IntegrationTester $I)
    {
        $login = 'foobar';
        $email = 'foo@bar.com';
        $password = 'foobar22';
        $ip = '22.22.22.22';
        $lastVisit = Carbon::now();
        $country = 10; // Argentina
        $city = 'Foo City';

        $this->repo->create($login, $email, $password, $ip, $lastVisit, $country, $city);

        $user = $this->repo->getById(1);

        // Test default assigned values
        $I->assertTrue($user->getIsSubscribedToWeekly());
        $I->assertFalse($user->getIsSubscribedToDaily());
        $I->assertTrue($user->wantsEmailComment());

        $I->assertEquals($login, $user->login);
        $I->assertEquals($email, $user->email);
        $I->assertEquals($city, $user->city);
        $I->assertEquals('Argentina', $user->country_object->name);
    }

    public function testDestroy(IntegrationTester $I)
    {
        $u = $I->insertInDatabase(1, 'User');

        $this->repo->destroy($u->id);

        $I->assertNull($this->repo->getById($u->id));
    }

    public function testMostCommon(IntegrationTester $I)
    {
        $mostCommonId = 42;
        $I->insertInDatabase(2, 'User', ['country' => 2]);
        $I->insertInDatabase(3, 'User', ['country' => $mostCommonId]);

        $I->assertEquals($mostCommonId, $this->repo->mostCommonCountryId());
    }

    public function testGetNonActiveHavingNewsletter(IntegrationTester $I)
    {
        $lastYear = Carbon::now()->subDays(370);
        $u = $I->insertInDatabase(1, 'User', ['last_visit' => $lastYear]);

        // A user, who haven't logged in the last year, but without newsletters
        $fake = $I->insertInDatabase(1, 'User', ['last_visit' => $lastYear]);
        $this->newsletterRepo->deleteForUserAndType($fake, 'weekly');

        $users = $this->repo->getNonActiveHavingNewsletter();

        $I->assertIsCollection($users);
        $I->assertEquals(1, count($users));
        $I->assertEquals($u->login, $users->first()->login);
    }

    public function testFromCountry(IntegrationTester $I)
    {
        $firstCountry = $this->countryRepo->findById(1);
        $secondCountry = $this->countryRepo->findById(2);

        // Create some users from the first country
        $I->insertInDatabase(5, 'User', ['country' => $firstCountry->id]);

        // It shouldn't retrieve users from the 1st country
        $I->assertEmpty($this->repo->fromCountry($secondCountry, 1, 10));
        // We should retrieve our users from the 2nd country
        $I->assertEquals(5, count($this->repo->fromCountry($firstCountry, 1, 10)));
        // We shouldn't retrieve too much users
        $I->assertEquals(2, count($this->repo->fromCountry($firstCountry, 1, 2)));
    }

    public function testCountFromCountry(IntegrationTester $I)
    {
        $firstCountry = $this->countryRepo->findById(1);
        $secondCountry = $this->countryRepo->findById(2);

        // Create some users from the first country
        $I->insertInDatabase(2, 'User', ['country' => $firstCountry->id]);
        // One user with an hidden profile
        $I->insertInDatabase(1, 'User', ['country' => $firstCountry->id, 'hide_profile' => 1]);

        // We shouldn't count the user with an hidden profile
        $I->assertEquals(2, $this->repo->countFromCountry($firstCountry));

        $I->assertEquals(0, $this->repo->countFromCountry($secondCountry));
    }

    private function insertUsersForSearch(IntegrationTester $I, $partial)
    {
        $I->insertInDatabase(2, 'User');
        $I->insertInDatabase(1, 'User', ['login' => 'ab'.$partial.'bar']);
        $I->insertInDatabase(1, 'User', ['login' => 'abc'.$partial.'baz']);
        // We shouldn't retrieve an hidden profile
        $I->insertInDatabase(1, 'User', ['login' => 'hidden'.$partial, 'hide_profile' => true]);

        return 2;
    }
}
