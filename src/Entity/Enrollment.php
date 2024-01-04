<?php

namespace App\Entity;

use App\Repository\EnrollmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnrollmentRepository::class)]
class Enrollment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $enrolledDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completionDate = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    private ?Courses $courses = null;

    public function getId(): ?int
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
