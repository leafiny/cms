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
 * Class Core_Page
 */
class Core_Page extends Core_Template_Abstract implements Core_Interface_Page
{
    /**
     * Contains session var name to destroy in post render
     *
     * @var string[] $tmpSessionKeys
     */
    protected $tmpSessionKeys = [];
    /**
     * Avoid to execute code when this var is false
     *
     * @var bool $process
     */
    protected $process = true;

    /**
     * Check if action can process
     *
     * @return bool
     */
    public function canProcess(): bool
    {
        return $this->process;
    }

    /**
     * Set process flag
     *
     * @param bool $process
     */
    public function setProcess(bool $process): void
    {
        $this->process = $process;
    }

    /**
     * preProcess
     *
     * @return void
     * @throws Exception
     */
    public function preProcess(): void
    {
        App::dispatchEvent(
            $this->getContext() . '_page_pre_process',
            ['object' => $this]
        );

        if ($this->canProcess()) {
            $this->secure();
        }

        if ($this->canProcess()) {
            $this->allowParams();
        }

        if ($this->canProcess()) {
            $this->error();
        }
    }

    /**
     * postProcess
     *
     * @return void
     */
    public function postProcess(): void
    {
        App::dispatchEvent(
            $this->getContext() . '_page_post_process',
            ['object' => $this]
        );
    }

    /**
     * Dispatch
     *
     * @return void
     */
    public function process(): void
    {
        App::dispatchEvent(
            $this->getContext() . '_page_process',
            ['object' => $this]
        );

        if ($this->canProcess()) {
            $this->action();
        }
    }

    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        App::dispatchEvent(
            $this->getObjectType() . '_action',
            ['object' => $this]
        );
    }

    /**
     * Retrieve content
     *
     * @return string|null
     */
    public function getContent(): ?string
    {
        $content = (string)$this->getCustom('content');

        if (!$content) {
            return null;
        }

        return $this->getModuleFile($content, 'template');
    }

    /**
     * Retrieve only POST params
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
                    $object->setData($field, strip_tags($value));
                }
            }
        }

        return $object;
    }

    /**
     * Retrieve POST AND REQUEST params
     *
     * @return Leafiny_Object
     */
    public function getParams(): Leafiny_Object
    {
        $object = new Leafiny_Object;

        $post    = $_POST;
        $request = $_REQUEST;

        if (!empty($post)) {
            foreach ($post as $field => $value) {
                if (is_array($value)) {
                    $data = new Leafiny_Object;
                    $object->setData($field, $data->setData($value));
                } else {
                    $object->setData($field, strip_tags($value));
                }
            }
        }

        if (!empty($request)) {
            foreach ($request as $field => $value) {
                if (is_array($value)) {
                    $data = new Leafiny_Object;
                    $object->setData($field, $data->setData($value));
                } else {
                    $object->setData($field, strip_tags($value));
                }
            }
        }

        return $object;
    }

    /**
     * Retrieve identifier path name
     *
     * @param string $identifier
     *
     * @return string
     */
    public function getPathName(string $identifier): string
    {
        $path = explode('/', trim($identifier, '/'));
        array_pop($path);

        if (empty($path)) {
            return '/';
        }

        return '/' . join('/', $path) . '/';
    }

    /**
     * Redirect
     *
     * @param string|null $page
     * @param int         $code
     *
     * @return void
     */
    public function redirect(?string $page = null, int $code = 302): void
    {
        if (!$page) {
            $page = $this->getBaseUrl();
        }

        $this->setProcess(false);
        $this->setRender(false);

        if ($code === 302) {
            header('HTTP/1.1 302 Found');
        } else {
            header('HTTP/1.1 301 Moved Permanently');
        }

        header('Location: ' . $page);
        exit;
    }

    /**
     * Forward page with other identifier
     *
     * @param $identifier
     *
     * @return Core_Page
     */
    public function forward(string $identifier): Core_Page
    {
        $identifier = $this->getIdentifierPath($identifier);

        $page = App::getObject('page', $identifier);
        $this->setData($page->getData());

        App::dispatchEvent(
            $this->getObjectType() . '_forward_after',
            ['object' => $this]
        );

        return $this;
    }

    /**
     * Send 404 if page does not allow params in URL
     *
     * @return Core_Page
     * @throws Exception
     */
    protected function allowParams(): Core_Page
    {
        if (!$this->getAllowParams() && $this->getHasParams()) {
            $this->setProcess(false);
            $this->error(true);
        }

        return $this;
    }

    /**
     * Page Error 404
     *
     * @param bool $force
     *
     * @return Core_Page
     * @throws Exception
     */
    public function error(bool $force = false): Core_Page
    {
        if (($this->getTemplate() !== null && $this->getContent() === null) || $force) {
            if ($this->getObjectIdentifier() !== 'http_404') {
                $this->forward('http_404');
                $this->setProcess(false);
            } else {
                throw new Exception('There was no 404 page configured or found.');
            }
        }

        return $this;
    }

    /**
     * Redirect to secure protocol if needed
     *
     * @return Core_Page
     */
    protected function secure(): Core_Page
    {
        $redirect = App::getConfig('app.secured_protocol') !== App::getConfig('app.unsecured_protocol');

        if (!$redirect) {
            return $this;
        }

        if ($this->getIsSecure()) {
            App::setProtocol(App::getConfig('app.secured_protocol'));

            if (!App::isSsl()) {
                $this->redirect($this->getBaseUrl(true) . ltrim($this->getObjectIdentifier(), '/'));
            }
        } else {
            if (App::isSsl()) {
                $this->redirect($this->getBaseUrl() . ltrim($this->getObjectIdentifier(), '/'));
            }
        }

        return $this;
    }

    /**
     * Set error message
     *
     * @param string $message
     *
     * @return void
     */
    public function setErrorMessage(string $message): void
    {
        $messages = $this->getTmpSessionData('error_message');
        $messages[] = $message;

        $messages = array_unique($messages);

        $this->setTmpSessionData('error_message', $messages);
    }

    /**
     * Set success message
     *
     * @param string $message
     *
     * @return void
     */
    public function setSuccessMessage(string $message): void
    {
        $messages = $this->getTmpSessionData('success_message');
        $messages[] = $message;

        $messages = array_unique($messages);

        $this->setTmpSessionData('success_message', $messages);
    }

    /**
     * Set warning message
     *
     * @param string $message
     *
     * @return void
     */
    public function setWarningMessage(string $message): void
    {
        $messages = $this->getTmpSessionData('warning_message');
        $messages[] = $message;

        $messages = array_unique($messages);

        $this->setTmpSessionData('warning_message', $messages);
    }

    /**
     * Retrieve error messages
     *
     * @return string[]
     */
    public function getErrorMessages(): array
    {
        return $this->getTmpSessionData('error_message') ?: [];
    }

    /**
     * Retrieve success messages
     *
     * @return string[]
     */
    public function getSuccessMessages(): array
    {
        return $this->getTmpSessionData('success_message') ?: [];
    }

    /**
     * Retrieve warning message
     *
     * @return string[]
     */
    public function getWarningMessages(): array
    {
        return $this->getTmpSessionData('warning_message') ?: [];
    }

    /**
     * Set temporary session data. Will be destroy after render.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function setTmpSessionData(string $key, $value): void
    {
        if (App::getSession($this->getContext()) !== null) {
            App::getSession($this->getContext())->set('tmp_' . $key, $value);
        }
    }

    /**
     * Unset temporary session data
     *
     * @param string $key
     *
     * @return void
     */
    public function unsTmpSessionData(string $key): void
    {
        if (App::getSession($this->getContext()) !== null) {
            App::getSession($this->getContext())->destroy('tmp_' . $key);
        }
    }

    /**
     * Retrieve temporary session data
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getTmpSessionData(string $key)
    {
        $this->tmpSessionKeys[] = $key;

        if (App::getSession($this->getContext()) === null) {
            return null;
        }

        return App::getSession($this->getContext())->get('tmp_' . $key);
    }

    /**
     * Retrieve current language
     *
     * @param bool $withCountry
     *
     * @return string
     */
    public function getLanguage(bool $withCountry = false): string
    {
        $language = App::getLanguage();

        if ($withCountry) {
            return $language;
        }

        return substr($language, 0, 2);
    }

    /**
     * Retrieve is secure
     *
     * @return bool|null
     */
    public function getIsSecure(): ?bool
    {
        return (bool)$this->getCustom('is_secure');
    }

    /**
     * Retrieve referer Url
     *
     * @return string|null
     */
    public function getRefererUrl(): ?string
    {
        if ($this->getCustom('referer_identifier') !== null) {
            return $this->getUrl($this->getCustom('referer_identifier'));
        }

        if (App::getRefererUrl() !== null) {
            return App::getRefererUrl();
        }

        return $this->getUrl();
    }

    /**
     * Retrieve current path through App interface  
     *
     * @return string|null
     */
    public function getCurrentPath(): ?string
    {
        return App::getRequestUri();
    }

    /**
     * Retrieve current path, trimmed of .html
     *
     * @return string|null
     */
    public function getTrimmedPath(): ?string
    {
        $result = str_replace(".html", "", App::getRequestUri());
        return str_replace("/", "", $result);
    }

    /**
     * Get image resized file name in media directory
     *
     * @param string $filename
     * @param int    $maxWidth
     * @param int    $maxHeight
     *
     * @return string
     */
    public function imageResize(string $filename, int $maxWidth, int $maxHeight): string
    {
        /** @var Core_Helper_File $helper */
        $helper = App::getObject('helper_file');

        return $helper->imageResize($helper->getMediaDir(), $filename, $maxWidth, $maxHeight) ?: $filename;
    }

    /**
     * Retrieve object allow params
     *
     * @return bool|null
     */
    public function getAllowParams(): ?bool
    {
        return (bool)$this->getCustom('allow_params');
    }

    /**
     * Retrieve object has params
     *
     * @return bool|null
     */
    public function getHasParams(): ?bool
    {
        return $this->getObjectParams()->hasData();
    }

    /**
     * Retrieve page class
     *
     * @return string
     */
    public function getPageClass(): string
    {
        $helper = $this->getHelper();

        $identifier = 'page-' . ($helper->formatKey($this->getObjectIdentifier(), ['html']) ?: 'root');
        if ($this->getObjectKey()) {
            $identifier .= ' body-key-' . $helper->formatKey($this->getObjectKey()) ?: '';
        }

        return trim($identifier);
    }

    /**
     * Post render
     *
     * @return void
     */
    public function postRender(): void
    {
        if (App::getSession($this->getContext()) !== null) {
            foreach ($this->tmpSessionKeys as $key) {
                $this->unsTmpSessionData($key);
            }
        }
    }
}
