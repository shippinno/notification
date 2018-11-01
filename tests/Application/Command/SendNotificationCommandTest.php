<?php

namespace Shippinno\Notification\Application\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\Body;
use Shippinno\Notification\Domain\Model\EmailDestination;
use Shippinno\Notification\Domain\Model\Gateway;
use Shippinno\Notification\Domain\Model\GatewayRegistry;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationRepository;
use Shippinno\Notification\Domain\Model\Subject;
use Shippinno\Notification\Infrastructure\Domain\Model\InMemoryNotificationRepository;
use Tanigami\ValueObjects\Web\EmailAddress;

class SendNotificationCommandTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var NotificationRepository */
    private $notificationRepository;

    /** @var Gateway */
    private $gateway;

    /** @var GatewayRegistry */
    private $gatewayRegistry;

    /** @var SendNotificationHandler */
    private $handler;

    public function setUp()
    {
        $this->gateway = Mockery::spy(Gateway::class);
        $this->gateway->shouldReceive(['destinationType' => 'EmailDestination']);
        $this->gatewayRegistry = new GatewayRegistry;
        $this->gatewayRegistry->set($this->gateway);
        $this->notificationRepository = new InMemoryNotificationRepository;
        $this->handler = new SendNotificationHandler(
            $this->notificationRepository,
            $this->gatewayRegistry
        );
    }

    public function testItSendsNotification()
    {
        $notification = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('subject'),
            new Body('body')
        );
        $this->notificationRepository->add($notification);
        $this->handler->handle(new SendNotification(1));
        $this->assertTrue($notification->isSent());
    }

    /**
     * @expectedException \Shippinno\Notification\Domain\Model\NotificationNotFoundException
     * @expectedExceptionMessage Notification (123) does not exits.
     */
    public function testItThrowsExceptionIfNotificationDoesNotExist()
    {
        $this->handler->handle(new SendNotification(123));
    }
}
