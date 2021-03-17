<?php


namespace App\FormRequest\Insight;

use App\Entity\Insight;
use Symfony\Component\Validator\Constraints as Assert;


class AddFromFeedbackRequest
{

    /**
     * @Assert\NotBlank()
     */
    public $weight;

    public static function fromInsight(Insight $insight)
    {
        $insightRequest = new AddFromFeedbackRequest();
        $insightRequest->weight = $insight->getWeight();

        return $insightRequest;
    }
}