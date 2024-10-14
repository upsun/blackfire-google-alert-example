<?php

namespace App\Entity;

use App\Repository\RssFeedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: RssFeedRepository::class)]
class RssFeed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 1024)]
    private ?string $url = null;

    #[ORM\Column]
    private ?bool $active = null;

    /**
     * @var Collection<int, Feed>
     */
    #[ORM\OneToMany(targetEntity: Feed::class, mappedBy: 'rssFeed', orphanRemoval: true)]
    #[ORM\OrderBy(["published" => "DESC"])]
    private Collection $feeds;

    public function __construct()
    {
        $this->feeds = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
        
    /**
     * @return Collection<int, Feed>
     */
    public function getFeeds(): Collection
    {
        return $this->feeds;
    }
    
    public function addFeed(Feed $feed): static
    {
        if (!$this->feeds->contains($feed)) {
            $this->feeds->add($feed);
            $feed->setRssFeed($this);
        }
    
        return $this;
    }
    
    public function removeFeed(Feed $feed): static
    {
        if ($this->feeds->removeElement($feed)) {
            // set the owning side to null (unless already changed)
            if ($feed->getRssFeed() === $this) {
                $feed->setRssFeed(null);
            }
        }
    
        return $this;
    }
}
