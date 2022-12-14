<?php declare(strict_types=1);

namespace Sarok\Repository;

use Sarok\DB;
use Sarok\DIContainer;
use PHPUnit\Framework\TestCase;

abstract class RepositoryTest extends TestCase
{
    private static DIContainer $container;

    public static function setUpBeforeClass() : void
    {
        $container = new DIContainer();
        
        $container->put('logPath', './logs/log.txt');
        $container->put('logLevel', 5);

        $container->put("db_host", "mysql");
        $container->put("db_name", "sarok");
        $container->put("db_user", "sarok");
        $container->put("db_password", "such_sec0re");

        self::$container = $container;
    }

    protected static function get(string $name, bool $prototype = false) : mixed
    {
        return self::$container->get($name, $prototype);
    }

    protected function clearTable(string $tableName) : void
    {
        $db = self::get(DB::class);
        $db->execute("TRUNCATE TABLE `$tableName`");
    }
}
