<?php

namespace Shippinno\Notification\Application\Query;

use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Application\DataTransformer\NotificationDtoDataTransformer;
use Shippinno\Notification\Domain\Model\DeduplicationKey;
use Shippinno\Notification\Domain\Model\NotificationBuilder;
use Shippinno\Notification\Domain\Model\NotificationDeduplicationKeySpecification;
use Shippinno\Notification\Domain\Model\NotificationIsFreshSpecification;
use Shippinno\Notification\Infrastructure\Domain\Model\InMemoryNotificationRepository;

class FilterNotificationsHandlerTest extends TestCase
{
    public function testItFiltersNotifications()
    {
        $notificationRepository = new InMemoryNotificationRepository;
        $notificationRepository->add(
            NotificationBuilder::notification()->build()
        );
        $notificationRepository->add(
            NotificationBuilder::notification()
                ->withDeduplicationKey('DEDUPLICATION_KEY')
                ->build()
        );
        $handler = new FilterNotificationsHandler(
            $notificationRepository,
            new NotificationDtoDataTransformer
        );
        $result = $handler->handle(
            new FilterNotifications(
                (new NotificationIsFreshSpecification)
                    ->and(new NotificationDeduplicationKeySpecification(new DeduplicationKey('DEDUPLICATION_KEY')))
            )
        );
        $this->assertCount(1, $result);
        $this->assertSame('DEDUPLICATION_KEY', $result[0]['deduplication_key']);
    }
}
