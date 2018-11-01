<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use InvalidArgumentException;
use Verraes\ClassFunctions\ClassFunctions;

class GatewayRegistry
{
    /**
     * @var Gateway[]
     */
    protected $gateways = [];

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
        $destinationType = ClassFunctions::short($destination);
        if (isset($this->gateways[$destinationType])) {
            new InvalidArgumentException(
                sprintf(
                    'No gateway for destination type (%s) is supported.',
                    $destinationType
                )
            );
        }

        return $this->gateways[$destinationType];
    }
}
