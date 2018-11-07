<?php

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Mockery;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\DeduplicationKey;

class NotificationDeduplicationKeyTypeTest extends TestCase
{
    public function testItConverts()
    {
        $type = Mockery::mock(NotificationDeduplicationKeyType::class)->makePartial();
        $phpValue = new DeduplicationKey('key');
        $databaseValue = $type->convertToDatabaseValue($phpValue);
        $this->assertSame($phpValue->key(), $databaseValue);
        $phpValueConverted = $type->convertToPHPValue($databaseValue);
        $this->assertSame($phpValue->key(), $phpValueConverted->key());
    }

    public function testItConvertsNull()
    {
        $type = Mockery::mock(NotificationDeduplicationKeyType::class)->makePartial();
        $this->assertNull($type->convertToDatabaseValue(null));
        $this->assertNull($type->convertToPHPValue(null));
    }
}