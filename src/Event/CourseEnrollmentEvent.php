<?php

namespace App\Event;

use App\Entity\Enrollment;
use Symfony\Contracts\EventDispatcher\Event;

class CourseEnrollmentEvent extends Event
{
    public const NAME = "course.enrollment";

    public function __construct(private Enrollment $enrollment)
    {
    }

    public function getEnrollment()
    {
        return $this->enrollment;
    }
}
