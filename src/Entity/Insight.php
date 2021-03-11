<?php

namespace App\Entity;

use App\Repository\InsightRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InsightRepository::class)
 */
class Insight
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Feedback::class, inversedBy="insights")
     * @ORM\JoinColumn(nullable=false)
     */
    private $feedback;

    /**
     * @ORM\ManyToOne(targetEntity=Feature::class, inversedBy="insights")
     * @ORM\JoinColumn(nullable=false)
     */
    private $feature;

    /**
     * @ORM\ManyToOne(targetEntity=InsightWeight::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $weight;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback): self
    {
        $this->feedback = $feedback;

        return $this;
    }

    public function getFeature(): ?Feature
    {
        return $this->feature;
    }

    public function setFeature(?Feature $feature): self
    {
        $this->feature = $feature;

        return $this;
    }

    public function getWeight(): ?InsightWeight
    {
        return $this->weight;
    }

    public function setWeight(?InsightWeight $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
}
