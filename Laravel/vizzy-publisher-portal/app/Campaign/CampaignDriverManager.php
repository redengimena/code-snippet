<?php

namespace App\Campaign;

use Illuminate\Support\Manager;

class CampaignDriverManager extends Manager
{
    /**
     * Create a new ActiveCampain driver instance.
     *
     * @return \App\Campaign\Managers\ActiveCampaign
     */
    protected function createActivecampaignDriver()
    {
        $config = $this->getConfig('activecampaign');

        return new Managers\ActiveCampaign($config['url'], $config['key'], $config);
    }

    /**
     * Get the default campaign driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->container['config']->get('campaign.default');
    }

    /**
     * Get the configuration of the given manager.
     *
     * @param string $manager
     * @return array
     */
    protected function getConfig($manager)
    {
        return $this->container['config']->get("campaign.managers.$manager", []);
    }

    /**
     * Get the campaign manager given its name.
     *
     * @param string $name (optional)
     * @return \App\Contracts\Campaign\CampaignManager
     */
    public function manager($name = null)
    {
        return $this->driver($name);
    }
}