<?php

namespace itechTest\Components\Injection;


/**
 * Class Container
 *
 * @package itechTest\Components\Injection
 */
class Container implements \ArrayAccess
{
    /**
     * @var Container
     */
    private static $instance;
    /**
     * @var array
     */
    private $entries = [];

    /**
     * @param mixed $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet($id)
    {
        $instance = self::$instance;
        if (!isset($instance->entries[$id])) {
            throw new \Exception($id);
        }

        $action = $instance->entries[$id];
        return \is_callable($action) ? $action($instance) : $action;
    }

    /**
     * Checks if a parameter or an object is set.
     *
     * @param string $id The unique identifier for the parameter or object
     *
     * @return bool
     * @throws \Exception
     */
    public function offsetExists($id): bool
    {
        return isset(self::getInstance()->entries[$id]);
    }

    /**
     * @param array $values
     *
     * @return Container
     */
    public static function getInstance(array $values = []): Container
    {
        if (null === self::$instance) {
            self::$instance = new static;
        }

        foreach ($values as $key => $value) {
            try {
                self::$instance->offsetSet($key, $value);
            } catch (\Exception $exception) {
                // do nothing for now
            }
        }


        return self::$instance;
    }

    /**
     * @param Container $container
     */
    public static function setInstance(Container $container): void
    {
        self::$instance = $container;
    }

    /**
     * @param mixed $id
     * @param mixed $value
     */
    public function offsetSet($id, $value): void
    {
        $instance = self::$instance;
        $instance->entries[$id] = $value;
    }

    /**
     * @param mixed $id
     */
    public function offsetUnset($id): void
    {
        $instance = self::getInstance();
        unset($instance->entries[$id]);
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return \array_keys(self::getInstance()->entries);
    }
}