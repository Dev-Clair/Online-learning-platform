<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function sendEmail(string $to, string $subject, string $message)
    {
        $email = (new Email())
            ->to($to)
            ->subject($subject)
            ->text($message);

        $this->mailer->send($email);
    }
}
