<?php

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\Body;
use Shippinno\Notification\Domain\Model\DeduplicationKey;
use Shippinno\Notification\Domain\Model\EmailDestination;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\Subject;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationBodyType;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationDeduplicationKeyType;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationIdType;
use Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type\NotificationSubjectType;
use Tanigami\ValueObjects\Web\EmailAddress;

class DoctrineNotificationRepositoryTest extends TestCase
{
    /** @var PrecociousDoctrineNotificationRepository $repository */
    private $repository;

    public function setUp()
    {
        $this->repository = $this->createRepository();
    }

    private function createRepository()
    {
        $this->addCustomTypes();
        $em = $this->initEntityManager();
        $this->initSchema($em);
        return new PrecociousDoctrineNotificationRepository($em, $em->getClassMetaData(Notification::class));
    }

    private function addCustomTypes()
    {
        if (!Type::hasType('notification_id')) {
            Type::addType('notification_id', NotificationIdType::class);
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
        return EntityManager::create(
            ['url' => 'sqlite:///:memory:'],
            Setup::createXMLMetadataConfiguration(
                [__DIR__.'/../../../../src/Infrastructure/Persistence/Doctrine/Mapping'],
                $devMode = true
            )
        );
    }

    public function testItHasNotificationAfterAdded()
    {
        $notification = new Notification(
            NotificationId::create(),
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
        $this->assertNotNull($this->repository->notificationOfId($notification->notificationId()));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Notification of deduplication key (DEDUPLICATION_KEY) already exists.
     */
    public function testThatNotificationWithExistingDeduplicationKeyCannotBeAdded()
    {
        $deduplicationKey = new DeduplicationKey('DEDUPLICATION_KEY');
        $notification1 = new Notification(
            NotificationId::create(),
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('SUBJECT'),
            new Body('BODY'),
            $deduplicationKey
        );
        $notification2 = new Notification(
            NotificationId::create(),
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('SUBJECT'),
            new Body('BODY'),
            $deduplicationKey
        );
        $this->repository->add($notification1);
        $this->assertTrue($this->repository->hasNotificationOfDeduplicationKey($deduplicationKey));
        $this->repository->add($notification2);
    }
}

class PrecociousDoctrineNotificationRepository extends DoctrineNotificationRepository
{
    public function add(Notification $notification): void
    {
        parent::add($notification);
        $this->getEntityManager()->flush();
    }
}
