<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use LogicException;
use Shippinno\Notification\Domain\Model\DeduplicationKey;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationRepository;

class DoctrineNotificationRepository extends EntityRepository implements NotificationRepository
{
    /**
     * @var bool
     */
    private $isPrecocious;

    /**
     * {@inheritdoc}
     * @param bool $isPrecocious
     */
    public function __construct($em, ClassMetadata $class, bool $isPrecocious)
    {
        $this->isPrecocious = $isPrecocious;
        parent::__construct($em, $class);
    }

    /**
     * {@inheritdoc}
     */
    public function add(Notification $notification, bool $precocious = false): void
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
        $this->persist($notification);
    }

    /**
     * {@inheritdoc}
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function persist(Notification $notification): void
    {
        $this->getEntityManager()->persist($notification);
        if ($this->isPrecocious) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function notificationOfId(NotificationId $notificationId): ?Notification
    {
        return $this->find($notificationId);
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
    public function freshNotifications(): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.sentAt IS NULL')
            ->orderBy('n.notificationId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
