<?php

declare(strict_types=1);

/**
 * Class Backend_Page_Admin_Form_Save
 */
class Backend_Page_Admin_Form_Save extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $post = $this->getPost();

        if (empty($post->getData())) {
            $this->redirect($this->getRefererUrl(), true);
        }

        foreach ($post->getData() as $field => $value) {
            $post->setData($field, is_object($value) ? $value : trim((string)$value));
        }

        $data = clone $post;

        $this->setTmpSessionData('form_post_data', $data);
        $this->validate($post);

        try {
            $objectId = $this->getModel()->save($post);
            $this->setSuccessMessage(App::translate('Data has been successfully saved'));
            App::dispatchEvent(
                'backend_object_save_after',
                [
                    'data'       => $data,
                    'identifier' => $this->getModelIdentifier(),
                    'object_id'  => $objectId
                ]
            );
            $this->unsTmpSessionData('form_post_data');
            $redirect = $this->getCustom('redirect_identifier');
            if ($redirect === null) {
                $redirect = $this->getPathName($this->getObjectIdentifier()) . '?id=' . $objectId;
            }
            $this->redirect($redirect);
        } catch (Exception $exception) {
            $this->setErrorMessage(App::translate($exception->getMessage()));
        }

        $this->redirect($this->getRefererUrl(), true);
    }

    /**
     * Retrieve only POST params (do not strip tags)
     *
     * @return Leafiny_Object
     */
    public function getPost(): Leafiny_Object
    {
        $object = new Leafiny_Object;
        $post   = $_POST;

        if (!empty($post)) {
            foreach ($post as $field => $value) {
                if (is_array($value)) {
                    $data = new Leafiny_Object;
                    $object->setData($field, $data->setData($value));
                } else {
                    $object->setData($field, $value);
                }
            }
        }

        return $object;
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
     * Form validation
     *
     * @param Leafiny_Object $post
     */
    public function validate(Leafiny_Object $post): void {}

    /**
     * Set error message and redirect
     *
     * @param string $message
     *
     * @return void
     */
    public function setErrorMessage(string $message): void
    {
        parent::setErrorMessage($message);

        $this->redirect($this->getRefererUrl(), true);
    }
}
