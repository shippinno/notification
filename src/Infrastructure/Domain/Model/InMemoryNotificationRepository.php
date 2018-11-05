<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Shippinno\Notification\Domain\Model\DeduplicationKey;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationRepository;

class InMemoryNotificationRepository implements NotificationRepository
{
    /**
     * @var Notification[]
     */
    private $notifications = [];

    private $nextIdentity = 1;

    /**
     * {@inheritdoc}
     */
    public function add(Notification $notification): void
    {
        $deduplicationKey = $notification->deduplicationKey();
        if (!is_null($deduplicationKey) && $this->hasNotificationOfDeduplicationKey($deduplicationKey)) {
            return;
        }
        $this->setNotification($notification);
    }

    /**
     * {@inheritdoc}
     */
    public function markSent(Notification $notification): void
    {
        $notification->markSent();
        $this->setNotification($notification);
    }

    /**
     * {@inheritdoc}
     */
    public function markFailed(Notification $notification, string $reason): void
    {
        $notification->markFailed($reason);
        $this->setNotification($notification);
    }

    /**
     * @param Notification $notification
     */
    private function setNotification(Notification $notification): void
    {
        $this->notifications[$this->nextIdentity()->id()] = $notification;
    }


    /**
     * {@inheritdoc}
     */
    public function notificationOfId(NotificationId $notificationId): ?Notification
    {
        if (!isset($this->notifications[$notificationId->id()])) {
            return null;
        }

        return $this->notifications[$notificationId->id()];
    }

    /**
     * {@inheritdoc}
     */
    public function hasNotificationOfDeduplicationKey(DeduplicationKey $deduplicationKey): bool
    {
        foreach ($this->notifications as $notification) {
            if ($notification->deduplicationKey()->equals($deduplicationKey)) {
                return true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsentNotifications(): array
    {
        return array_filter($this->notifications, function (Notification $notification) {
            return !$notification->isSent();
        });
    }

    /**
     * @return NotificationId
     */
    private function nextIdentity(): NotificationId
    {
        return new NotificationId($this->nextIdentity++);
    }
}
