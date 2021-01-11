<?php

declare(strict_types=1);

/**
 * Class Core_Object_Factory
 */
class Core_Object_Factory
{
    /**
     * Retrieve Object
     *
     * @param string      $type
     * @param string|null $identifier
     *
     * @return Object|Leafiny_Object
     */
    public function getObject(string $type, ?string $identifier = null): Object
    {
        $params = null;
        $key    = null;
        $class  = $this->getClass($type);
        $config = $this->getConfig($type);

        if ($identifier !== null) {
            $extract = new Leafiny_Object();
            $extract->setData('identifier', $identifier);

            if ($this->canDispatchEvent($type)) {
                App::dispatchEvent(
                    $type . '_identifier_extract_before',
                    ['identifier' => $identifier, 'extract' => $extract]
                );
            }

            $extract->setData(
                $this->extract($type, $extract->getData('identifier'))
            );

            if ($this->canDispatchEvent($type)) {
                App::dispatchEvent(
                    $type . '_identifier_extract_after',
                    ['identifier' => $identifier, 'extract' => $extract]
                );
            }

            $params     = $extract->getData('params');
            $key        = $extract->getData('key');
            $identifier = $extract->getData('identifier');

            $config = $this->getConfig($type, $identifier);

            if (isset($config['class'])) {
                if ($config['class'] && class_exists($config['class'])) {
                    $class = $config['class'];
                }
            }

            if (isset($config['disabled'])) {
                if ($config['disabled']) {
                    return new $class();
                }
            }
        }

        /* @var $object Leafiny_Object */
        $object = new $class();

        if ($object instanceof Leafiny_Object) {
            $configObject = new Leafiny_Object();
            $configObject->setData($config);
            $object->setData('custom', $configObject);

            if ($params) {
                parse_str($params, $params);
            }

            $object->setData('object_type', $type);
            $object->setData('object_identifier', $identifier);
            $object->setData('object_key', $key);
            $object->setData('object_params', new Leafiny_Object($params));
        }

        if ($this->canDispatchEvent($type)) {
            App::dispatchEvent('object_init_after', ['object' => $object]);
            App::dispatchEvent($type . '_object_init_after', ['object' => $object]);
        }

        $this->process($object, $type);

        return $object;
    }

    /**
     * Retrieve params (?) and key (*) from identifier
     *
     * @param string $type
     * @param string $identifier
     *
     * @return string[]
     */
    protected function extract(string $type, string $identifier): array
    {
        $key    = null;
        $params = null;

        if (preg_match('/\\?(?P<params>.*)/', $identifier, $matches)) {
            $params = $matches['params'];
            $identifier = preg_replace('/\\?.*/', '', $identifier);
        }

        if (!$this->isAllowed($identifier)) {
            return [
                'key'        => $key,
                'params'     => $params,
                'identifier' => ''
            ];
        }

        $identifiers = App::getIdentifiers($type);

        foreach ($identifiers as $value) {
            $parts = explode('*', $value);

            if (count($parts) !== 2) {
                continue;
            }

            $regex = '/^' . preg_quote($parts[0], '/') . '(?P<key>.*)' . preg_quote($parts[1], '/') . '$/';

            if (!preg_match($regex, $identifier, $matches)) {
                continue;
            }

            if (empty($matches['key'])) {
                continue;
            }

            $key = $matches['key'];
            $identifier = $parts[0] . '*' . $parts[1];

            break;
        }

        return [
            'key'        => $key,
            'params'     => $params,
            'identifier' => $identifier
        ];
    }

    /**
     * Check if identifier is allowed. Asterisk is reserved for key.
     *
     * @param string $identifier
     *
     * @return bool
     */
    protected function isAllowed(string $identifier): bool
    {
        return !(bool)preg_match('/[*<>%]/', $identifier);
    }

    /**
     * Retrieve merged data from config files
     *
     * @param string $type
     * @param string|null $identifier
     *
     * @return mixed[]
     */
    protected function getConfig(string $type, ?string $identifier = null): array
    {
        if ($identifier === null) {
            return array_merge(
                App::getConfig('default', []), // default.default
                App::getConfig($type . '.' . 'default', []) // model.default
            );
        }

        return array_merge(
            App::getConfig('default', []), // default.default
            App::getConfig('default' . '.' . $identifier, []), // default.identifier
            App::getConfig($type . '.' . 'default', []), // model.default
            App::getConfig($type . '.' . $identifier, []) // model.identifier
        );
    }

    /**
     * Process
     *
     * @param Object|Leafiny_Object $object
     * @param string $type
     *
     * @return void
     */
    protected function process(Object $object, string $type): void
    {
        if (method_exists($object, 'preProcess')) {
            $object->preProcess();
        }

        if (method_exists($object, 'process')) {
            if ($this->canDispatchEvent($type)) {
                App::dispatchEvent(
                    $type . '_process_before',
                    ['object' => $object]
                );
            }

            $object->process();

            if ($this->canDispatchEvent($type)) {
                App::dispatchEvent(
                    $type . '_process_after',
                    ['object' => $object]
                );
            }
        }

        if (method_exists($object, 'postProcess')) {
            $object->postProcess();
        }
    }

    /**
     * Can Dispatch Event
     *
     * @param string $type
     *
     * @return bool
     */
    public function canDispatchEvent(string $type): bool
    {
        return $type !== 'event';
    }

    /**
     * Retrieve default class name
     *
     * @param string $type
     *
     * @return string
     */
    protected function getClass(string $type): string
    {
        if (class_exists('Core_' . $this->format($type))) { // Core_Type
            $class = 'Core_' . $this->format($type);
        } else { // Leafiny_Object
            $class = Leafiny_Object::class;
        }

        return $class;
    }

    /**
     * Format String
     *
     * @param string $string
     *
     * @return string
     */
    protected function format(string $string): string
    {
        $string = ucwords(strtolower(preg_replace('/_/', ' ', $string)));

        return preg_replace('/ /', '_', $string);
    }
}
