<?php

namespace App\Campaign\Managers\ActiveCampaign;

use App\Models\User;

trait HasPodcasterTags
{
    /**
     * Create a tag for adding/attaching to a podcaster.
     *
     * @param array $parts
     * @return \App\Campaign\Managers\ActiveCampaign\AddableTag
     */
    public function podcasterAddTag(array $parts = [])
    {
        return (new AddableTag($this->tagDelimiter, ['Podcasters']))->push($parts);
    }

    /**
     * Create a tag for removing/detaching from podcaster.
     *
     * @param array $parts
     * @return \App\Campaign\Managers\ActiveCampaign\RemovableTag
     */
    public function podcasterRemoveTag(array $parts = [])
    {
        return (new RemovableTag($this->tagDelimiter, ['Podcasters']))->push($parts);
    }

    /**
     * Push the given tags for the podcaster.
     *
     * @param \App\Models\User $podcaster
     * @param array $tags
     * @return void
     */
    public function pushPodcasterTags(User $podcaster, array $tags)
    {
        return $this->pushTags($podcaster->email, $tags);
    }

}