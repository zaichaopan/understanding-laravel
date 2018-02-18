<?php

namespace Src;

class Container
{
    protected $instances = [];

    public $bindings = [];

    protected function isShared($abstract)
    {
        return $this->bindings[$abstract]['shared'];
    }

    protected function isNotShared($abstract)
    {
        return !$this->isShared($abstract);
    }

    protected function hasBinding($abstract)
    {
        return array_key_exists($abstract, $this->bindings);
    }

    protected function hasInstance($abstract)
    {
        return array_key_exists($abstract, $this->instances);
    }

    public function instance($abstract, $concrete)
    {
        $this->instances[$abstract] = $concrete;

        return $this;
    }

    public function bind($abstract, $concrete, $shared = false)
    {
        $this->bindings[$abstract] = compact('concrete', 'shared');

        return $this;
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }

    public function make($abstract)
    {
        if ($this->hasBinding($abstract)) {
            if ($this->isNotShared($abstract)) {
                $resolve = $this->bindings[$abstract]['concrete'];
                return $resolve();
            }

            if ($this->hasInstance($abstract)) {
                return $this->instances[$abstract];
            }

            $resolver = $this->bindings[$abstract]['concrete'];

            return $this->instances[$abstract] = $resolver();
        }

        if ($this->hasInstance($abstract)) {
            return $this->instances[$abstract];
        }

        if ($instance = $this->autoResolve($abstract)) {
            return $instance;
        }

        throw new \Exception('Unable to resolve binding!');
    }

    public function autoResolve($abstract)
    {
        if (!class_exists($abstract)) {
            return false;
        }

        $class = new \ReflectionClass($abstract);

        if (!$class->isInstantiable()) {
            return false;
        }

        $constructor = $class->getConstructor();



        if (!$constructor) {
            return new $abstract;
        }

        $params = $constructor->getParameters();




        $argus = [];

        try {
            foreach ($params as $param) {
                $paramClass = $param->getClass()->getName();

                $argus[] = $this->make($paramClass);


            }
        } catch (\Exception $e) {
            throw new \Exception('Unable to resolve complex dependencies.');
        }

        return $class->newInstanceArgs($argus);
    }
}
