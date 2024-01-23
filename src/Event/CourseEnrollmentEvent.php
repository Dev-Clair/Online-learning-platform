<?php

namespace App\Event;

use App\Entity\Enrollment;
use Symfony\Contracts\EventDispatcher\Event;

class EnrollmentEvent extends Event
{
    public const ENROLLMENT = "course.enrollment";
    public const UNENROLLMENT = "course.unenrollment";

    public function __construct(private Enrollment $enrollment)
    {
    }

    public function getEnrollment()
    {
        return $this->enrollment;
    }
}
