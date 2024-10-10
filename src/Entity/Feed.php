<?php

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedRepository::class)]
class Feed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $googleId = null;

    #[ORM\Column(length: 500)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $link = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $published = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $author = null;

    #[ORM\Column(options: ["default" => false])]
    private bool $markerDone = false;

    #[ORM\Column(length: 255)]
    private ?string $source_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(string $googleId): static
    {
        $this->googleId = $googleId;

        return $this;
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

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

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

    public function getPublished(): ?\DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(\DateTimeInterface $published): static
    {
        $this->published = $published;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function isMarkerDone(): bool
    {
        return $this->markerDone;
    }

    public function setMarkerDone(bool $markerDone): static
    {
        $this->markerDone = $markerDone;

        return $this;
    }

    public function getSourceName(): ?string
    {
        return $this->source_name;
    }

    public function setSourceName(string $source_name): static
    {
        $this->source_name = $source_name;

        return $this;
    }
    
    public function getBlackfireMarkerTitle(): string
    {
        return $this->getSourceName().'|'.$this->getTitle();
    }
}
