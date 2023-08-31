<?php

namespace Shippinno\Notification\Application\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\Gateway;
use Shippinno\Notification\Domain\Model\GatewayRegistry;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationBuilder;
use Shippinno\Notification\Domain\Model\NotificationNotFoundException;
use Shippinno\Notification\Domain\Model\NotificationRepository;
use Shippinno\Notification\Domain\Model\SendNotification as SendNotificationService;
use Shippinno\Notification\Infrastructure\Domain\Model\InMemoryNotificationRepository;

class SendNotificationHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var NotificationRepository */
    private $notificationRepository;

    /** @var GatewayRegistry */
    private $gatewayRegistry;

    /** @var SendNotificationHandler */
    private $handler;

    public function setUp(): void
    {
        $this->gatewayRegistry = new GatewayRegistry;
        $this->notificationRepository = new InMemoryNotificationRepository;
        $this->handler = new SendNotificationHandler(
            $this->notificationRepository,
            new SendNotificationService($this->gatewayRegistry)
        );
    }

    public function testItSendsNotification()
    {
        $notification = NotificationBuilder::notification()->build();
        $this->notificationRepository->add($notification);
        $gateway = Mockery::spy(Gateway::class);
        $this->gatewayRegistry->set($notification->destination()::type(), $gateway);
        $this->handler->handle(new SendNotification($notification->notificationId()->id()));
        $gateway
            ->shouldHaveReceived('send')
            ->with(Mockery::on(function (Notification $aNotification) use ($notification) {
                return $aNotification->notificationId()->equals($notification->notificationId());
            }))
            ->once();
        $sentNotification = $this->notificationRepository->notificationOfId($notification->notificationId());
        $this->assertTrue($sentNotification->isSent());
    }

    /**
     * @expectedException \Shippinno\Notification\Domain\Model\NotificationNotFoundException
     * @expectedExceptionMessage Notification (123) does not exist.
     */
    public function testItThrowsExceptionIfNotificationDoesNotExist()
    {
        $this->expectException(NotificationNotFoundException::class);
        $this->handler->handle(new SendNotification(123));
    }
}
