<?php

declare(strict_types=1);

/**
 * Class Backend_Model_Cache
 */
class Backend_Model_Cache extends Leafiny_Object
{
    /**
     * Retrieve caches
     *
     * @return Leafiny_Object[]
     */
    public function get(): array
    {
        /** @var array[] $refresh */
        $cache = $this->getCustom('cache');

        $caches = [];

        if (!is_array($cache)) {
            return $caches;
        }

        foreach ($cache as $data) {
            if (!isset($data['name'], $data['flush'])) {
                continue;
            }

            if (isset($data['active'])) {
                $data['status'] = is_string($data['active']) ? App::getConfig($data['active']) : $data['active'];
            }

            $caches[] = new Leafiny_Object($data);
        }

        return $caches;
    }

    /**
     * Get custom data
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getCustom(string $key)
    {
        /** @var Leafiny_Object $custom */
        $custom = $this->getData(Core_Object_Factory::CUSTOM_KEY);

        if (!$custom) {
            return null;
        }

        return $custom->getData($key);
    }
}
