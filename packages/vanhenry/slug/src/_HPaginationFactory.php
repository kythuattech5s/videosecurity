<?php

namespace vth\slug;
use Illuminate\Pagination;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\Router;

class HpaginationFactory extends Pagination\Factory
{
    /**
     * Create a new Skeleton Instance
     */
    public function __construct()
    {
        // constructor body
    }

    /**
     * Friendly welcome
     *
     * @param string $phrase Phrase to return
     *
     * @return string Returns the phrase passed in
     */
    public function echoPhrase($phrase)
    {
        return $phrase;
    }
}
