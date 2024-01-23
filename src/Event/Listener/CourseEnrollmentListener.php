<?php

namespace App\Event\Listener;

use App\Event\CourseEnrollmentEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CourseEnrollmentListener
{
    public function __construct(private MailerService $mailerService)
    {
    }

    #[AsEventListener(event: CourseEnrollmentEvent::class)]
    public function onEnrollment(CourseEnrollmentEvent $event)
    {
        $enrollment = $event->getEnrollment();

        $to = $enrollment->getStudent()->getEmail();
        $subject = 'Enrollment Complete! Start Learning Now.';
        $message = "You’re all set to start learning. Ready to jump in?";

        $this->mailerService->sendEmail($to, $subject, $message);
    }

    #[AsEventListener(event: CourseEnrollmentEvent::class)]
    public function onUnenrollment(CourseEnrollmentEvent $event)
    {
        $enrollment = $event->getEnrollment();

        $to = $enrollment->getStudent()->getEmail();
        $subject = 'Unenrolled!';
        $message = "Sorry, You’ve been enrolled from " . $enrollment->getCourses()->getTitle() . ". Kindly enroll again to continue learning";

        $this->mailerService->sendEmail($to, $subject, $message);
    }
}
