<?php
namespace Entity;

use DateTime;
use ReflectionException;
use Models\TextType as ModelTextType;

class TextType extends Entity
{
    const MODEL = 'Models\\TextType';

    private ?int $id = null;
    private bool $isActive = true;
    private string $name;
    private DateTime $created;
    private ?DateTime $updated = null;

    /**
     * @param string $name
     */
    public function __construct(string $name = 'new text type')
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

    public function setId(?int $id): TextType
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): TextType
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TextType
    {
        $this->name = $name;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): TextType
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): TextType
    {
        $this->updated = $updated;
        return $this;
    }
}
