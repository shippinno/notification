<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

interface NotificationRepository
{
    /**
     * @param NotificationId $notificationId
     * @return null|Notification
     */
    public function notificationOfId(NotificationId $notificationId): ?Notification;

    /**
     * @param DeduplicationKey $deduplicationKey
     * @return bool
     */
    public function hasNotificationOfDeduplicationKey(DeduplicationKey $deduplicationKey): bool;

    /**
     * @param Notification $notification
     */
    public function add(Notification $notification): void;
}
