<?php

namespace Src\Facades;

use Src\Container;

abstract class Facade
{
    protected static $app;

    protected static $resolvedInstance;

    public static function getFacadeAccessor()
    {
        return new \RuntimeException('Please implementation facade accessor');
    }

    public static function setFacadeApplication($app)
    {
        static::$app = $app;
    }

    public static function __callStatic($method, $args)
    {
        if (!static::$resolvedInstance) {
            if (!static::$app) {
                $app = new Container;
                static::setFacadeApplication($app);
            }

            static::$resolvedInstance = static::$app->make(static::getFacadeAccessor());
        }

        $instance = static::$resolvedInstance;

        return $instance->$method(...$args);
    }
}
