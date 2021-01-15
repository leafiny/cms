<?php

declare(strict_types=1);

/**
 * Class Contact_Page_Contact_Post
 */
class Contact_Page_Contact_Post extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        $form = $this->getPost();

        if (!$form->hasData()) {
            $this->redirect($this->getUrl('/contact.html'));
        }

        $this->setTmpSessionData('form_post_data', $form);

        /** @var Contact_Model_Message $message */
        $message = App::getObject('model', 'contact_message');
        $error = $message->validate($form);

        if ($this->isFormCodeRequired()) {
            $formCode = $this->getTmpSessionData('form_code') ?: '';
            if (empty($formCode) || strtolower($formCode) !== strtolower($form->getData('form_code') ?: '')) {
                $error = 'Invalid security code';
            }
        }

        if (!empty($error)) {
            $this->setErrorMessage($this->translate($error));
            $this->redirect($this->getUrl('/contact.html'));
        }

        /** @var Core_Mail $mail */
        $mail = App::getObject('mail', 'contact');
        $mail->setSenderName($form->getData('name'));
        $mail->setReplyTo($form->getData('email'));

        try {
            $data = new Leafiny_Object();
            $data->setData(
                [
                    'name'    => $form->getData('name'),
                    'email'   => $form->getData('email'),
                    'message' => $form->getData('message'),
                ]
            );
            $message->save($data);

            $mail->send(['form' => $form]);
            $this->setSuccessMessage(
                $this->translate('Your message was sent successfully')
            );
            $this->unsTmpSessionData('form_post_data');
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->setErrorMessage(
                $this->translate('An error occurred when sending message')
            );
        }

        $this->redirect($this->getUrl('/contact.html'));
    }

    /**
     * Is Form code required
     *
     * @return bool
     */
    public function isFormCodeRequired(): bool
    {
        return (bool)$this->getCustom('form_code_required');
    }
}
