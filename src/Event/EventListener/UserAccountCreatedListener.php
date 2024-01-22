<?php

namespace App\Events\EventListener;

use App\Event\UserAccountCreatedEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserCreatedListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onUserAccountCreated(UserAccountCreatedEvent $event)
    {
        $user = $event->getUser();

        $email = (new Email())
            ->to($user->getEmail())
            ->subject('Welcome to Jagaad Online')
            ->text('A user account profile has been created for you. Kindly click on the link to create and set-up a secure user password. Thanks and welcome!');

        $this->mailer->send($email);
    }
}
