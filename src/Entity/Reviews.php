<?php

namespace App\Entity;

use App\Entity\Users\Student;
use App\Repository\ReviewsRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewsRepository::class)]
class Reviews
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[Assert\NotBlank(message: "Name field cannot be blank")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z]+(\s[a-zA-Z]+)?$/",
        message: "Name must contain only letters"
    )]
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $name = null;

    #[Assert\NotBlank(message: "Email field cannot be blank")]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    #[ORM\Column(length: 255, unique: false, nullable: false)]
    private ?string $email = null;

    #[Assert\NotBlank(message: "Message field cannot be blank")]
    #[ORM\Column(length: 255)]
    private ?string $review = null;

    #[Slug(fields: ['name'])]
    #[ORM\Column(length: 255)]
    private ?string $reviewslug = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?Courses $course = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?Student $student = null;

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = ucwords($name);

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(string $review): static
    {
        $this->review = $review;

        return $this;
    }

    public function getReviewslug(): ?string
    {
        return $this->reviewslug;
    }

    public function setReviewslug(?string $reviewslug): static
    {
        $this->reviewslug = $reviewslug;

        return $this;
    }

    public function getCourse(): ?Courses
    {
        return $this->course;
    }

    public function setCourse(?Courses $course): static
    {
        $this->course = $course;

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
}
