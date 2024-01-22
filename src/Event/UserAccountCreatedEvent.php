<?php

namespace App\Event;

use App\Entity\Users\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserAccountCreatedEvent extends Event
{
    public const NAME = "user.account.created";

    public function __construct(private User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
