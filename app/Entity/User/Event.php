<?php
namespace Entity\User;

use DateTime;
use Entity\User;
use Utils\Mailer;
use Entity\Entity;
use ReflectionException;
use Exceptions\UserException;
use Exceptions\MailException;
use Utils\Data\ValidationEvent;
use Models\User\Event as ModelUserEvent;

class Event extends Entity
{
    const MODEL = 'Models\\User\\Event';

    private ?int $id = null;
    private bool $active = true;
    private ?User $user = null;
    private ?string $email = null;
    private EventTemplate $eventTemplate;
    private ?string $code = null;
    private array $params;
    private DateTime $expire;
    private ?DateTime $send = null;
    private DateTime $created;
    private ?DateTime $updated = null;

    /**
     * New user event
     * @param int $templateId - template id
     * @param ?User $user - user id
     * @param ?array $params - params
     * @throws ReflectionException
     */
    public function __construct(int $templateId = ModelUserEvent::TEMPLATE_EMAIL_CONFIRM, ?string $email = null, ?User $user = null, ?array $params = [])
    {
        $this->user = $user ?: null;
        $this->email = $email ?: $user ?-> getEmail();
        $this->eventTemplate = EventTemplate::factory(['id' => $templateId]);
        $this->params = $params + ['user_email' => $this->email];
        $this->expire = (new DateTime())->modify('+1 day');
        $this->created = new DateTime();

        if (in_array($templateId, [ModelUserEvent::TEMPLATE_EMAIL_CONFIRM, ModelUserEvent::TEMPLATE_PASSWORD_RECOVERY]))
            $this->code = sha1(time()) . md5(time());
    }

    /**
     * Mapping
     * @return array
     */
    public function getFields(): array
    {
        return [
            'id'                     => ['type' => 'int',                'field' => 'id'],
            'active'                 => ['type' => 'bool',               'field' => 'active'],
            'user_id'                => ['type' => 'User',               'field' => 'user'],
            'user_event_template_id' => ['type' => 'User\EventTemplate', 'field' => 'eventTemplate'],
            'code'                   => ['type' => 'string',             'field' => 'code'],
            'params'                 => ['type' => 'array',              'field' => 'params'],
            'expire'                 => ['type' => 'datetime',           'field' => 'expire'],
            'send'                   => ['type' => 'datetime',           'field' => 'send'],
            'created'                => ['type' => 'datetime',           'field' => 'created'],
            'updated'                => ['type' => 'datetime',           'field' => 'updated'],
        ];
    }

    /**
     * Send an event
     * @return bool|int
     * @throws MailException|UserException
     */
    public function send(): bool|int
    {
        ValidationEvent::event($this) && ValidationEvent::isEventNotSend($this);

        if ($this->eventTemplate->getUserEventType()->getId() === ModelUserEvent::TYPE_MAIL) {
            $mail = (new Mailer($this))->send();
            return $this->setSend($mail->getSend())->setUpdated($mail->getSend())->save();
        }
        else return false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Event
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Event
    {
        $this->active = $active;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Event
    {
        $this->user = $user;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Event
    {
        $this->email = $email;
        return $this;
    }

    public function getEventTemplate(): EventTemplate
    {
        return $this->eventTemplate;
    }

    public function setEventTemplate(EventTemplate $eventTemplate): Event
    {
        $this->eventTemplate = $eventTemplate;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): Event
    {
        $this->code = $code;
        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): Event
    {
        $this->params = $params;
        return $this;
    }

    public function getExpire(): DateTime
    {
        return $this->expire;
    }

    public function setExpire(DateTime $expire): Event
    {
        $this->expire = $expire;
        return $this;
    }

    public function getSend(): ?DateTime
    {
        return $this->send;
    }

    public function setSend(?DateTime $send): Event
    {
        $this->send = $send;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): Event
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): Event
    {
        $this->updated = $updated;
        return $this;
    }
}
