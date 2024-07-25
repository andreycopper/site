<?php
namespace Entity\User;

use DateTime;
use DateInterval;
use Entity\Entity;

class Block extends Entity
{
    const MODEL = 'Models\\User\\Block';

    private ?int $id = null;
    private ?int $userId;
    private DateTime $expire;
    private ?string $reason = null;
    private DateTime $created;
    private ?DateTime $updated = null;

    /**
     * New user block
     * @param ?int $userId
     * @param ?string $reason
     */
    public function __construct(?int $userId = null, ?string $reason = null)
    {
        $blockTime = self::MODEL::INTERVAL_DAY;

        $this->userId = $userId;
        $this->expire = (new DateTime())->add(new DateInterval("PT{$blockTime}S"));
        $this->reason = $reason;
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
            'user_id' => ['type' => 'int',      'field' => 'userId'],
            'expire'  => ['type' => 'datetime', 'field' => 'expire'],
            'reason'  => ['type' => 'string',   'field' => 'reason'],
            'created' => ['type' => 'datetime', 'field' => 'created'],
            'updated' => ['type' => 'datetime', 'field' => 'updated'],
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Block
    {
        $this->id = $id;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): Block
    {
        $this->userId = $userId;
        return $this;
    }

    public function getExpire(): DateTime
    {
        return $this->expire;
    }

    public function setExpire(DateTime $expire): Block
    {
        $this->expire = $expire;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): Block
    {
        $this->reason = $reason;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): Block
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): Block
    {
        $this->updated = $updated;
        return $this;
    }
}
