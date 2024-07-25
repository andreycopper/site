<?php
namespace Entity;

use DateTime;
use DateInterval;
use Entity\User\Block;
use Entity\User\Group;
use Entity\User\Gender;
use ReflectionException;
use Exceptions\UserException;
use Models\Request as ModelRequest;
use Models\TextType as ModelTextType;
use Models\User\Group as ModelUserGroup;
use Models\User\Block as ModelUserBlock;
use Models\User\Gender as ModelUserGender;

class User extends Entity
{
    const MODEL = 'Models\\User';

    private ?int $id = null;
    private bool $isActive = false;
    private bool $isBlocked = false;
    private ?Block $block = null;
    private Group $group;
    private ?string $login = null;
    private ?string $password = null;
    private ?string $email = null;
    private bool $isShowEmail = false;
    private ?string $phone = null;
    private bool $isShowPhone = false;
    private ?string $name = null;
    private ?string $secondName = null;
    private ?string $lastName = null;
    private Gender $gender;
    private bool $hasPersonalDataAgreement = true;
    private bool $hasMailingAgreement = false;
    private TextType $mailingType;
    private int $timezone = 0;
    private DateTime $created;
    private ?DateTime $updated = null;
    private ?string $publicKey = null;
    private ?string $privateKey = null;

    /**
     * New user
     * @param ?string $login
     * @param ?string $password
     * @param ?string $email
     * @throws ReflectionException
     */
    public function __construct(?string $login = null, ?string $password = null, ?string $email = null)
    {
        $this->group = Group::factory(['id' => ModelUserGroup::USER_USER]);
        $this->gender = Gender::factory(['id' => ModelUserGender::GENDER_MALE]);
        $this->mailingType = TextType::factory(['id' => ModelTextType::TYPE_HTML]);
        $this->login = $login;
        $this->password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
        $this->email = $email;
        $this->created = new DateTime();
    }

    public function init(?array $data, ?array $params = []): ?static
    {
        parent::init($data);
        if (!empty($params['load_keys'])) $this->loadPrivateKey()->loadPublicKey();
        return $this;
    }

    /**
     * Mapping
     * @return array
     */
    public function getFields(): array
    {
        return [
            'id'                      => ['type' => 'int',            'field' => 'id'],
            'active'                  => ['type' => 'bool',           'field' => 'isActive'],
            'blocked'                 => ['type' => 'bool',           'field' => 'isBlocked'],
            'locked'                  => ['type' => 'bool',           'field' => 'isLocked'],
            'block_id'                => ['type' => 'User\Block',     'field' => 'block'],
            'group_id'                => ['type' => 'User\Group',     'field' => 'group'],
            'login'                   => ['type' => 'string',         'field' => 'login'],
            'password'                => ['type' => 'string',         'field' => 'password'],
            'email'                   => ['type' => 'string',         'field' => 'email'],
            'show_email'              => ['type' => 'bool',           'field' => 'isShowEmail'],
            'phone'                   => ['type' => 'string',         'field' => 'phone'],
            'show_phone'              => ['type' => 'bool',           'field' => 'isShowPhone'],
            'name'                    => ['type' => 'string',         'field' => 'name'],
            'second_name'             => ['type' => 'string',         'field' => 'secondName'],
            'last_name'               => ['type' => 'string',         'field' => 'lastName'],
            'gender_id'               => ['type' => 'User\Gender',    'field' => 'gender'],
            'personal_data_agreement' => ['type' => 'bool',           'field' => 'hasPersonalDataAgreement'],
            'mailing'                 => ['type' => 'bool',           'field' => 'hasMailingAgreement'],
            'mailing_type_id'         => ['type' => 'TextType',       'field' => 'mailingType'],
            'timezone'                => ['type' => 'int',            'field' => 'timezone'],
            'created'                 => ['type' => 'datetime',       'field' => 'created'],
            'updated'                 => ['type' => 'datetime',       'field' => 'updated'],
        ];
    }

    public function loadPublicKey(): User
    {
        $publicKeyFile = DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . 'public.pem';
        $this->publicKey = is_file($publicKeyFile) && filesize($publicKeyFile) > 0 ? file_get_contents($publicKeyFile) : null;
        return $this;
    }

    public function loadPrivateKey(): User
    {
        $privateKeyFile = DIR_CERTIFICATES . DIRECTORY_SEPARATOR . $this->id . DIRECTORY_SEPARATOR . 'private.pem';
        $this->privateKey = is_file($privateKeyFile) && filesize($privateKeyFile) > 0 ? file_get_contents($privateKeyFile) : null;
        return $this;
    }

    /**
     * Создает запись о блокировке пользователя
     * @param int $time - время блокировки
     * @param ?string $reason - причина блокировки
     * @return bool|int
     */
    public function block(int $time, ?string $reason = null): bool|int
    {
        $date = new DateTime();
        $userBlock = new ModelUserBlock();
        $userBlock->user_id = $this->id;
        $userBlock->created = $date->format('Y-m-d H:i:s');
        $userBlock->expire = $date->add(new DateInterval( "PT{$time}S" ))->format('Y-m-d H:i:s');
        $userBlock->reason = $reason;
        return $userBlock->save();
    }

    public function isAdmin(): bool
    {
        return $this->group->getId() === ModelUserGroup::USER_ADMIN;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setActive(bool $isActive): User
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function setBlocked(bool $isBlocked): User
    {
        $this->isBlocked = $isBlocked;
        return $this;
    }

    public function getBlock(): ?Block
    {
        return $this->block;
    }

    public function setBlock(?Block $block): User
    {
        $this->block = $block;
        return $this;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): User
    {
        $this->group = $group;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): User
    {
        $this->login = $login;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function isShowEmail(): bool
    {
        return $this->isShowEmail;
    }

    public function setShowEmail(bool $isShowEmail): User
    {
        $this->isShowEmail = $isShowEmail;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): User
    {
        $this->phone = $phone;
        return $this;
    }

    public function isShowPhone(): bool
    {
        return $this->isShowPhone;
    }

    public function setShowPhone(bool $isShowPhone): User
    {
        $this->isShowPhone = $isShowPhone;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->secondName;
    }

    public function setSecondName(?string $secondName): User
    {
        $this->secondName = $secondName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): User
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): User
    {
        $this->gender = $gender;
        return $this;
    }

    public function hasPersonalDataAgreement(): bool
    {
        return $this->hasPersonalDataAgreement;
    }

    public function setPersonalDataAgreement(bool $hasPersonalDataAgreement): User
    {
        $this->hasPersonalDataAgreement = $hasPersonalDataAgreement;
        return $this;
    }

    public function hasMailingAgreement(): bool
    {
        return $this->hasMailingAgreement;
    }

    public function setMailingAgreement(bool $hasMailingAgreement): User
    {
        $this->hasMailingAgreement = $hasMailingAgreement;
        return $this;
    }

    public function getMailingType(): TextType
    {
        return $this->mailingType;
    }

    public function setMailingType(TextType $mailingType): User
    {
        $this->mailingType = $mailingType;
        return $this;
    }

    public function getTimezone(): int
    {
        return $this->timezone;
    }

    public function setTimezone(int $timezone): User
    {
        $this->timezone = $timezone;
        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): User
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): User
    {
        $this->updated = $updated;
        return $this;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(?string $publicKey): User
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    public function setPrivateKey(?string $privateKey): User
    {
        $this->privateKey = $privateKey;
        return $this;
    }
}
