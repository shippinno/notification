<?php

namespace Shippinno\Notification\Domain\Model;

use Mockery;
use PHPUnit\Framework\TestCase;
use Tanigami\ValueObjects\Web\EmailAddress;

class SendNotificationTest extends TestCase
{
    public function test()
    {
        $notification = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('subject'),
            new Body('body')
        );
        $gateway = Mockery::spy(Gateway::class);
        $gatewayRegistry = new GatewayRegistry;
        $gatewayRegistry->set($notification->destination()::type(), $gateway);
        $service = new SendNotification($gatewayRegistry);
        $service->execute($notification);
        $gateway->shouldHaveReceived('send')->once();
        $this->assertTrue($notification->isSent());
    }

    public function testNoGateway()
    {
        $notification = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('subject'),
            new Body('body')
        );
        $gatewayRegistry = new GatewayRegistry;
        $service = new SendNotification($gatewayRegistry);
        $service->execute($notification);
        $this->assertTrue($notification->isFailed());
        $this->assertContains('Gateway not found', $notification->failedFor());
    }

    public function testGatewayFail()
    {
        $notification = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('subject'),
            new Body('body')
        );
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
        $this->assertContains('Gateway failed to send', $notification->failedFor());
    }
}
