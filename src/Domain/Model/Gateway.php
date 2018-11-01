<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

interface Gateway
{
    /**
     * @return string
     */
    public function destinationType(): string;

    /**
     * @param Notification $notification
     * @return mixed
     */
    public function send(Notification $notification);
}