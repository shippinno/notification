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
     * @param DeduplicationKey $deduplicationKey
     * @return bool
     */
    public function hasNotificationOfDeduplicationKey(DeduplicationKey $deduplicationKey): bool;

    /**
     * @return Notification[]
     */
    public function unsentNotifications(): array;
}
