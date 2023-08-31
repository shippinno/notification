<?php

namespace Shippinno\Notification\Domain\Model;

use Mockery;
use PHPUnit\Framework\TestCase;

class SendNotificationTest extends TestCase
{
    public function testItSendsNotification()
    {
        $notification = NotificationBuilder::notification()->build();
        $gateway = Mockery::spy(Gateway::class);
        $gatewayRegistry = new GatewayRegistry;
        $gatewayRegistry->set($notification->destination()::type(), $gateway);
        $service = new SendNotification($gatewayRegistry);
        $service->execute($notification);
        $gateway->shouldHaveReceived('send')->once();
        $this->assertTrue($notification->isSent());
    }

    public function testItFailsIfNotFound()
    {
        $notification = NotificationBuilder::notification()->build();
        $gatewayRegistry = new GatewayRegistry;
        $service = new SendNotification($gatewayRegistry);
        $service->execute($notification);
        $this->assertTrue($notification->isFailed());
        $this->assertStringContainsString('Gateway not found', $notification->failedFor());
    }

    public function testItFailsIfGatewayFailsToSendNotification()
    {
        $notification = NotificationBuilder::notification()->build();
        $exception = new NotificationNotSentException($notification);
        $gateway = Mockery::mock(Gateway::class);
        $gateway
            ->shouldReceive(['destinationType' => $notification->destination()::type()])
            ->shouldReceive('send')->andThrow($exception);
        $gatewayRegistry = new GatewayRegistry;
        $gatewayRegistry->set($notification->destination()::type(), $gateway);
        $service = new SendNotification($gatewayRegistry);
        $service->execute($notification);
        $this->assertTrue($notification->isFailed());
        $this->assertStringContainsString('Gateway failed to send', $notification->failedFor());
    }
}
