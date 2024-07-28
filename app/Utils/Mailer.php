<?php
namespace Utils;

use DateTime;
use Entity\User\Event;
use Exceptions\MailException;

class Mailer
{
    const TYPE_TEXT = 'text';
    const TYPE_HTML = 'html';
    const CANT_SEND_EMAIL = 'Can\'t send email';

    private string $to;
    private string $from = EMAIL;
    private string $fromName = SITENAME;
    private string $replyTo = EMAIL;
    private ?string $headers = null;
    private string $type = self::TYPE_HTML;
    private string $subject;
    private string $message;
    private ?DateTime $send;

    /**
     * New mail
     * @param Event $event - event
     */
    public function __construct(Event $event)
    {
        $this->type = $event->getUser()->getMailingType()->getName();
        $this->to = $event->getUser()->getEmail();
        $this->subject = $event->getEventTemplate()->getName();
        $this->message = $event->getEventTemplate()->getMessage();
        $this->prepareMessage($event)->removeTags();
    }

    /**
     * Is text message
     * @return bool
     */
    private function isText(): bool
    {
        return $this->type === self::TYPE_TEXT;
    }

    /**
     * Is html message
     * @return bool
     */
    private function isHtml(): bool
    {
        return $this->type === self::TYPE_HTML;
    }

    /**
     * Prepare message - replace all vars in text
     * @param Event $event
     * @return $this
     */
    private function prepareMessage(Event $event): Mailer
    {
        if (!empty($event->getUser())) {
            $this->message = str_replace('#USER_EMAIL#', $event->getUser()->getEmail() ?: '', $this->message);
        }

        $this->message = str_replace('#CODE#', $event->getCode() ?: '', $this->message);

        $constants = get_defined_constants(true);
        if (!empty($constants['user']) && is_array($constants['user'])) { // замена всех констант в шаблоне
            foreach ($constants['user'] as $key => $constant) {
                if (is_string($constant)) $this->message = str_replace('#' . mb_strtoupper($key) . '#', $constant, $this->message);
            }
        }

        if (!empty($event->getParams()) && is_array($event->getParams())) { // замена всех переменных из массива подстановок
            foreach ($event->getParams() as $key => $param) {
                $this->message = str_replace('#' . mb_strtoupper($key) . '#', $param, $this->message);
            }
        }

        return $this;
    }

    /**
     * Remove extra tags depending on mailing type (text/html)
     * @return Mailer
     */
    private function removeTags(): Mailer
    {
        if ($this->isHtml()) {
            $this->message = strip_tags(trim($this->message), '<p><div><span><b><strong><i><br><h1><h2><h3><h4><h5><h6><ul><ol><li><a><table><tr><th><td><caption>');
        }
        elseif ($this->isText()) {
            $this->message = strip_tags($this->message);
        }

        return $this;
    }

    /**
     * Send an event
     * @return Mailer
     * @throws MailException
     */
    public function send(): Mailer
    {
        $contentType = 'Content-type: text/' . ($this->isHtml() ? 'html' : 'plain') . '; charset=utf-8';
        $this->headers =
            "MIME-Version: 1.0\r\n" .
            "{$contentType}\r\n" .
            "Content-Transfer-Encoding: 7bit\r\n" .
            "From: {$this->fromName} {$this->from}\r\n" .
            "Reply-To: {$this->replyTo}\r\n" .
            "X-Mailer: PHP" . phpversion() . "\r\n";

        $result = mail(
            $this->to,
            "=?UTF-8?B?" . base64_encode($this->subject) . "?=",
            $this->message,
            $this->headers
        );

        if (!$result) throw new MailException(self::CANT_SEND_EMAIL);

        $this->send = new DateTime();
        return $this;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function setTo(string $to): Mailer
    {
        $this->to = $to;
        return $this;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): Mailer
    {
        $this->from = $from;
        return $this;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function setFromName(string $fromName): Mailer
    {
        $this->fromName = $fromName;
        return $this;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    public function setReplyTo(string $replyTo): Mailer
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    public function getHeaders(): ?string
    {
        return $this->headers;
    }

    public function setHeaders(?string $headers): Mailer
    {
        $this->headers = $headers;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Mailer
    {
        $this->type = $type;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): Mailer
    {
        $this->subject = $subject;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Mailer
    {
        $this->message = $message;
        return $this;
    }

    public function getSend(): ?DateTime
    {
        return $this->send;
    }

    public function setSend(?DateTime $send): Mailer
    {
        $this->send = $send;
        return $this;
    }
}
