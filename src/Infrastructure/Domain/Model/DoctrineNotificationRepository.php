<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Doctrine\ORM\EntityRepository;
use LogicException;
use Shippinno\Notification\Domain\Model\DeduplicationKey;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationRepository;

class DoctrineNotificationRepository extends EntityRepository implements NotificationRepository
{
    /**
     * {@inheritdoc}
     */
    public function add(Notification $notification): void
    {
        $deduplicationKey = $notification->deduplicationKey();
        if (!is_null($deduplicationKey) && $this->hasNotificationOfDeduplicationKey($deduplicationKey)) {
            throw new LogicException(
                sprintf(
                    'Notification of deduplication key (%s) already exists.',
                    $deduplicationKey
                )
            );
        }
        $this->getEntityManager()->persist($notification);
    }

    /**
     * {@inheritdoc}
     */
    public function hasNotificationOfDeduplicationKey(DeduplicationKey $deduplicationKey): bool
    {
        return !is_null($this->findOneBy(['deduplicationKey' => $deduplicationKey]));
    }

    /**
     * {@inheritdoc}
     */
    public function unsentNotifications(): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.sentAt IS NULL')
            ->orderBy('n.notificationId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
