<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Warranty::class, mappedBy: 'tags')]
    private Collection $warranties;

    public function __construct()
    {
        $this->warranties = new ArrayCollection();
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

    /**
     * @return Collection<int, Warranty>
     */
    public function getWarranties(): Collection
    {
        return $this->warranties;
    }

    public function addWarranty(Warranty $warranty): static
    {
        if (!$this->warranties->contains($warranty)) {
            $this->warranties->add($warranty);
            $warranty->addTag($this);
        }

        return $this;
    }

    public function removeWarranty(Warranty $warranty): static
    {
        if ($this->warranties->removeElement($warranty)) {
            $warranty->removeTag($this);
        }

        return $this;
    }
}
