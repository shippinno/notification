<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Exception;

class NotificationNotFoundException extends Exception
{
    /**
     * @var NotificationId
     */
    private $notificationId;

    /**
     * NotificationNotFoundException constructor.
     * @param NotificationId $notificationId
     */
    public function __construct(NotificationId $notificationId)
    {
        $this->notificationId = $notificationId;
        parent::__construct(
            sprintf(
                'Notification (%s) does not exist.',
                $this->notificationId()->id()
            )
        );
    }

    /**
     * @return NotificationId
     */
    public function notificationId(): NotificationId
    {
        return $this->notificationId;
    }
}
