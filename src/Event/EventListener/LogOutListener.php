<?php

namespace App\Event\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogOutListener
{
    #[AsEventListener(event: LogoutEvent::class)]
    public function onLogoutEvent(LogoutEvent $event): void
    {
        // ...
    }
}
