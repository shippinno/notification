<?php

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\Body;
use Shippinno\Notification\Domain\Model\DeduplicationKey;
use Shippinno\Notification\Domain\Model\EmailDestination;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationBuilder;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationIsFailedSpecification;
use Shippinno\Notification\Domain\Model\NotificationIsFreshSpecification;
use Shippinno\Notification\Domain\Model\NotificationIsSentAtSpecification;
use Shippinno\Notification\Domain\Model\NotificationIsSentSpecification;
use Shippinno\Notification\Domain\Model\NotificationMetadataSpecification;
use Shippinno\Notification\Domain\Model\Subject;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationBodyType;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationDeduplicationKeyType;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationDestinationType;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationIdType;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationSubjectType;
use Tanigami\ValueObjects\Web\EmailAddress;

class DoctrineNotificationRepositoryTest extends TestCase
{
    /** @var DoctrineNotificationRepository $repository */
    private $repository;

    /** @var EntityManager $entityManager */
    private $entityManager;

    public function setUp()
    {
        $this->addCustomTypes();
        $this->entityManager = $this->initEntityManager();
        $this->initSchema($this->entityManager);
        $this->repository = $this->createRepository();
    }

    private function createRepository()
    {
        return new DoctrineNotificationRepository(
            $this->entityManager,
            $this->entityManager->getClassMetaData(Notification::class),
            true
        );
    }

    private function addCustomTypes()
    {
        if (!Type::hasType('notification_id')) {
            Type::addType('notification_id', NotificationIdType::class);
        }
        if (!Type::hasType('notification_destination')) {
            Type::addType('notification_destination', NotificationDestinationType::class);
        }
        if (!Type::hasType('notification_subject')) {
            Type::addType('notification_subject', NotificationSubjectType::class);
        }
        if (!Type::hasType('notification_body')) {
            Type::addType('notification_body', NotificationBodyType::class);
        }
        if (!Type::hasType('notification_deduplication_key')) {
            Type::addType('notification_deduplication_key', NotificationDeduplicationKeyType::class);
        }
    }

    private function initSchema(EntityManager $em) {
        $tool = new SchemaTool($em);
        $tool->createSchema([
            $em->getClassMetadata(Notification::class),
        ]);
    }

    protected function initEntityManager()
    {
        $config = Setup::createXMLMetadataConfiguration(
            [__DIR__.'/../../../../src/Infrastructure/Persistence/Doctrine/Mapping'],
            $devMode = true
        );
        return EntityManager::create(
            ['url' => 'sqlite:///:memory:'],
            $config
        );
    }

    public function testItHasNotificationAfterAdded()
    {
        $notification = new Notification(
            new EmailDestination(
                [new EmailAddress('to1@example.com'), new EmailAddress('to2@example.com')],
                [new EmailAddress('cc1@example.com'), new EmailAddress('cc2@example.com')],
                [new EmailAddress('bcc1@example.com'), new EmailAddress('bcc2@example.com')]
            ),
            new Subject('SUBJECT'),
            new Body('BODY'),
            new DeduplicationKey('DEDUPLICATION_KEY')
        );
        $this->repository->add($notification);
        $this->assertCount(1, $this->repository->freshNotifications());
    }

    /**
     * @expectedException \Shippinno\Notification\Domain\Model\DeduplicationException
     * @expectedExceptionMessage Notification of deduplication key (DEDUPLICATION_KEY) already exists.
     */
    public function testThatNotificationWithExistingDeduplicationKeyCannotBeAdded()
    {
        $deduplicationKey = new DeduplicationKey('DEDUPLICATION_KEY');
        $notification1 = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('SUBJECT'),
            new Body('BODY'),
            $deduplicationKey
        );
        $notification2 = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('SUBJECT'),
            new Body('BODY'),
            $deduplicationKey
        );
        $this->repository->add($notification1);
        $this->assertTrue($this->repository->hasNotificationOfDeduplicationKey($deduplicationKey));
        $this->repository->add($notification2);
    }

    public function testItReturnsFreshNotificationsOrderedById()
    {
        $notification1 = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('SUBJECT'),
            new Body('BODY')
        );
        $notification2 = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('SUBJECT'),
            new Body('BODY')
        );
        $notification3 = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('SUBJECT'),
            new Body('BODY')
        );
        $this->repository->add($notification1);
        $this->repository->add($notification2);
        $this->repository->add($notification3);
        $notification1->markSent();
        $this->repository->persist($notification1);
        $unsentNotifications = $this->repository->freshNotifications();
        $this->assertCount(2, $unsentNotifications);
        $this->assertSame(2, $unsentNotifications[0]->notificationId()->id());
        $this->assertSame(3, $unsentNotifications[1]->notificationId()->id());
    }

    public function testItReturnsNotificationsMatchingSpecification()
    {
        $freshNotification = NotificationBuilder::notification()->build();
        $lockedNotification = NotificationBuilder::notification()->build();
        $lockedNotification->lock();
        $failedNotification = NotificationBuilder::notification()->build();
        $failedNotification->markFailed('reason');
        $sentNotification = NotificationBuilder::notification()->build();
        $sentNotification->markSent();
        $this->repository->add($freshNotification);
        $this->repository->add($lockedNotification);
        $this->repository->add($failedNotification);
        $this->repository->add($sentNotification);
        $specification = new NotificationIsFailedSpecification;
        $result = $this->repository->query($specification);
        $this->assertCount(1, $result);
        $this->assertTrue($result[0]->isFailed());
    }
}
