<?php

namespace App\Entity\Users;

use App\Entity\Profile;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Gedmo\Mapping\Annotation\Slug;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[MappedSuperclass()]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[Assert\NotBlank(message: "First name field cannot be blank")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\-]+$/",
        message: "First name must contain only letters"
    )]
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $firstName = null;

    #[Assert\NotBlank(message: "Last name field cannot be blank")]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\-]+$/",
        message: "Last name must contain only letters"
    )]
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $lastName = null;

    #[Assert\NotBlank(message: "Email field cannot be blank")]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    #[ORM\Column(length: 180, unique: true, nullable: false)]
    private ?string $email = null;

    #[ORM\Column(nullable: false)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[Slug(fields: ['firstName', 'lastName'])]
    #[ORM\Column(length: 255)]
    private ?string $userslug = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private Profile $profile;

    public function __construct()
    {
        // Create and assign new profile to user on instantiation (user account creation)
        $this->profile = new Profile;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstName;
    }

    public function setFirstname(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastName;
    }

    public function setLastname(string $lastName): static
    {
        $this->lastName = $lastName;

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getUserSlug(): ?string
    {
        return $this->userslug;
    }

    public function setUserSlug(string $userslug): static
    {
        $this->userslug = $userslug;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }
}
