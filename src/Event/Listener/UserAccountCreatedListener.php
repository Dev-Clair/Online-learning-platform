<?php

namespace App\Event\Listener;

use App\Event\UserAccountCreatedEvent;
use App\Service\MailerService;

class UserAccountCreatedListener
{
    public function __construct(private MailerService $mailerService)
    {
    }

    public function onAdminUserAccountCreated(UserAccountCreatedEvent $event)
    {
        $user = $event->getUser();

        $to = $user->getEmail();
        $subject = 'Welcome to Jagaad Online';
        $message = 'An admin account profile has been created for you. Kindly click on the link to create and set-up a secure password for your account. Thanks and welcome!';

        $this->mailerService->sendEmail($to, $subject, $message);
    }

    public function onInstructorUserAccountCreated(UserAccountCreatedEvent $event)
    {
        $user = $event->getUser();

        $to = $user->getEmail();
        $subject = 'Welcome to Jagaad Online';
        $message = 'An instructor account profile has been created for you. Kindly click on the link to create and set-up a secure password for your account. Thanks and welcome!';

        $this->mailerService->sendEmail($to, $subject, $message);
    }

    public function onStudentUserAccountCreated(UserAccountCreatedEvent $event)
    {
        $user = $event->getUser();

        $to = $user->getEmail();
        $subject = 'Welcome to Jagaad Online';
        $message = '';

        $this->mailerService->sendEmail($to, $subject, $message);
    }
}
