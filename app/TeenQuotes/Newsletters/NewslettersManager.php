<?php

namespace TeenQuotes\Newsletters;

use App;
use Illuminate\Support\Collection;
use TeenQuotes\Newsletters\Models\Newsletter;
use TeenQuotes\Newsletters\Repositories\NewsletterRepository;
use TeenQuotes\Users\Models\User;

class NewslettersManager
{
    /**
     * @var NewsletterRepository
     */
    private $newslettersRepo;

    /**
     * @var NewsletterList
     */
    private $newslettersList;

    /**
     * @param NewsletterRepository $newslettersRepo
     * @param NewsletterList       $newslettersList
     */
    public function __construct(NewsletterRepository $newslettersRepo, NewsletterList $newslettersList)
    {
        $this->newslettersRepo = $newslettersRepo;
        $this->newslettersList = $newslettersList;
    }

    /**
     * Subscribe a user to a newsletter.
     *
     * @param User   $user
     * @param string $type The newsletter's type
     */
    public function createForUserAndType(User $user, $type)
    {
        $this->newslettersRepo->createForUserAndType($user, $type);

        if ($this->shouldCallAPI()) {
            $this->newslettersList->subscribeTo($this->getListNameFromType($type), $user);
        }
    }

    /**
     * Unsubscribe a user from a newsletter.
     *
     * @param User   $u
     * @param string $type The newsletter's type
     */
    public function deleteForUserAndType(User $u, $type)
    {
        if ($this->shouldCallAPI()) {
            $this->newslettersList->unsubscribeFrom($this->getListNameFromType($type), $u);
        }

        return $this->newslettersRepo->deleteForUserAndType($u, $type);
    }

    /**
     * Unsubscribe multiple users from all newsletters.
     *
     * @param Collection $users
     */
    public function deleteForUsers(Collection $users)
    {
        if ($this->shouldCallAPI()) {
            foreach (Newsletter::getPossibleTypes() as $type) {
                $this->newslettersList->unsubscribeUsersFrom($this->getListNameFromType($type), $users);
            }
        }

        return $this->newslettersRepo->deleteForUsers($users);
    }

    /**
     * Determine if we should call the API.
     *
     * @return bool
     */
    private function shouldCallAPI()
    {
        return App::environment() == 'production';
    }

    /**
     * Get the name of a newsletter's list for a given type.
     *
     * @param string $type
     *
     * @return string
     */
    private function getListNameFromType($type)
    {
        return $type.'Newsletter';
    }
}
