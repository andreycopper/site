<?php
namespace Entity;

use DateTime;

class Service extends Entity
{
    const MODEL = 'Models\\Service';

    private ?int $id = null;
    private bool $isActive = true;
    private string $name;
    private DateTime $created;
    private ?DateTime $updated = null;

    /**
     * @param string $name
     */
    public function __construct(string $name = 'new service')
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

    public function setId(?int $id): Service
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): Service
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Service
    {
        $this->name = $name;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): Service
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): Service
    {
        $this->updated = $updated;
        return $this;
    }
}
