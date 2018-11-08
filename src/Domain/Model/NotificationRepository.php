<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

interface NotificationRepository
{
    /**
     * @param Notification $notification
     * @throws DeduplicationException
     */
    public function add(Notification $notification): void;

    /**
     * @param Notification $notification
     */
    public function persist(Notification $notification): void;

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
    public function freshNotifications(): array;
}
