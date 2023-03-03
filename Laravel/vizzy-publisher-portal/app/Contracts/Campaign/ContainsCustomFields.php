<?php

namespace App\Contracts\Campaign;

interface ContainsCustomFields
{
    /**
     * Get the custom fields.
     * 
     * @return array
     */
    public function getCustomFields();
}