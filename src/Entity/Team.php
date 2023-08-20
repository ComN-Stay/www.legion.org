<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message: 'L\'adresse email est obligatoire'
    )]
    #[Assert\Email(
        message: 'L\'adresse email {{ value }} n\'est pas une adresse email valide.',
    )]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{10,}$/',
        match: true,
        message: 'Le mot de passe doit comporter au moins 1 majuscule, 1 minuscule, 1 chiffre et un caractère spécial parmis @$!%*?&'
    )]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le prénom doit contenir au minimum {{ limit }} caracteres',
        maxMessage: 'C\'est un peu long pour un prénom !'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z][\' \p{L}-]*$/',
        match: true,
        message: 'Un prénom ne peut être composé que de lettres, d\'espaces, de tirets et d\'apostrophes'
    )]
    #[Assert\NotBlank(
        message: 'Le prénom est obligatoire'
    )]
    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le Nom doit contenir au minimum {{ limit }} caracteres',
        maxMessage: 'C\'est un peu long pour un Nom !'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z][\' \p{L}-]*$/',
        match: true,
        message: 'Un Nom ne peut être composé que de lettres, d\'espaces, de tirets et d\'apostrophes'
    )]
    #[Assert\NotBlank(
        message: 'Le Nom est obligatoire'
    )]
    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gender $fk_gender = null;

    #[Assert\Regex(
        pattern: '/\+?\d{1,4}?[-.\s]?\(?\d{1,3}?\)?[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,9}/',
        match: true,
        message: 'Merci de renseigner un N° de téléphone valide'
    )]
    #[ORM\Column(length: 25, nullable: true)]
    private ?string $phone = null;

    public function getId(): ?int
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
        $roles[] = 'ROLE_USER';

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
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = strtoupper($firstname);

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFkGender(): ?Gender
    {
        return $this->fk_gender;
    }

    public function setFkGender(?Gender $fk_gender): static
    {
        $this->fk_gender = $fk_gender;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFullname(): string
    {
        return (string) $this->firstname . ' ' . $this->lastname;
    }
}
