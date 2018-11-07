<?php

namespace Shippinno\Notification\Domain\Model;

use InvalidArgumentException;

class DestinationRegistry
{
    /**
     * @var null|DestinationRegistry
     */
    protected static $instance = null;

    /**
     * @var Destination[]
     */
    protected $destinations = [];

    /**
     * @return DestinationRegistry
     */
    public static function instance(): DestinationRegistry
    {
        if (null === static::$instance) {
            static::$instance = new DestinationRegistry;
        }

        return static::$instance;
    }

    /**
     * @param string $name
     * @param Destination $destination
     */
    public function set(string $name, Destination $destination): void
    {
        $this->destinations[$name] = $destination;
    }

    /**
     * @param array $destinations
     */
    public function setAll(array $destinations): void
    {
        $this->destinations = $destinations;
    }

    /**
     * @param string $name
     * @return Destination
     */
    public function get(string $name): Destination
    {
        if (!isset($this->destinations[$name])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No destination is registered with name (%s).',
                    $name
                )
            );
        }

        return $this->destinations[$name];
    }
}
