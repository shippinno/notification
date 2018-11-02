<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

interface Destination
{
    /**
     * @return string
     */
    public function destinationType(): string;
}