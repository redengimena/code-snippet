<?php

namespace App\Campaign\Managers\ActiveCampaign;

interface Tag
{
    /**
     * Get the text value of the tag.
     * 
     * @return string
     */
    public function text();
}