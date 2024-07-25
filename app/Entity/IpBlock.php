<?php
namespace Entity;

use DateTime;
use DateInterval;
use ReflectionException;
use Models\User\Block as ModelUserBlock;

class IpBlock extends Entity
{
    const MODEL = 'Models\\IpBlock';

    private ?int $id = null;
    private ?string $ip = null;
    private ?int $userId = null;
    private ?string $login = null;
    private DateTime $expire;
    private ?string $reason = null;
    private DateTime $created;
    private ?DateTime $updated = null;

    /**
     * New ip block
     * @param ?string $ip - ip address
     * @param ?int $userId - user id
     * @param ?string $login - user login
     * @param ?string $reason - block reason
     */
    public function __construct(?string $ip = null, ?int $userId = null, ?string $login = null, ?string $reason = null)
    {
        $blockTime = ModelUserBlock::INTERVAL_DAY;

        $this->ip = $ip;
        $this->userId = $userId;
        $this->login = $login;
        $this->expire = (new DateTime())->add(new DateInterval("PT{$blockTime}S"));
        $this->reason = $reason;
        $this->created = new DateTime();
    }

    /**
     * Mapping fields
     * @return array
     */
    public function getFields(): array
    {
        return [
            'id'      => ['type' => 'int',      'field' => 'id'],
            'ip'      => ['type' => 'string',   'field' => 'ip'],
            'user_id' => ['type' => 'int',      'field' => 'userId'],
            'login'   => ['type' => 'string',   'field' => 'login'],
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

    public function setId(?int $id): IpBlock
    {
        $this->id = $id;
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): IpBlock
    {
        $this->ip = $ip;
        return $this;
    }

    public function getUser(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): IpBlock
    {
        $this->userId = $userId;
        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): IpBlock
    {
        $this->login = $login;
        return $this;
    }

    public function getExpire(): DateTime
    {
        return $this->expire;
    }

    public function setExpire(DateTime $expire): IpBlock
    {
        $this->expire = $expire;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): IpBlock
    {
        $this->reason = $reason;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): IpBlock
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): IpBlock
    {
        $this->updated = $updated;
        return $this;
    }
}
