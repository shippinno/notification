<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use InvalidArgumentException;

abstract class Gateway
{
    /**
     * @param Notification $notification
     * @throws NotificationNotSentException
     */
    public function send(Notification $notification): void
    {
        $destination = $notification->destination();
        if (!$this->sendsToDestination($destination)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid destination type: %s',
                    $destination::type()
                )
            );
        }
        $this->doSend($notification);
    }

    /**
     * @param Notification $notification
     * @throws NotificationNotSentException
     */
    abstract protected function doSend(Notification $notification): void;

    /**
     * @param Destination $destination
     * @return bool
     */
    abstract public function sendsToDestination(Destination $destination): bool;
}