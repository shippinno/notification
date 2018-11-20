<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Shippinno\Notification\Domain\Model\DeduplicationKey;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationDeduplicationKeySpecification;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationRepository;
use Tanigami\Specification\Specification;

class InMemoryNotificationRepository implements NotificationRepository
{
    /**
     * @var Notification[]
     */
    private $notifications = [];

    /**
     * @var int
     */
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
        $notification->setNotificationId($this->nextIdentity());
        $this->persist($notification);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Notification $notification): void
    {
        $this->notifications[$notification->notificationId()->id()] = $notification;
    }

    /**
     * {@inheritdoc}
     */
    public function notificationOfId(NotificationId $notificationId): ?Notification
    {
        if (!isset($this->notifications[$notificationId->id()])) {
            return null;
        }

        return clone $this->notifications[$notificationId->id()];
    }

    /**
     * {@inheritdoc}
     */
    public function hasNotificationOfDeduplicationKey(DeduplicationKey $deduplicationKey): bool
    {
        foreach ($this->notifications as $notification) {
            if ((new NotificationDeduplicationKeySpecification($deduplicationKey))->isSatisfiedBy($notification)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function query(
        Specification $specification = null,
        array $orderings = null,
        int $maxResults = null,
        int $firstResult = null
    ): array {
        $notifications = $this->notifications;
        if (!is_null($specification)) {
            $notifications =
                array_values(
                    array_filter(
                        $this->notifications,
                        function (Notification $notification) use ($specification) {
                            return $specification->isSatisfiedBy($notification);
                        }
                    )
                );
        }

        // No max results or first result implementation

        return $notifications;
    }

    /**
     * @return NotificationId
     */
    private function nextIdentity(): NotificationId
    {
        return new NotificationId($this->nextIdentity++);
    }
}
