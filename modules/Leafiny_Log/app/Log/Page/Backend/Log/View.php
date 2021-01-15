<?php

declare(strict_types=1);

/**
 * Class Log_Page_Backend_Log_View
 */
class Log_Page_Backend_Log_View extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Log Data
     *
     * @var Leafiny_Object|null $log
     */
    protected $log = null;

    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $params = $this->getParams();

        if (!$params->getData('id')) {
            $this->log = new Leafiny_Object();
            return;
        }

        try {
            $model = $this->getModel();
            $log = $model->get($params->getData('id'));
            if (!$log->hasData()) {
                $this->setErrorMessage($this->translate('This element no longer exists'));
                $this->redirect($this->getRefererUrl(), true);
            }
            $this->log = $log;
        } catch (Throwable $throwable) {
            $this->setErrorMessage($throwable->getMessage());
            $this->log = new Leafiny_Object();
        }
    }

    /**
     * Retrieve level as text
     *
     * @param int $level
     *
     * @return string
     */
    public function getLevelText(int $level): string
    {
        /** @var Log_Model_Db $log */
        $log = $this->getModel();

        return strtoupper($log->getLevelText($level));
    }

    /**
     * Retrieve current log
     *
     * @return Leafiny_Object|null
     */
    public function getLog(): ?Leafiny_Object
    {
        return $this->log;
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
}
