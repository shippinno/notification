<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Verraes\ClassFunctions\ClassFunctions;

abstract class Destination
{
    /**
     * @return string
     */
    abstract public function jsonRepresentation(): string;

    /**
     * @return string
     */
    public static function type(): string
    {
        return ClassFunctions::short(static::class);
    }

    /**
     * @return string
     */
    public function typedJsonRepresentation(): string
    {
        $decoded = json_decode($this->jsonRepresentation(), true);
        $decoded['type'] = self::type();

        return json_encode($decoded);
    }

    /**
     * @param string $stringRepresentation
     * @return Destination
     */
    abstract public static function fromJsonRepresentation(string $stringRepresentation): Destination;
}