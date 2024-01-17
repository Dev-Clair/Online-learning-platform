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
    private ?\DateTimeImmutable $enrolledDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completionDate = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    private ?Student $student = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    private ?Courses $courses = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function getEnrolledDate(): ?\DateTimeImmutable
    {
        return $this->enrolledDate;
    }

    public function setEnrolledDate(\DateTimeImmutable $enrolledDate): static
    {
        $this->enrolledDate = $enrolledDate;

        return $this;
    }

    public function getCompletionDate(): ?\DateTimeImmutable
    {
        return $this->completionDate;
    }

    public function setCompletionDate(\DateTimeImmutable $completionDate): static
    {
        $this->completionDate = $completionDate;

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
