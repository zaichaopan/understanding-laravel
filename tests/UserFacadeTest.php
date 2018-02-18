<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Src\Facades\User;

class UserFacadeTest extends TestCase
{
    /** @test */
    public function it_should_exec_method_inside_src_user()
    {
        User::setName('Joe Doe');

        $this->assertEquals('Joe Doe', User::getName());
    }
}
