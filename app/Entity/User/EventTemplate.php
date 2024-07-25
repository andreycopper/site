<?php
namespace Entity\User;

use DateTime;
use Entity\Entity;
use ReflectionException;
use Models\User\EventType as ModelUserEventType;

class EventTemplate extends Entity
{
    const MODEL = 'Models\\User\\EventTemplate';

    private ?int $id = null;
    private bool $isActive = true;
    private EventType $userEventType;
    private ?string $name;
    private ?string $message;
    private DateTime $created;
    private ?DateTime $updated = null;

    /**
     * New event template
     * @param int $userEventTypeId - event type id
     * @param ?string $name - template name
     * @param ?string $message - message
     * @throws ReflectionException
     */
    public function __construct(int $userEventTypeId = ModelUserEventType::TYPE_EMAIL, ?string $name = null, ?string $message = null)
    {
        $this->userEventType = EventType::factory(['id' => $userEventTypeId]);
        $this->name = $name;
        $this->message = $message;
        $this->created = new DateTime();
    }

    /**
     * Mapping fields
     * @return array
     */
    public function getFields(): array
    {
        return [
            'id'                 => ['type' => 'int',            'field' => 'id'],
            'active'             => ['type' => 'bool',           'field' => 'isActive'],
            'user_event_type_id' => ['type' => 'User\EventType', 'field' => 'userEventType'],
            'name'               => ['type' => 'string',         'field' => 'name'],
            'message'            => ['type' => 'string',         'field' => 'message'],
            'created'            => ['type' => 'datetime',       'field' => 'created'],
            'updated'            => ['type' => 'datetime',       'field' => 'updated'],
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): EventTemplate
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): EventTemplate
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getUserEventType(): EventType
    {
        return $this->userEventType;
    }

    public function setUserEventType(EventType $userEventType): EventTemplate
    {
        $this->userEventType = $userEventType;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): EventTemplate
    {
        $this->name = $name;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): EventTemplate
    {
        $this->message = $message;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): EventTemplate
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): EventTemplate
    {
        $this->updated = $updated;
        return $this;
    }
}
