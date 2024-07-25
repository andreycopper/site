<?php
namespace Models;

class Setting extends Model
{
    protected static $db_table = 'site.settings';

    public $id;
    public $active;
    public $name;
    public $value;
    public $description;
    public $created;
    public $updated;

    /**
     * Возвращает массив настроек из БД
     * @return array
     */
    public static function getSiteSettings()
    {
        $settings = [];
        $data = Setting::getList();

        if (!empty($data) && is_array($data)) {
            foreach ($data as $item) {
                $settings[$item['name']] = $item['value'];
            }
        }

        return $settings;
    }
}
