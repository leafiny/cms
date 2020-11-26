<?php

declare(strict_types=1);

/**
 * Class Core_Observer_Abstract
 */
abstract class Core_Observer_Abstract extends Leafiny_Object
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    abstract public function execute(Leafiny_Object $object): void;

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
        $custom = $this->getData('custom');

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
