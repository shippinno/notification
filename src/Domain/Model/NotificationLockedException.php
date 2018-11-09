<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use DateTime;
use Exception;

class NotificationLockedException extends Exception
{
    /**
     * @var Notification
     */
    private $notification;

    /**
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
        parent::__construct(
            sprintf(
                'Cannot send locked notification: "%s" created at %s',
                $this->notification->subject()->subject(),
                $this->notification->createdAt()->format(DateTime::W3C)
            )
        );
    }

    /**
     * @return Notification
     */
    public function notification(): Notification
    {
        return $this->notification;
    }
}
