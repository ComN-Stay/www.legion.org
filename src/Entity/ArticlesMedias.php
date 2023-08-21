<?php

namespace App\Entity;

use App\Repository\ArticlesMediasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticlesMediasRepository::class)]
class ArticlesMedias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'articleMedias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Articles $fk_article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getFkArticle(): ?Articles
    {
        return $this->fk_article;
    }

    public function setFkArticle(?Articles $fk_article): static
    {
        $this->fk_article = $fk_article;

        return $this;
    }
}
