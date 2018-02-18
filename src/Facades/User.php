<?php

namespace Src\Facades;

use Src\User as SrcUser;

class User extends Facade
{
    public static function getFacadeAccessor()
    {
        return SrcUser::class;
    }
}
