<?php
namespace App\Services;


class SlugService
{

    public function createSlug(String $value):String {

        /* remove non-alphanumeric */
        $slug = preg_replace("/[^A-Za-z0-9 ]/", '', $value);
        /* to lowercase and replace ' ' with - */
        $slug = strtolower(str_replace(" ", "-", $slug));

        return $slug;
    }

}