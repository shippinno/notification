<?php

namespace Shippinno\Notification\Application\Query;

use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Application\DataTransformer\NotificationDtoDataTransformer;
use Shippinno\Notification\Domain\Model\NotificationBuilder;
use Shippinno\Notification\Domain\Model\NotificationNotFoundException;
use Shippinno\Notification\Infrastructure\Domain\Model\InMemoryNotificationRepository;

class FetchNotificationHandlerTest extends TestCase
{
    public function testItFetchesNotification()
    {
        $notificationRepository = new InMemoryNotificationRepository;
        $notificationRepository->add(
            NotificationBuilder::notification()->build()
        );
        $handler = new FetchNotificationHandler(
            $notificationRepository,
            new NotificationDtoDataTransformer
        );
        $result = $handler->handle(new FetchNotification(1));
        $this->assertSame(1, $result['notification_id']);
    }

    /**
     * @expectedException \Shippinno\Notification\Domain\Model\NotificationNotFoundException
     */
    public function testItThrowsExceptionIfNotificationNotFound()
    {
        $handler = new FetchNotificationHandler(
            new InMemoryNotificationRepository,
            new NotificationDtoDataTransformer
        );
        $this->expectException(NotificationNotFoundException::class);
        $handler->handle(new FetchNotification(1));
    }
}
