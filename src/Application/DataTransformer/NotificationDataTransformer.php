<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\DataTransformer;

use Shippinno\Notification\Domain\Model\Notification;

interface NotificationDataTransformer
{
    /**
     * @param Notification $notification
     */
    public function write(Notification $notification): void;

    /**
     * @return mixed
     */
    public function read();
}