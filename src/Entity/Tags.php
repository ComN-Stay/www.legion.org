<?php

namespace App\Entity;

use App\Repository\TagsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagsRepository::class)]
class Tags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $article_qte = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 75, nullable: true)]
    private ?string $meta_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta_description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meta_keyword = null;

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

    public function getArticleQte(): ?int
    {
        return $this->article_qte;
    }

    public function setArticleQte(int $article_qte): static
    {
        $this->article_qte = $article_qte;

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

    public function getMetaName(): ?string
    {
        return $this->meta_name;
    }

    public function setMetaName(?string $meta_name): static
    {
        $this->meta_name = $meta_name;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(?string $meta_description): static
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    public function getMetaKeyword(): ?string
    {
        return $this->meta_keyword;
    }

    public function setMetaKeyword(?string $meta_keyword): static
    {
        $this->meta_keyword = $meta_keyword;

        return $this;
    }
}
