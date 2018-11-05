<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use InvalidArgumentException;

class GatewayRegistry
{
    /**
     * @var null|GatewayRegistry
     */
    private static $instance = null;

    /**
     * @var Gateway[]
     */
    protected $gateways = [];

    /**
     * @return GatewayRegistry
     */
    public static function instance(): GatewayRegistry
    {
        if (null === static::$instance) {
            static::$instance = new GatewayRegistry;
        }

        return static::$instance;
    }

    /**
     * @param Gateway $gateway
     */
    public function set(Gateway $gateway)
    {
        $this->gateways[$gateway->destinationType()] = $gateway;
    }

    /**
     * @param Destination $destination
     * @return Gateway
     */
    public function get(Destination $destination): Gateway
    {
        $destinationType = $destination->destinationType();
        if (!isset($this->gateways[$destinationType])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No gateway for destination type (%s) is supported.',
                    $destinationType
                )
            );
        }

        return $this->gateways[$destinationType];
    }
}
