<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $short_description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_add = null;

    #[ORM\ManyToOne]
    private ?Team $fk_team = null;

    #[ORM\ManyToOne]
    private ?User $fk_user = null;

    #[ORM\Column]
    private ?int $visits = null;

    #[ORM\ManyToMany(targetEntity: Tags::class, inversedBy: 'articles')]
    private Collection $tags;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta_description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta_keywords = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\OneToMany(mappedBy: 'fk_article', targetEntity: ArticlesMedias::class, orphanRemoval: true)]
    private Collection $articleMedias;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Status $fk_status = null;

    #[ORM\Column]
    private ?int $nb_likes = null;

    #[ORM\Column]
    private ?int $nb_shares = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'articles_likes')]
    private Collection $likes;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'articles_shares')]
    private Collection $shares;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->articleMedias = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->shares = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->short_description;
    }

    public function setShortDescription(string $short_description): static
    {
        $this->short_description = $short_description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->date_add;
    }

    public function setDateAdd(\DateTimeInterface $date_add): static
    {
        $this->date_add = $date_add;

        return $this;
    }

    public function getFkTeam(): ?Team
    {
        return $this->fk_team;
    }

    public function setFkTeam(?Team $fk_team): static
    {
        $this->fk_team = $fk_team;

        return $this;
    }

    public function getFkUser(): ?User
    {
        return $this->fk_user;
    }

    public function setFkUser(?User $fk_user): static
    {
        $this->fk_user = $fk_user;

        return $this;
    }

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(int $visits): static
    {
        $this->visits = $visits;

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getMetaName(): ?string
    {
        return $this->meta_name;
    }

    public function setMetaName(string $meta_name): static
    {
        $this->meta_name = $meta_name;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(string $meta_description): static
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords(?string $meta_keywords): static
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, ArticlesMedias>
     */
    public function getArticleMedias(): Collection
    {
        return $this->articleMedias;
    }

    public function addArticleMedia(ArticlesMedias $articleMedia): static
    {
        if (!$this->articleMedias->contains($articleMedia)) {
            $this->articleMedias->add($articleMedia);
            $articleMedia->setFkArticle($this);
        }

        return $this;
    }

    public function removeArticleMedia(ArticlesMedias $articleMedia): static
    {
        if ($this->articleMedias->removeElement($articleMedia)) {
            // set the owning side to null (unless already changed)
            if ($articleMedia->getFkArticle() === $this) {
                $articleMedia->setFkArticle(null);
            }
        }

        return $this;
    }

    public function getFkStatus(): ?Status
    {
        return $this->fk_status;
    }

    public function setFkStatus(?Status $status): static
    {
        $this->fk_status = $status;

        return $this;
    }

    public function getNbLikes(): ?int
    {
        return $this->nb_likes;
    }

    public function setNbLikes(int $nb_likes): static
    {
        $this->nb_likes = $nb_likes;

        return $this;
    }

    public function getNbShares(): ?int
    {
        return $this->nb_shares;
    }

    public function setNbShares(int $nb_shares): static
    {
        $this->nb_shares = $nb_shares;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }

        return $this;
    }

    public function removeLike(User $like): static
    {
        $this->likes->removeElement($like);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getShares(): Collection
    {
        return $this->shares;
    }

    public function addShare(User $share): static
    {
        if (!$this->shares->contains($share)) {
            $this->shares->add($share);
        }

        return $this;
    }

    public function removeShare(User $share): static
    {
        $this->shares->removeElement($share);

        return $this;
    }
}
