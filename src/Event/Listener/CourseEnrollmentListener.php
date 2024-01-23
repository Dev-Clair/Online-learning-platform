<?php

namespace App\Events\Listener;

use App\Event\EnrollmentEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class CourseEnrollmentListener
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onEnrollment(EnrollmentEvent $event)
    {
        $enrollment = $event->getEnrollment();

        $email = (new Email())
            ->to($enrollment->getStudent()->getEmail())
            ->subject('Enrollment Complete! Start learning now.')
            ->text("You’re all set to start learning. Ready to jump in?");

        $this->mailer->send($email);
    }

    public function onUnenrollment(EnrollmentEvent $event)
    {
        $enrollment = $event->getEnrollment();

        $email = (new Email())
            ->to($enrollment->getStudent()->getEmail())
            ->subject('Unenrolled!')
            ->text("Sorry, You’ve been enrolled from " . $enrollment->getCourses()->getTitle() . ". Kindly enroll again to continue learning");

        $this->mailer->send($email);
    }
}
