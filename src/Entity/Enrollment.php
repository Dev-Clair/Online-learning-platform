<?php

namespace App\Entity;

use App\Entity\Users\Student;
use App\Repository\EnrollmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: EnrollmentRepository::class)]
class Enrollment
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateEnrolled = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $dateCompleted = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    private ?Courses $courses = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastAccessed = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function getDateEnrolled(): ?\DateTimeImmutable
    {
        return $this->dateEnrolled;
    }

    public function setDateEnrolled(\DateTimeImmutable $dateEnrolled): static
    {
        $this->dateEnrolled = $dateEnrolled;

        return $this;
    }

    public function getDateCompleted(): ?\DateTimeImmutable
    {
        return $this->dateCompleted;
    }

    public function setDateCompleted(\DateTimeImmutable $dateCompleted): static
    {
        $this->dateCompleted = $dateCompleted;

        return $this;
    }

    public function getLastAccessed(): ?\DateTimeImmutable
    {
        return $this->lastAccessed;
    }

    public function setLastAccessed(?\DateTimeImmutable $lastAccessed): static
    {
        $this->lastAccessed = $lastAccessed;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getCourses(): ?Courses
    {
        return $this->courses;
    }

    public function setCourses(?Courses $courses): static
    {
        $this->courses = $courses;

        return $this;
    }
}
