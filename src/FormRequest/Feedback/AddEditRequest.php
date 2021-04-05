<?php


namespace App\FormRequest\Feedback;


use App\Entity\Feedback;
use Symfony\Component\Validator\Constraints as Assert;


class AddEditRequest
{

    /**
     * @Assert\NotBlank()
     */
    public $description;

    public $source;

    public static function fromFeedback(Feedback $feedback)
    {
        $feedbackRequest = new self();

        $feedbackRequest->description = $feedback->getDescription();
        $feedbackRequest->source = $feedback->getSource();

        return $feedbackRequest;
    }

    public static function fromArray(Array $array)
    {
        $feedbackRequest = new self();

        $feedbackRequest->description = $array['description'];
        $feedbackRequest->source = $array['source'];

        return $feedbackRequest;
    }

}