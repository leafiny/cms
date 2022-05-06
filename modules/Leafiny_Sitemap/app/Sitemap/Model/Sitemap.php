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
 * Class Sitemap_Model_Sitemap
 */
class Sitemap_Model_Sitemap extends Core_Model
{
    /**
     * Retrieve all URLs
     *
     * @return Leafiny_Object
     */
    public function getUrls(): Leafiny_Object
    {
        $entities = $this->getCustom('entity');

        $urls = [];

        foreach ($entities as $identifier => $config) {
            $options = $this->getOptions($identifier, $config);
            if (!$options['enabled']) {
                continue;
            }

            $items = $this->getItems($identifier, $options);

            foreach ($items as $item) {
                $type = $item->getData('language') ?: 'default';

                $urls[$type] = $urls[$type] ?? [];
                $urls[$type][] = str_replace('*', $item->getData($options['column']), $options['pattern']);
            }
        }

        return new Leafiny_Object($urls);
    }

    /**
     * Retrieve items
     *
     * @param string  $identifier
     * @param mixed[] $options
     *
     * @return Leafiny_Object[]
     */
    protected function getItems(string $identifier, array $options): array
    {
        /** @var Core_Model $model */
        $model = App::getObject('model', $identifier);

        $columns = [
            $options['column']
        ];
        if ($options['language'] ?? false) {
            $columns[] = $options['language'];
        }

        try {
            return $model->getList(
                $options['list']['filters'] ?? [],
                $options['list']['orders'] ?? [],
                null,
                [],
                $columns
            );
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }

    /**
     * Build options from config
     *
     * @param mixed[] $config
     *
     * @return mixed[]
     */
    protected function getOptions(string $identifier, array $config): array
    {
        if (!isset($config['language'])) {
            $config['language'] = 'language';
        }

        if (!isset($config['list'])) {
            $config['list'] = [];
        }
        if (!isset($config['list']['filters'])) {
            $config['list']['filters'] = $this->getDefaultFilters();
        }
        if (!isset($config['list']['orders'])) {
            $config['list']['orders'] = $this->getDefaultOrders();
        }

        if (isset($config['column'], $config['pattern'])) {
            return $config;
        }

        $rewrite = App::getConfig('model.rewrite.entity.' . $identifier);
        if (!is_array($rewrite)) {
            $config['enabled'] = false;
            return $config;
        }

        $config['column'] = $rewrite['column'];

        $config['pattern'] = $rewrite['target'];
        if ($rewrite['enabled']) {
            $config['pattern'] = $rewrite['source'];
        }

        return $config;
    }

    /**
     * Retrieve default filters
     *
     * @return array
     */
    protected function getDefaultFilters(): array
    {
        return [
            [
                'column' => 'status',
                'value'  => 1,
            ],
            [
                'column'   => 'robots',
                'value'    => '%NOINDEX%',
                'operator' => 'NOT LIKE',
            ],
            [
                'column'    => 'robots',
                'value'     => null,
                'operator'  => 'IS',
                'condition' => 'or'
            ],
        ];
    }

    /**
     * Retrieve default orders
     *
     * @return array
     */
    protected function getDefaultOrders(): array
    {
        return [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ],
        ];
    }
}
