<?php

namespace Shippinno\Notification\Application\Command;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\Body;
use Shippinno\Notification\Domain\Model\Gateway;
use Shippinno\Notification\Domain\Model\GatewayRegistry;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationRepository;
use Shippinno\Notification\Domain\Model\SendNotification as SendNotificationService;
use Shippinno\Notification\Domain\Model\SlackChannelDestination;
use Shippinno\Notification\Domain\Model\Subject;
use Shippinno\Notification\Infrastructure\Domain\Model\InMemoryNotificationRepository;

class SendFreshNotificationsHandlerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var NotificationRepository */
    private $notificationRepository;

    /** @var GatewayRegistry */
    private $gatewayRegistry;

    /** @var SendFreshNotificationsHandler */
    private $handler;

    public function setUp()
    {
        $this->gatewayRegistry = new GatewayRegistry;
        $this->notificationRepository = new InMemoryNotificationRepository;
        $this->handler = new SendFreshNotificationsHandler(
            $this->notificationRepository,
            new SendNotificationService($this->gatewayRegistry)
        );
    }

    public function testItSendsFreshNotifications()
    {
        $destination = new SlackChannelDestination('channel');
        $fresh1 = new Notification(
            $destination,
            new Subject('subject'),
            new Body('body')
        );
        $fresh2 = new Notification(
            $destination,
            new Subject('subject'),
            new Body('body')
        );
        $sent = new Notification(
            $destination,
            new Subject('subject'),
            new Body('body')
        );
        $sent->markSent();
        $failed = new Notification(
            $destination,
            new Subject('subject'),
            new Body('body')
        );
        $failed->markFailed('Some reason.');
        $this->notificationRepository->add($fresh1);
        $this->notificationRepository->add($fresh2);
        $this->notificationRepository->add($sent);
        $this->notificationRepository->add($failed);
        $gateway = Mockery::spy(Gateway::class);
        $this->gatewayRegistry->set($destination::type(), $gateway);
        $this->handler->handle(new SendFreshNotifications);
        $gateway
            ->shouldHaveReceived('send')
            ->twice();
        $this->assertTrue($this->notificationRepository->notificationOfId($fresh1->notificationId())->isSent());
        $this->assertTrue($this->notificationRepository->notificationOfId($fresh2->notificationId())->isSent());
    }
}