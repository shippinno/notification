<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Shippinno\Notification\Domain\Model\DeduplicationException;
use Shippinno\Notification\Domain\Model\DeduplicationKey;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationDeduplicationKeySpecification;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationRepository;
use Tanigami\Specification\Specification;

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
            throw new DeduplicationException($deduplicationKey);
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
        return !empty($this->query(new NotificationDeduplicationKeySpecification($deduplicationKey)));
    }

    /**
     * {@inheritdoc}
     */
    public function freshNotifications(): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.sentAt IS NULL')
            ->andWhere('n.failedAt IS NULL')
            ->andWhere('n.lockedAt IS NULL')
            ->orderBy('n.notificationId', 'ASC')
            ->getQuery()
            ->getResult();
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
        $queryBuilder =  $this->createQueryBuilder('n');
        if (!is_null($specification)) {
            $queryBuilder->where($specification->whereExpression('n'));
        }
        if (!is_null($orderings)) {
            foreach ($orderings as $sort => $order) {
                $queryBuilder->orderBy('n.' . $sort, $order);
            }
        }
        if (!is_null($maxResults)) {
            $queryBuilder->setMaxResults($maxResults);
        }
        if (!is_null($firstResult)) {
            $queryBuilder->setFirstResult($firstResult);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     * @throws ORMException
     */
    public function remove(Notification $notification): void
    {
        $this->getEntityManager()->remove($notification);
    }

    /**
     * {@inheritdoc}
     * @throws ORMException
     */
    public function removeAll(array $notifications): void
    {
        foreach ($notifications as $notification) {
            $this->remove($notification);
        }
    }
}
