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
        $destinationType = $notification->destination()->destinationType();
        if ($destinationType !== $this->destinationType()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid destination type: %s, expected: %s',
                    $destinationType,
                    $this->destinationType()
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
     * @return string
     */
    abstract public function destinationType(): string;
}