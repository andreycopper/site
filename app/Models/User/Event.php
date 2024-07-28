<?php
namespace Models\User;

use System\Db;
use Models\Model;

class Event extends Model
{
    const TYPE_MAIL = 1;
    const TYPE_SMS = 2;
    const TYPE_PUSH = 3;

    const TEMPLATE_EMAIL_CONFIRM = 1;
    const TEMPLATE_REGISTER = 2;
    const TEMPLATE_PASSWORD_RECOVERY = 3;
    const TEMPLATE_PASSWORD_CHANGED = 4;

    const EVENT_EMAIL_EMPTY = 'Event email is empty';
    const EVENT_DOESNT_EXIST = 'Event doesn\'t exist';
    const EVENT_ALREADY_SENT = 'Event already sent';
    const EVENT_ALREADY_EXPIRED = 'Event already expired';
    const CODE_DOESNT_EXIST = 'This code doesn\'t exist';
    const CODE_ALREADY_EXPIRED = 'This code already expired';
    const CODE_ALREADY_ACTIVATED = 'This code already activated';
    const CODE_DIDNT_SEND = 'This code didn\'t send anywhere';
    const CODE_ALREADY_SENT = 'Code already sent';

    protected static $db_table = 'site.user_events';

    public ?int $id;
    public ?int $active = 1;
    public ?int $user_id = null;
    public int $user_event_template_id;
    public ?string $code = null;
    public ?string $params = null;
    public string $expire;
    public ?string $send;
    public string $created;
    public ?string $updated = null;

    /**
     * Get event by id
     * @param int $id - event id
     * @param ?array $params - params
     * @return ?array
     */
    public static function getById(int $id, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND e.active IS NOT NULL AND e.expire > NOW()' : '';
        $db->params = ['id' => $id];
        $db->sql = "
            SELECT 
                e.id, e.active, e.user_id, e.user_event_template_id, e.code, e.params, e.expire, e.send, e.created, e.updated
            FROM {$prefix}{$table} e
            WHERE e.id = :id {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Get event by code
     * @param string $code - code
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByCode(string $code, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND e.expire > NOW() AND e.active IS NOT NULL' : '';
        $template = !empty($params['template']) ? 'AND e.user_event_template_id = :template' : '';
        $db->params = ['code' => $code];
        if (!empty($params['template'])) $db->params['template'] = $params['template'];

        $db->sql = "
            SELECT 
                e.id, e.active, e.user_id, e.user_event_template_id, e.code, e.params, e.expire, e.send, e.created, e.updated
            FROM {$prefix}{$table} e 
            WHERE e.code IS NOT NULL AND e.code = :code {$template} {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }

    /**
     * Get event by code
     * @param string $email - email
     * @param ?array $params - params
     * @return ?array
     */
    public static function getByEmail(string $email, ?array $params = []): ?array
    {
        $params += ['active' => true, 'object' => false];
        $prefix = self::$db_prefix;
        $table = self::$db_table;

        $db = Db::getInstance();
        $active = !empty($params['active']) ? 'AND e.expire > NOW() AND e.active IS NOT NULL' : '';
        $template = !empty($params['template']) ? 'AND e.user_event_template_id = :template' : '';
        $db->params = ['email' => $email];
        if (!empty($params['template'])) $db->params['template'] = $params['template'];

        $db->sql = "
            SELECT 
                e.id, e.active, e.user_id, e.user_event_template_id, e.code, e.params, e.expire, e.send, e.created, e.updated
            FROM {$prefix}{$table} e 
            LEFT JOIN {$prefix}site.users u ON u.id = e.user_id
            WHERE e.code IS NOT NULL AND u.email = :email {$template} {$active}";

        $data = $db->query(!empty($params['object']) ? static::class : null);
        return !empty($data) ? array_shift($data) : null;
    }
}
