<?php
namespace Entity\User;

use DateTime;
use Entity\Entity;

class EventType extends Entity
{
    const MODEL = 'Models\\User\\EventType';

    private ?int $id = null;
    private bool $isActive = true;
    private string $name;
    private DateTime $created;
    private ?DateTime $updated = null;

    /**
     * New event type
     * @param string $name - event type name
     */
    public function __construct(string $name = 'new event')
    {
        $this->name = $name;
        $this->created = new DateTime();
    }

    /**
     * Mapping
     * @return array
     */
    public function getFields(): array
    {
        return [
            'id'      => ['type' => 'int',      'field' => 'id'],
            'active'  => ['type' => 'bool',     'field' => 'isActive'],
            'name'    => ['type' => 'string',   'field' => 'name'],
            'created' => ['type' => 'datetime', 'field' => 'created'],
            'updated' => ['type' => 'datetime', 'field' => 'updated'],
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): EventType
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): EventType
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): EventType
    {
        $this->name = $name;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): EventType
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): EventType
    {
        $this->updated = $updated;
        return $this;
    }
}
