<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function sendEmail(array $to, string $subject, string $message)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('aniogbu.samuel@jagaadonline.com', 'Aniogbu Samuel'))
            ->to(new $to[0], $to[1])
            ->subject($subject)
            ->text($message)
            ->textTemplate('')
            ->htmlTemplate('');

        $this->mailer->send($email);
    }
}
