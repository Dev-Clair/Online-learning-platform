<?php

namespace App\Entity\Users;

use App\Entity\Enrollment;
use App\Entity\Reviews;
use App\Entity\Users\User;
use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends User
{
    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Enrollment::class)]
    private Collection $enrollments;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Reviews::class)]
    private Collection $reviews;

    public function __construct()
    {
        parent::__construct();

        $this->enrollments = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    /**
     * @return Collection<int, Enrollment>
     */
    public function getEnrollments(): Collection
    {
        return $this->enrollments;
    }

    public function addEnrollment(Enrollment $enrollments): static
    {
        if (!$this->enrollments->contains($enrollments)) {
            $this->enrollments->add($enrollments);
            $enrollments->setStudent($this);
        }

        return $this;
    }

    public function removeEnrollment(Enrollment $enrollments): static
    {
        if ($this->enrollments->removeElement($enrollments)) {
            // set the owning side to null (unless already changed)
            if ($enrollments->getStudent() === $this) {
                $enrollments->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reviews>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setStudent($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getStudent() === $this) {
                $review->setStudent(null);
            }
        }

        return $this;
    }
}
