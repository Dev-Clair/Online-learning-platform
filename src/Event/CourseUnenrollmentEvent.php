<?php

namespace App\Event;

use App\Entity\Enrollment;
use Symfony\Contracts\EventDispatcher\Event;

class CourseunEnrollmentEvent extends Event
{
    public const NAME = "course.unenrollment";

    public function __construct(private Enrollment $enrollment)
    {
    }

    public function getEnrollment()
    {
        return $this->enrollment;
    }
}
