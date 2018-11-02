<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

abstract class Destination
{
    /**
     * @return string
     */
    abstract public function destinationType(): string;
}