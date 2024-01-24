<?php

namespace App\Event\Listener;

use App\Event\UserBirthdayEvent;
use App\Service\MailerService;

class UserBirthdayListener
{
    public function __construct(private MailerService $mailerService)
    {
    }

    public function onAdminUserAccountCreated(UserBirthdayEvent $event)
    {
        $user = $event->getUser();

        $to = $user->getEmail();
        $subject = 'Happy Birthday!!!';
        $message = '>>>BW
        Dear { $user->getFirstname() }

        We hope your birthday brings you opprotunities to prosper! Wishing you all the best on your special day.

        Happy Birthday!

        From all of us at Jagaad Online.
        BW>>>';

        $this->mailerService->sendEmail($to, $subject, $message);
    }
}
