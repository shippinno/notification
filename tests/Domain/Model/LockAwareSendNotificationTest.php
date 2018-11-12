<?php

namespace Shippinno\Notification\Domain\Model;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Infrastructure\Domain\Model\InMemoryNotificationRepository;

class LockAwareSendNotificationTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @expectedException \Shippinno\Notification\Domain\Model\NotificationLockedException
     */
    public function testItThrowsExceptionIfNotificationIsLocked()
    {
        $notification = NotificationBuilder::notification()->build();
        $notification->lock();
        $gateway = Mockery::mock(Gateway::class);
        $gatewayRegistry = new GatewayRegistry;
        $gatewayRegistry->set($notification->destination()::type(), $gateway);
        $service = new LockAwareSendNotification(
            $gatewayRegistry,
            new InMemoryNotificationRepository
        );
        $service->execute($notification);
    }

    public function testsItPersistsNotificationLockedBeforeAndUnlockedAfter()
    {
        $notification = NotificationBuilder::notification()->build();
        $gateway = Mockery::mock(Gateway::class);
        $gateway->shouldReceive('send')->once();
        $gatewayRegistry = new GatewayRegistry;
        $gatewayRegistry->set($notification->destination()::type(), $gateway);
        $notificationRepository = Mockery::mock(NotificationRepository::class);
        $notificationRepository
            ->shouldReceive('persist')
            ->with(Mockery::on(function (Notification $notification) {
                return $notification->isLocked() && !$notification->isSent();
            }))
            ->once()
            ->shouldReceive('persist')
            ->with(Mockery::on(function (Notification $notification) {
                return !$notification->isLocked() && $notification->isSent();
            }))
            ->once();
        $service = new LockAwareSendNotification(
            $gatewayRegistry,
            $notificationRepository
        );
        $service->execute($notification);
    }
}