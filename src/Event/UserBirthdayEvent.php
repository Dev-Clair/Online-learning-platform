<?php

namespace App\Event;

use App\Entity\Users\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserBirthdayEvent extends Event
{
    public const NAME = "user.birthday";

    public function __construct(private User $user)
    {
    }

    public function getUser()
    {
        return $this->user;
    }
}
