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
    private $Feedback;

    /**
     * @ORM\ManyToOne(targetEntity=Feature::class, inversedBy="insights")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Feature;

    /**
     * @ORM\ManyToOne(targetEntity=FeedbackValue::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeedback(): ?Feedback
    {
        return $this->Feedback;
    }

    public function setFeedback(?Feedback $Feedback): self
    {
        $this->Feedback = $Feedback;

        return $this;
    }

    public function getFeature(): ?Feature
    {
        return $this->Feature;
    }

    public function setFeature(?Feature $Feature): self
    {
        $this->Feature = $Feature;

        return $this;
    }

    public function getValue(): ?FeedbackValue
    {
        return $this->value;
    }

    public function setValue(?FeedbackValue $value): self
    {
        $this->value = $value;

        return $this;
    }
}
