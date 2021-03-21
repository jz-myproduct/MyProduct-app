<?php


namespace App\FormRequest\Feedback;


class ListFilterRequest
{
    public $isNew;

    public $fulltext;

    public static function fromArray(array $array)
    {
        $request = new self();
        $request->isNew = $array['isNew'];
        $request->fulltext = $array['fulltext'];

        return $request;
    }

}