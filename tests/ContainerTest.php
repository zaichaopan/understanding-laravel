<?php

namespace Tests;

use Src\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /** @test */
    public function it_can_create_contain_instance()
    {
        $container = new Container;

        $this->assertInstanceOf(Container::class, $container);
    }

    /** @test */
    public function it_can_resolve_instance_out_of_the_container()
    {
        $dummy = new Dummy;

        $container = new Container;

        $container->instance(Dummy::class, $dummy);

        $this->assertSame($dummy, $container->make(Dummy::class));
    }

    /** @test */
    public function it_throws_exception_if_instance_cannot_be_resolved()
    {
        $this->expectException(\Exception::class);

        $container = new Container;

        $container->make('FakeClass');
    }

    /** @test */
    public function it_can_resolve_binding()
    {
        $resolver = function () {
            return new Dummy;
        };

        $container = new Container;

        $container->bind(Dummy::class, $resolver);

        $this->assertInstanceOf(Dummy::class, $container->make(Dummy::class));

        $dummy = $container->make(Dummy::class);

        $this->assertNotSame($dummy, $container->make(Dummy::class));
    }

    /** @test */
    public function it_can_resolve_singleton_binding()
    {
        $resolver = function () {
            return new Dummy;
        };

        $container = new Container;

        $container->singleton(Dummy::class, $resolver);

        $dummy = $container->make(Dummy::class);

        $this->assertSame($dummy, $container->make(Dummy::class));
    }

    /** @test */
    public function it_can_resolve_dependencies_of_dependencies()
    {
        $container = new Container;

        $baz = $container->make(Baz::class);

        $this->assertInstanceOf(Baz::class, $baz);
    }
}

class Dummy
{
}

class Foo
{
}

class Bar
{
    public function __construct(Foo $foo)
    {
    }
}

class Baz
{
    public function __construct(Bar $bar)
    {
    }
}
