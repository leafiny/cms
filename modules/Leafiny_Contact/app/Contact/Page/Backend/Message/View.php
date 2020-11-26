<?php

declare(strict_types=1);

/**
 * Class Contact_Page_Backend_Message_View
 */
class Contact_Page_Backend_Message_View extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Message Data
     *
     * @var Leafiny_Object|null $message
     */
    protected $message = null;

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
            $this->message = new Leafiny_Object();
            return;
        }

        try {
            $model = $this->getModel();
            $message = $model->get($params->getData('id'));
            if (!$message->hasData()) {
                $this->setErrorMessage($this->translate('This element no longer exists'));
                $this->redirect($this->getRefererUrl(), true);
            }
            if (!$message->getData('is_open')) {
                $message->setData('is_open', 1);
                $model->save($message);
            }
            $this->message = $message;
            App::dispatchEvent('backend_contact_message_view', ['message' => $message]);
        } catch (Exception $exception) {
            $this->setErrorMessage($exception->getMessage());
            $this->message = new Leafiny_Object();
        }
    }

    /**
     * Retrieve current message
     *
     * @return Leafiny_Object|null
     */
    public function getMessage(): ?Leafiny_Object
    {
        return $this->message;
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
