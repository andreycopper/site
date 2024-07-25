<?php
namespace Entity\User;

use DateTime;
use Entity\User;
use System\Token;
use DateInterval;
use Entity\Entity;
use Entity\Service;
use ReflectionException;
use Models\User\Session as ModelUserSession;

class Session extends Entity
{
    const MODEL = 'Models\\User\\Session';

    private ?int $id = null;
    private bool $isActive = true;
    private string $email;
    private User $user;
    private Service $service;
    private string $ip;
    private string $device;
    private DateTime $logOn;
    private DateTime $expire;
    private ?string $token = null;
    private ?string $comment = null;

    /**
     * New user session
     * @param int $userId - user id
     * @param ?string $email - user login
     * @param ?string $comment - comment
     * @throws ReflectionException
     */
    public function __construct(int $userId = 1, ?string $email = null, ?string $comment = null)
    {
        $lifeTime = Token::TOKEN_LIFE_TIME;

        $this->user = User::factory(['id' => $userId]);
        if (!empty($email)) $this->email = $email;
        $this->service = Service::factory(['id' => ModelUserSession::SERVICE_SITE]);
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->device = $_SERVER['HTTP_USER_AGENT'];
        $this->logOn = new DateTime();
        $this->expire = (new DateTime())->add(new DateInterval("PT{$lifeTime}S"));
        $this->comment = $comment;
    }

    /**
     * Mapping
     * @return array
     */
    public function getFields(): array
    {
        return [
            'id'         => ['type' => 'int',      'field' => 'id'],
            'active'     => ['type' => 'bool',     'field' => 'isActive'],
            'email'      => ['type' => 'string',   'field' => 'email'],
            'user_id'    => ['type' => 'User',     'field' => 'user'],
            'service_id' => ['type' => 'Service',  'field' => 'service'],
            'ip'         => ['type' => 'string',   'field' => 'ip'],
            'device'     => ['type' => 'string',   'field' => 'device'],
            'log_on'     => ['type' => 'datetime', 'field' => 'logOn'],
            'expire'     => ['type' => 'datetime', 'field' => 'expire'],
            'token'      => ['type' => 'string',   'field' => 'token'],
            'comment'    => ['type' => 'string',   'field' => 'comment'],
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Session
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): Session
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Session
    {
        $this->email = $email;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Session
    {
        $this->user = $user;
        return $this;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function setService(Service $service): Session
    {
        $this->service = $service;
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): Session
    {
        $this->ip = $ip;
        return $this;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): Session
    {
        $this->device = $device;
        return $this;
    }

    public function getLogOn(): DateTime
    {
        return $this->logOn;
    }

    public function setLogOn(DateTime $logOn): Session
    {
        $this->logOn = $logOn;
        return $this;
    }

    public function getExpire(): DateTime
    {
        return $this->expire;
    }

    public function setExpire(DateTime $expire): Session
    {
        $this->expire = $expire;
        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): Session
    {
        $this->token = $token;
        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): Session
    {
        $this->comment = $comment;
        return $this;
    }
}
