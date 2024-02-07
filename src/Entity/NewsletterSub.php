<?php

namespace App\Entity;

use App\Repository\NewsletterSubRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsletterSubRepository::class)]
class NewsletterSub
{
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
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function getId(): string
    {
        return $this->id;
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
}
