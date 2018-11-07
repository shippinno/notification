<?php

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Mockery;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\Body;

class NotificationBodyTypeTest extends TestCase
{
    public function testItConverts()
    {
        $type = Mockery::mock(NotificationBodyType::class)->makePartial();
        $phpValue = new Body('body');
        $databaseValue = $type->convertToDatabaseValue($phpValue);
        $this->assertSame($phpValue->body(), $databaseValue);
        $phpValueConverted = $type->convertToPHPValue($databaseValue);
        $this->assertSame($phpValue->body(), $phpValueConverted->body());
    }
}