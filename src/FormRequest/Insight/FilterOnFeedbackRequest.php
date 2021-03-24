<?php


namespace App\FormRequest\Insight;


class FilterOnFeedbackRequest
{
    public $fulltext;

    public $tags;

    public $state;

    public static function fromArray(array $array)
    {
        $request = new self();
        $request->fulltext = $array['fulltext'];
        $request->tags = $array['tags'];
        $request->state = $array['state'];

        return $request;
    }

}