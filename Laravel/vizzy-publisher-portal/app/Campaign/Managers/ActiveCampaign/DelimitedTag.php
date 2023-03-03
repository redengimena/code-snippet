<?php

namespace App\Campaign\Managers\ActiveCampaign;

class DelimitedTag implements Tag
{
    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var array 
     */
    protected $parts;

    /**
     * Create new instance of tag.
     * 
     * @param string $delimiter
     * @param array $parts (optional)
     */
    public function __construct($delimiter, array $parts = [])
    {
        $this->delimiter = $delimiter;
        $this->parts = $parts;
    }

    /**
     * Get parts of the tag.
     * 
     * @return array
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Push parts to the tag.
     * 
     * @param array|string $parts
     * @param string[] ...$rest
     * @return $this
     */
    public function push($parts)
    {
        if (is_string($parts)) {
            $parts = func_get_args();
        }

        $this->parts = array_merge($this->parts, (array) $parts);

        return $this;
    }

    /**
     * Clear the parts of the tag.
     * 
     * @return $this
     */
    public function clear()
    {
        $this->parts = [];

        return $this;
    }
    
    /**
     * Get the text representation of the tag.
     * 
     * @return string
     */
    public function text()
    {
        return implode($this->delimiter, $this->parts);
    }

    /**
     * Get the string representation of the tag.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }
}