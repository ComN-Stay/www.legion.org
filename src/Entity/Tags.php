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
}
