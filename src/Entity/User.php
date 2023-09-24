<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 75)]
    private ?string $firstname = null;

    #[ORM\Column(length: 75)]
    private ?string $lastname = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Gender $fk_gender = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $fk_company = null;

    #[ORM\Column]
    private ?bool $is_company_admin = null;

    #[ORM\OneToMany(mappedBy: 'fk_user', targetEntity: Consents::class, orphanRemoval: true)]
    private Collection $consents;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column]
    private ?bool $adverts_auth = null;

    #[ORM\Column]
    private ?bool $articles_auth = null;

    #[ORM\Column]
    private ?bool $petitions_auth = null;

    public function __construct()
    {
        $this->consents = new ArrayCollection();
    }

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
        $this->firstname = $firstname;

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

    public function getFullname(): string
    {
        return (string) $this->firstname . ' ' . $this->lastname;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getFkCompany(): ?Company
    {
        return $this->fk_company;
    }

    public function setFkCompany(?Company $fk_company): static
    {
        $this->fk_company = $fk_company;

        return $this;
    }

    public function isIsCompanyAdmin(): ?bool
    {
        return $this->is_company_admin;
    }

    public function setIsCompanyAdmin(bool $is_company_admin): static
    {
        $this->is_company_admin = $is_company_admin;

        return $this;
    }

    /**
     * @return Collection<int, Consents>
     */
    public function getConsents(): Collection
    {
        return $this->consents;
    }

    public function addConsent(Consents $consent): static
    {
        if (!$this->consents->contains($consent)) {
            $this->consents->add($consent);
            $consent->setFkUser($this);
        }

        return $this;
    }

    public function removeConsent(Consents $consent): static
    {
        if ($this->consents->removeElement($consent)) {
            // set the owning side to null (unless already changed)
            if ($consent->getFkUser() === $this) {
                $consent->setFkUser(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function isAdvertsAuth(): ?bool
    {
        return $this->adverts_auth;
    }

    public function setAdvertsAuth(bool $adverts_auth): static
    {
        $this->adverts_auth = $adverts_auth;

        return $this;
    }

    public function isArticlesAuth(): ?bool
    {
        return $this->articles_auth;
    }

    public function setArticlesAuth(bool $articles_auth): static
    {
        $this->articles_auth = $articles_auth;

        return $this;
    }

    public function isPetitionsAuth(): ?bool
    {
        return $this->petitions_auth;
    }

    public function setPetitionsAuth(bool $petitions_auth): static
    {
        $this->petitions_auth = $petitions_auth;

        return $this;
    }
}
