<?php

namespace App\Campaign\Managers\ActiveCampaign;

class PodcasterTag extends DelimitedTag
{
    /**
     * Create new instance of tag for podcasters.
     *
     * @param array $parts
     */
    public function __construct(array $parts = [])
    {
        parent::__construct(' | ', $parts);

        array_unshift($this->parts, 'Podcasters');
    }
}