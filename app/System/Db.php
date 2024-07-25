<?php

namespace System;

use System\Loggers\ErrorLogger;
use Traits\Singleton;
use Exceptions\DbException;

/**
 * Class Db
 * @package App\System
 */
class Db
{
    protected $dbh;
    public $sql;
    public $params;

    use Singleton;

    /**
     * @throws DbException
     */
    public function __construct()
    {
        $dbHost = CONFIG['db']['host'];
        $dbName = CONFIG['db']['dbprefix'] . CONFIG['db']['dbname'];
        $charset = CONFIG['db']['charset'];
        $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$charset}";
        try {
            $this->dbh = new \PDO(
                $dsn,
                CONFIG['db']['user'],
                CONFIG['db']['password'],
                [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'
                ]
            );
        }
        catch (\PDOException $e) {
            throw new DbException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Выполняет запрос к БД
     * @return bool
     * @throws DbException
     */
    public function execute(): bool
    {
        try {
            $sth = $this->dbh->prepare($this->sql);
            return $sth->execute($this->params);
        } catch (\PDOException $e) {
            ErrorLogger::getInstance()->error($e);
            throw new DbException('Ошибочный запрос', 10001);
        }
    }

    /**
     * Выполняет запрос к БД и извлекает данные из запроса
     * @param string|null $class
     * @return array
     * @throws DbException
     */
    public function query(string $class = null)
    {
        try {
            $sth = $this->dbh->prepare($this->sql);
            $sth->execute($this->params);
            return $class ?
                $sth->fetchAll(\PDO::FETCH_CLASS, $class) :
                $sth->fetchAll(\PDO::FETCH_ASSOC);
        }
        catch (\PDOException $e) {
            ErrorLogger::getInstance()->error($e);
            throw new DbException('Ошибочный запрос', 10002);
        }
    }

    /**
     * Выполняет запрос к БД. Генерирует запись за записью из ответа сервера базы данных.
     * Не делает fetchAll(), а построчно выполняет fetch()
     * @param string $sql
     * @param array $params
     * @param string|null $class
     * @return \Generator
     * @throws DbException
     */
    public function queryEach(string $sql, array $params = [], string $class = null)
    {
        try {
            $sth = $this->dbh->prepare($sql);
            $sth->execute($params);
            $class ? $sth->setFetchMode(\PDO::FETCH_CLASS, $class) : $sth->setFetchMode(\PDO::FETCH_ASSOC);
            while ($row = $sth->fetch()) yield $row;
        } catch (\PDOException $e) {
            ErrorLogger::getInstance()->error($e);
            throw new DbException('Ошибочный запрос', 10000);
        }
    }

    /**
     * Возвращает последний вставленный в БД id
     * @return int
     */
    public function lastInsertId(): int
    {
        return $this->dbh->lastInsertId();
    }
}
