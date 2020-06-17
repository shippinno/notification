<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Tanigami\Specification\Specification;

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
     * @param Specification|null $specification
     * @param array|null $orderings
     * @param int|null $maxResults
     * @param int|null $firstResult
     * @return Notification[]
     */
    public function query(
        Specification $specification = null,
        array $orderings = null,
        int $maxResults = null,
        int $firstResult = null
    ): array;

    /**
     * @param Notification $notification
     */
    public function remove(Notification $notification): void;

    /**
     * @param Notification[] $notifications
     */
    public function removeAll(array $notifications): void;
}
