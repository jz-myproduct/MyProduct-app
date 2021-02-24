<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FeedbackRepository::class)
 */
class Feedback
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="feedback")
     */
    private $company;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity=Feature::class)
     */
    private $feature;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fromPortal;

    public function __construct()
    {
        $this->feature = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PostUpdate
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getStatuses(): array
    {
        return ['new', 'active'];
    }

    public function setNewStatus(): self
    {
        $this->status = 'new';

        return $this;
    }

    public function setActiveStatus(): self
    {
        $this->status = 'active';

        return $this;
    }

    public function switchStatus(): self
    {
        if($this->getStatus() === 'new')
        {
            $this->setActiveStatus();
            return $this;
        }

        if($this->getStatus() === 'active')
        {
            $this->setNewStatus();
            return $this;
        }

        /* TODO tady by to asi chtělo spíše hodit exception */
        return $this;
    }

    /**
     * @return Collection|Feature[]
     */
    public function getFeature(): Collection
    {
        return $this->feature;
    }

    public function addFeature(Feature $feature): self
    {
        if (!$this->feature->contains($feature)) {
            $this->feature[] = $feature;
        }

        return $this;
    }

    public function removeFeature(Feature $feature): self
    {
        $this->feature->removeElement($feature);

        return $this;
    }

    public function getFromPortal(): ?bool
    {
        return $this->fromPortal;
    }

    public function setFromPortal(bool $fromPortal): self
    {
        $this->fromPortal = $fromPortal;

        return $this;
    }

}
