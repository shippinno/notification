<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use DateTime;
use Exception;
use Throwable;

class NotificationNotSentException extends Exception
{
    /**
     * @var Notification
     */
    private $notification;

    /**
     * @param Notification $notification
     * @param Throwable|null $previous
     */
    public function __construct(Notification $notification, Throwable $previous = null)
    {
        $this->notification = $notification;
        parent::__construct(
            sprintf(
                'Failed to send notification: "%s" created at %s',
                $notification->subject()->subject(),
                $notification->createdAt()->format(DateTime::W3C)
            ),
            0,
            $previous
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
