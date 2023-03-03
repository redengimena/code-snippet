<?php

namespace App\Campaign\Managers;

use App\Contracts\Campaign\CampaignManager;
use App\Campaign\Managers\ActiveCampaign\AddableTag;
use App\Campaign\Managers\ActiveCampaign\RemovableTag;
use Illuminate\Support\Arr;
use ActiveCampaign as ActiveCampaignConnect;
use App\Models\User;

class ActiveCampaign implements CampaignManager
{
    use ActiveCampaign\HasPodcasterTags;

    /**
     * @var \ActiveCampaign
     */
    protected $ac;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $tagDelimiter = ' | ';

    /**
     * Create new instance of the active campaign manager.
     *
     * @param string $url
     * @param string $key
     * @param array $config (optional)
     */
    public function __construct($url, $key, array $config = [])
    {
        $this->config = $config;

        $this->ac = new ActiveCampaignConnect($url, $key);
        $this->ac->set_curl_timeout($this->getConfig('timeout', 15));
    }

    /**
     * Add the podcaster to default podcaster contact list.
     *
     * @param \App\Models\User $podcaster
     * @return mixed
     */
    public function addPodcasterContact(User $podcaster)
    {
        $listId = $this->getConfig('podcaster.contact_list_id');

        $data = [
            'email' => $podcaster->email,
            'first_name' => $podcaster->firstname,
            'last_name' => $podcaster->lastname,
            // Uncomment the following lines to add to the podcaster contact list
            "p[$listId]" => $listId,
            "status[$listId]" => 1
        ];


        $data['tags'] = $this->glueTags([
            $this->podcasterAddTag(['Source', 'Portal Registration Form'])->text(),
            $this->podcasterAddTag(['Account', 'Registered'])->text()
        ]);

        $this->addContact($data);
    }

    /**
     * Get configuration given its key.
     *
     * @param string $key
     * @param mixed $defaultValue (optional)
     * @return mixed
     */
    public function getConfig($key, $defaultValue = null)
    {
        return Arr::get($this->config, $key, $defaultValue);
    }


    /**
     * Use AC Api to send contact
     *
     * @param [type] $data
     * @return void
     */
    protected function addContact($data)
    {
        $this->ac->api('contact/sync', $data);
    }

    /**
     * Push (add or remove) the given list of tags to active campaign.
     *
     * @param string $email
     * @param \App\Campaign\ActiveCampaign\Tag[] $tags
     * @return void
     */
    public function pushTags($email, array $tags)
    {
        list ($add, $remove) = collect($tags)
            ->filter(function ($tag) {
                return $tag instanceof AddableTag || $tag instanceof RemovableTag;
            })->partition(function ($tag) {
                return $tag instanceof AddableTag;
            });

        $data = compact('email');

        if ($add->isNotEmpty()) {
            $this->ac->api('contact/tag/add', $data + [
                'tags' => $add->map->text()->all()
            ]);
        }

        if ($remove->isNotEmpty()) {
            $this->ac->api('contact/tag/remove', $data + [
                'tags' => $remove->map->text()->all()
            ]);
        }
    }

    /**
     * Glue multiple tags created by makeTag.
     *
     * @param array $tags
     * @return string
     */
    protected function glueTags(array $tags)
    {
        return implode(',', $tags);
    }

    /**
     * Merge the given field from the configuration.
     *
     * @param array $data
     * @param string $key
     * @param mixed $default (optional)
     * @return void
     */
    protected function mergeFieldFromConfig(array &$data, $key, $value)
    {
        if (! is_null($fieldId = $this->getConfig($key))) {
            $data["field[$fieldId,0]"] = $value;
        }
    }
}