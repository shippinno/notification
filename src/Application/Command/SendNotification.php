<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

class SendNotification
{
    /**
     * @var int
     */
    private $notificationId;

    /**
     * @param int $notificationId
     */
    public function __construct(int $notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * @return int
     */
    public function notificationId(): int
    {
        return $this->notificationId;
    }
}
