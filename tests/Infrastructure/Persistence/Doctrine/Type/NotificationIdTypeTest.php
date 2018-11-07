<?php

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Mockery;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\NotificationId;

class NotificationIdTypeTest extends TestCase
{
    public function testItConverts()
    {
        $type = Mockery::mock(NotificationIdType::class)->makePartial();
        $phpValue = new NotificationId(1);
        $databaseValue = $type->convertToDatabaseValue($phpValue);
        $this->assertSame($phpValue->id(), $databaseValue);
        $phpValueConverted = $type->convertToPHPValue($databaseValue);
        $this->assertSame($phpValue->id(), $phpValueConverted->id());
    }
}
