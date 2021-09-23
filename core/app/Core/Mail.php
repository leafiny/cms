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
 * Class Core_Mail
 */
class Core_Mail extends Core_Template_Abstract
{
    /**
     * Send Mail
     *
     * @param mixed[] $variables
     *
     * @return bool
     * @throws Throwable
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    public function send(array $variables = []): bool
    {
        if (!$this->getRecipientEmail()) {
            throw new Exception('Recipient Email is missing');
        }

        if (!$this->getSenderEmail()) {
            throw new Exception('Sender Email is missing');
        }

        foreach ($variables as $code => $value) {
            $this->setData($code, $value);
        }

        if ($this->isDisabled()) {
            return true;
        }

        return $this->isSmtpEnabled() ? $this->advancedMail() : $this->simpleMail();
    }

    /**
     * Send mail via SMTP
     *
     * @return bool
     * @throws Throwable
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     * @throws PHPMailer\PHPMailer\Exception
     */
    protected function advancedMail(): bool
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        $mail->isSMTP();
        $mail->SetFrom($this->getSenderEmail(), $this->getSenderName());
        $mail->addReplyTo($this->getReplyTo() ?: $this->getSenderEmail());
        $mail->AddAddress($this->getRecipientEmail());
        if ($this->getContentType() === 'text/html') {
            $mail->isHTML();
        }

        $mail->CharSet    = $this->getCharset();
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth   = $this->getSmtpAuth();
        $mail->SMTPSecure = $this->getSmtpSecure();
        $mail->Host       = $this->getSmtpHost();
        $mail->Port       = $this->getSmtpPort();
        $mail->Username   = $this->getSmtpUsername();
        $mail->Password   = $this->getSmtpPassword();
        $mail->Subject    = $this->translate($this->getSubject());
        $mail->Body       = $this->render();

        return $mail->Send();
    }

    /**
     * Send simple mail with mail function
     *
     * @return bool
     * @throws Throwable
     * @throws Twig\Error\LoaderError
     * @throws Twig\Error\RuntimeError
     * @throws Twig\Error\SyntaxError
     */
    protected function simpleMail(): bool
    {
        $headers = array_merge(
            [
                'From: "' . $this->getSenderName() . '" <' . $this->getSenderEmail() . '>',
                'Reply-To: ' . ($this->getReplyTo() ?: $this->getSenderEmail()),
                'MIME-Version: 1.0',
                'Content-Transfer-Encoding: 8bit',
                'Content-type: ' . $this->getContentType() . '; charset=' . $this->getCharset(),
            ],
            $this->getHeaders()
        );

        $subject = $this->translate($this->getSubject());
        if (extension_loaded('mbstring')) {
            $subject = mb_encode_mimeheader($subject);
        }

        return mail(
            $this->getRecipientEmail(),
            $subject,
            $this->render(),
            join("\r\n", $headers)
        );
    }

    /**
     * Retrieve current language
     *
     * @return string
     */
    public function getLanguage(): string
    {
        $language = App::getLanguage();

        return substr($language, 0, 2);
    }

    /**
     * Retrieve Recipient Email
     *
     * @return string|null
     */
    public function getRecipientEmail(): ?string
    {
        return $this->getCustom('recipient_email');
    }

    /**
     * Add Recipient Email
     *
     * @param string $recipient
     *
     * @return void
     */
    public function setRecipientEmail(string $recipient): void
    {
        $this->setCustom('recipient_email', $recipient);
    }

    /**
     * Retrieve Subject
     *
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->getCustom('subject');
    }

    /**
     * Add Subject
     *
     * @param string $subject
     *
     * @return void
     */
    public function setSubject(string $subject): void
    {
        $this->setCustom('subject', $subject);
    }

    /**
     * Retrieve Email Sender
     *
     * @return string|null
     */
    public function getSenderEmail(): ?string
    {
        return $this->getCustom('sender_email');
    }

    /**
     * Add Sender Email
     *
     * @param string $senderEmail
     *
     * @return void
     */
    public function setSenderEmail(string $senderEmail): void
    {
        $this->setCustom('sender_email', $senderEmail);
    }

    /**
     * Retrieve Reply To E-mail
     *
     * @return string|null
     */
    public function getReplyTo(): ?string
    {
        return $this->getCustom('reply_to');
    }

    /**
     * Add Replay To Email
     *
     * @param string $replyTo
     *
     * @return void
     */
    public function setReplyTo(string $replyTo): void
    {
        $this->setCustom('reply_to', $replyTo);
    }

    /**
     * Retrieve Email Sender Name
     *
     * @return string|null
     */
    public function getSenderName(): ?string
    {
        return $this->getCustom('sender_name');
    }

    /**
     * Add Sender Name
     *
     * @param string $senderName
     *
     * @return void
     */
    public function setSenderName(string $senderName): void
    {
        $this->setCustom('sender_name', $senderName);
    }

    /**
     * Retrieve Email Content Type
     *
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->getCustom('content_type') ?: 'text/html';
    }

    /**
     * Add Content Type
     *
     * @param string $contentType
     *
     * @return void
     */
    public function setContentType(string $contentType): void
    {
        $this->setCustom('content_type', $contentType);
    }

    /**
     * Retrieve Headers
     *
     * @return string[]
     */
    public function getHeaders(): array
    {
        $headers = [];

        if (is_array($this->getCustom('headers'))) {
            $headers = $this->getCustom('headers');
        }

        return $headers;
    }

    /**
     * Add Headers
     *
     * @param string[] $headers
     *
     * @return void
     */
    public function setHeaders(array $headers): void
    {
        $this->setCustom('headers', $headers);
    }

    /**
     * Retrieve Charset
     *
     * @return string|null
     */
    public function getCharset(): ?string
    {
        return $this->getCustom('charset') ?: 'utf-8';
    }

    /**
     * Add Charset
     *
     * @param string $charset
     *
     * @return void
     */
    public function setCharset(string $charset): void
    {
        $this->setCustom('charset', $charset);
    }

    /**
     * Retrieve SMTP Auth
     *
     * @return bool
     */
    public function getSmtpAuth(): bool
    {
        return $this->getCustom('smtp_auth') ? true : false;
    }

    /**
     * Retrieve SMTP Secure connexion
     *
     * @return string|null
     */
    public function getSmtpSecure(): ?string
    {
        return $this->getCustom('smtp_secure');
    }

    /**
     * Retrieve SMTP Host
     *
     * @return string|null
     */
    public function getSmtpHost(): ?string
    {
        return $this->getCustom('smtp_host');
    }

    /**
     * Retrieve SMTP Port
     *
     * @return int|null
     */
    public function getSmtpPort(): ?int
    {
        return $this->getCustom('smtp_port');
    }

    /**
     * Retrieve SMTP Username
     *
     * @return string|null
     */
    public function getSmtpUsername(): ?string
    {
        return $this->getCustom('smtp_username');
    }

    /**
     * Retrieve SMTP Password
     *
     * @return string|null
     */
    public function getSmtpPassword(): ?string
    {
        return $this->getCustom('smtp_password');
    }

    /**
     * Retrieve mail is advanced
     *
     * @return bool
     */
    public function isSmtpEnabled(): bool
    {
        return $this->getCustom('smtp_enabled') ? true : false;
    }

    /**
     * Retrieve email is disabled
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return (bool)$this->getCustom('send_disabled');
    }
}
