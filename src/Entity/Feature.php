<?php

namespace App\Entity;

use App\Repository\FeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=FeatureRepository::class)
 */
class Feature
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="features")
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
     * @ORM\ManyToOne(targetEntity=FeatureState::class, inversedBy="features")
     * @ORM\JoinColumn(nullable=false)
     */
    private $state;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $score;

    /**
     * @ORM\ManyToMany(targetEntity=FeatureTag::class)
     */
    private $tags;

    /**
     * @ORM\OneToOne(targetEntity=PortalFeature::class, mappedBy="feature", cascade={"persist", "remove"})
     */
    private $portalFeature;

    /**
     * @ORM\OneToMany(targetEntity=Insight::class, mappedBy="Feature", orphanRemoval=true)
     */
    private $insights;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->insights = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getState(): ?FeatureState
    {
        return $this->state;
    }

    public function setState(?FeatureState $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function setInitialScore(): self
    {
        $this->score = 0;

        return $this;
    }

    public function setScoreDownByOne(): self
    {
        if($this->score === 0){
            return $this;
        }

        $this->score -= 1;

        return $this;
    }

    public function setScoreUpByOne(): self
    {
        $this->score += 1;

        return $this;
    }

    /**
     * @return Collection|FeatureTag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(FeatureTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(FeatureTag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getPortalFeature(): ?PortalFeature
    {
        return $this->portalFeature;
    }

    public function setPortalFeature(?PortalFeature $portalFeature): self
    {
        // unset the owning side of the relation if necessary
        if ($portalFeature === null && $this->portalFeature !== null) {
            $this->portalFeature->setFeature(null);
        }

        // set the owning side of the relation if necessary
        if ($portalFeature !== null && $portalFeature->getFeature() !== $this) {
            $portalFeature->setFeature($this);
        }

        $this->portalFeature = $portalFeature;

        return $this;
    }

    /**
     * @return Collection|Insight[]
     */
    public function getInsights(): Collection
    {
        return $this->insights;
    }

    public function addInsight(Insight $insight): self
    {
        if (!$this->insights->contains($insight)) {
            $this->insights[] = $insight;
            $insight->setFeature($this);
        }

        return $this;
    }

    public function removeInsight(Insight $insight): self
    {
        if ($this->insights->removeElement($insight)) {
            // set the owning side to null (unless already changed)
            if ($insight->getFeature() === $this) {
                $insight->setFeature(null);
            }
        }

        return $this;
    }

}
