<?php

namespace Shippinno\Notification\Domain\Model;

use PHPUnit\Framework\TestCase;

class NotificationTest extends TestCase
{
    public function testItMatchesMetadataSpecs()
    {
        $notification = NotificationBuilder::notification()
            ->withMetadata([
                'a' => ['b' => 'c'],
                'd' => 'e',
            ])
            ->build();
        $this->assertTrue($notification->matchesMetadataSpecs(['a.b' => 'c']));
        $this->assertFalse($notification->matchesMetadataSpecs(['a.b' => 'd']));
        $this->assertFalse($notification->matchesMetadataSpecs(['a.b' => 'c', 'd' => 'z']));
        $this->assertTrue($notification->matchesMetadataSpecs([]));
        $this->assertFalse($notification->matchesMetadataSpecs(['e.f' => 'g']));
    }
}