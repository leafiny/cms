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
 * Class Backend_Page_Admin_List
 */
class Backend_Page_Admin_List extends Backend_Page_Admin_Page_Abstract
{
    /**
     * List size
     *
     * @var int|null $size
     */
    protected $size = null;
    /**
     * List
     *
     * @var array|null $list
     */
    protected $list = null;

    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        if ($this->list === null) {
            $this->list = [];
            try {
                $this->list = $this->getModel()->getList(
                    $this->getFilters(),
                    [$this->getSortOrder()],
                    [$this->getOffset(), $this->getLimit()]
                );
            } catch (Throwable $throwable) {
                $this->setErrorMessage($throwable->getMessage());
            }
        }
    }

    /**
     * Retrieve list
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->list;
    }

    /**
     * Retrieve limit offset
     *
     * @return int
     */
    public function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getLimit();
    }

    /**
     * Retrieve Grid action URL
     *
     * @return string
     */
    public function getActionUrl(): string
    {
        return $this->getUrl($this->getObjectIdentifier() . 'action/');
    }

    /**
     * Retrieve Grid Edit URL
     *
     * @param int $id
     *
     * @return string
     */
    public function getEditUrl(int $id): string
    {
        return $this->getUrl($this->getPathName($this->getObjectIdentifier()) . 'edit/?id=' . $id);
    }

    /**
     * Retrieve reset filter URL
     *
     * @return string
     */
    public function getResetFilterUrl(): string
    {
        return $this->getActionUrl() . '?action=reset_filter';
    }

    /**
     * Retrieve pager URL
     *
     * @param int $page
     *
     * @return string
     */
    public function getUpdatePageUrl(int $page): string
    {
        return $this->getActionUrl() . '?page=' . $page;
    }

    /**
     * Retrieve sort order URL
     *
     * @param string $column
     *
     * @return string
     */
    public function getSortOrderUrl(string $column): string
    {
        return $this->getActionUrl() . '?action=sort_order&column=' . $column;
    }

    /**
     * Retrieve buttons
     *
     * @return array
     */
    public function getButtons(): array
    {
        if ($this->getCustom('list_buttons') !== null) {
            return $this->getCustom('list_buttons');
        }

        return [
            $this->getPathName($this->getObjectIdentifier()) . 'new/' => 'Add'
        ];
    }

    /**
     * Retrieve form actions
     *
     * @return string[]
     */
    public function getActions(): array
    {
        if ($this->getCustom('list_actions') !== null) {
            return $this->getCustom('list_actions');
        }

        return [
            'remove' => 'Remove',
        ];
    }

    /**
     * Retrieve records size without limits
     *
     * @return int
     * @throws Exception
     */
    public function getSize(): int
    {
        if ($this->size === null) {
            $this->size = $this->getModel()->size($this->getFilters());
        }

        return $this->size;
    }

    /**
     * Retrieve total pages number
     *
     * @return int|null
     * @throws Exception
     */
    public function getTotalPages(): ?int
    {
        return (int)ceil($this->getSize() / $this->getLimit()) ?: 1;
    }

    /**
     * Retrieve current page
     *
     * @return int
     */
    public function getPage(): int
    {
        return App::getSession('backend')->get($this->getGridId() . '_page') ?: 1;
    }

    /**
     * Retrieve limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        $limit = $this->getCustom('default_limit') ?: 20;

        App::getSession('backend')->set($this->getGridId() . '_limit', $limit);

        return $limit;
    }

    /**
     * Retrieve current filter
     *
     * @return array
     */
    public function getFilters(): array
    {
        $default = $this->getCustom('default_filters') ?: [];

        return App::getSession('backend')->get($this->getGridId() . '_filters') ?: $default;
    }

    /**
     * Retrieve current sort order
     *
     * @return array
     */
    public function getSortOrder(): array
    {
        $default = $this->getCustom('default_sort_order');

        if (!$default) {
            $default = [
                'order' => $this->getModel()->getPrimaryKey(),
                'dir'   => 'DESC'
            ];
        }

        return App::getSession('backend')->get($this->getGridId() . '_sort_order') ?: $default;
    }

    /**
     * Retrieve filter for column
     *
     * @param string $column
     *
     * @return string
     */
    public function getFilter(string $column): string
    {
        if (isset($this->getFilters()[$column]['text'])) {
            return $this->getFilters()[$column]['text'];
        }

        return '';
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
        $gridId = $this->getCustom('grid_id');

        return $gridId ? (string)$gridId : 'list_grid_' . $this->getModelIdentifier();
    }
}
