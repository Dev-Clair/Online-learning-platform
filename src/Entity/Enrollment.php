<?php

namespace App\Entity;

use App\Repository\EnrollmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(inversedBy: 'enrolledCourses')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'enrollment', targetEntity: Progress::class)]
    private Collection $progress;

    #[ORM\ManyToOne(inversedBy: 'enrollments')]
    private ?Courses $courses = null;

    public function __construct()
    {
        $this->progress = new ArrayCollection();
    }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Progress>
     */
    public function getProgress(): Collection
    {
        return $this->progress;
    }

    public function addProgress(Progress $progress): static
    {
        if (!$this->progress->contains($progress)) {
            $this->progress->add($progress);
            $progress->setEnrollment($this);
        }

        return $this;
    }

    public function removeProgress(Progress $progress): static
    {
        if ($this->progress->removeElement($progress)) {
            // set the owning side to null (unless already changed)
            if ($progress->getEnrollment() === $this) {
                $progress->setEnrollment(null);
            }
        }

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
