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
 * Class Backend_Page_Admin_List_Action
 */
class Backend_Page_Admin_List_Action extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        $params = $this->getParams();

        if (empty($params->getData())) {
            $this->redirect($this->getRefererUrl());
        }

        if ($params->getData('action') === 'reset_filter') {
            App::getSession('backend')->destroy($this->getGridId() . '_filters');
        }

        if ($params->getData('action') === 'sort_order') {
            $this->sortOrder($params);
        }

        if ($params->getData('action') === 'filter') {
            $this->filter($params);
        }

        if ($params->getData('action') === 'remove') {
            $this->remove($params);
        }

        $this->page($params);

        $this->redirect($this->getRefererUrl());
    }

    /**
     * Action Remove
     *
     * @param Leafiny_Object $post
     *
     * @return void
     */
    protected function remove(Leafiny_Object $post): void
    {
        if (!$post->getData('id')) {
            $this->setErrorMessage(App::translate('Please select items'));
            $this->redirect($this->getRefererUrl());
        }

        /** @var Leafiny_Object $param */
        $param = $post->getData('id');

        $model = $this->getModel();

        try {
            foreach ($param->getData() as $id) {
                $model->delete((int)$id);
            }
            App::dispatchEvent(
                'backend_action_remove_delete_after',
                [
                    'identifier' => $this->getModelIdentifier(),
                    'object_ids' => $param->toArray(),
                ]
            );
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            $this->redirect($this->getRefererUrl());
        }

        $this->setSuccessMessage(App::translate('Selected records have been deleted'));
    }

    /**
     * Action Sort Order
     *
     * @param Leafiny_Object $post
     *
     * @return void
     */
    protected function sortOrder(Leafiny_Object $post): void
    {
        if (!$post->getData('column')) {
            $this->redirect($this->getRefererUrl());
        }

        $column = trim($post->getData('column'));
        $dir    = 'ASC';

        /** @var array $current */
        $current = App::getSession('backend')->get($this->getGridId() . '_sort_order');

        if ($current && $current['dir'] && $current['order'] === $column) {
            if ($current['dir'] === 'ASC') {
                $dir = 'DESC';
            }
            if ($current['dir'] === 'DESC') {
                $dir = 'ASC';
            }
        }

        App::getSession('backend')->set(
            $this->getGridId() . '_sort_order',
            [
                'order' => $column,
                'dir'   => $dir
            ]
        );
    }

    /**
     * Action Filter
     *
     * @param Leafiny_Object $post
     *
     * @return void
     */
    protected function filter(Leafiny_Object $post): void
    {
        if (!$post->getData('filter')) {
            $this->redirect($this->getRefererUrl());
        }

        /** @var Leafiny_Object $param */
        $param = $post->getData('filter');

        $filters = [];
        foreach ($param->getData() as $column => $value) {
            if ($value === '') {
                continue;
            }
            $filters[$column] = [
                'column'   => $column,
                'text'     => trim($value),
                'value'    => '%' . trim($value) . '%',
                'operator' => 'LIKE',
            ];
        }

        App::getSession('backend')->set($this->getGridId() . '_filters', $filters);
        App::getSession('backend')->set($this->getGridId() . '_page', 1);
    }

    /**
     * Update page number
     *
     * @param Leafiny_Object $post
     *
     * @return void
     */
    protected function page(Leafiny_Object $post): void
    {
        if ($post->getData('page') === null) {
            return;
        }

        $page = (int)$post->getData('page');

        if ($page < 1) {
            $page = 1;
        }

        $max = (int)ceil($this->getSize() / $this->getLimit());

        if ($page > $max) {
            $page = $max;
        }

        App::getSession('backend')->set($this->getGridId() . '_page', $page);
    }

    /**
     * Retrieve limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return App::getSession('backend')->get($this->getGridId() . '_limit') ?: 20;
    }

    /**
     * Retrieve records size
     *
     * @return int
     */
    public function getSize(): int
    {
        $size = 0;

        try {
            $size = $this->getModel()->size($this->getFilters());
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
        }

        return $size;
    }

    /**
     * Retrieve current filter
     *
     * @return array
     */
    public function getFilters(): array
    {
        return App::getSession('backend')->get($this->getGridId() . '_filters') ?: [];
    }

    /**
     * Retrieve model
     *
     * @return Core_Model
     */
    public function getModel(): Core_Model
    {
        return App::getSingleton('model', $this->getModelIdentifier());
    }

    /**
     * Retrieve Model Identifier
     *
     * @return string|null
     */
    public function getModelIdentifier(): ?string
    {
        $modelIdentifier = $this->getCustom('model_identifier');

        return $modelIdentifier ? (string)$modelIdentifier : null;
    }

    /**
     * Retrieve grid id
     *
     * @return string
     */
    public function getGridId(): string
    {
        $gridId = $this->getData('grid_id');

        return $gridId ? (string)$gridId : 'list_grid_' . $this->getModelIdentifier();
    }
}
