<?php

namespace App\Event;

use App\Entity\Users\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserAccountCreatedEvent extends Event
{
    public const ADMIN = "admin.account.created";
    public const INSTRUCTOR = "instructor.account.created";
    public const STUDENT = "student.account.created";

    public function __construct(private User $user)
    {
    }

    public function getUser()
    {
        return $this->user;
    }
}
