<?php
namespace Entity\User;

use DateTime;
use Entity\Entity;

class Group extends Entity
{
    const MODEL = 'Models\\User\\Group';

    private ?int $id = null;
    private bool $isActive = true;
    private string $name;
    private DateTime $created;
    private ?DateTime $updated = null;

    /**
     * New group
     * @param string $name - group name
     */
    public function __construct(string $name = 'new group')
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

    public function setId(?int $id): Group
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): Group
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Group
    {
        $this->name = $name;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): Group
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): Group
    {
        $this->updated = $updated;
        return $this;
    }
}
