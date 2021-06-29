<?php
/**
 * This file is part of Leafiny.
 *
 * Copyright (C) Magentix SARL
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Class Core_Observer
 */
class Core_Observer extends Leafiny_Object
{
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

    /**
     * Retrieve object identifier
     *
     * @return string|null
     */
    public function getObjectIdentifier(): ?string
    {
        return $this->getData('object_identifier');
    }
}
