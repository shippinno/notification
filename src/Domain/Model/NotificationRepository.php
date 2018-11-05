<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

interface NotificationRepository
{
    /**
     * @param Notification $notification
     */
    public function add(Notification $notification): void;

    /**
     * @param Notification $notification
     */
    public function markSent(Notification $notification): void;

    /**
     * @param Notification $notification
     * @param string $reason
     */
    public function markFailed(Notification $notification, string $reason): void;

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
     * @return Notification[]
     */
    public function unsentNotifications(): array;
}
