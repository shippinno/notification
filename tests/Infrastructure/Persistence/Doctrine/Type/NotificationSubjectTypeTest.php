<?php

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Mockery;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\Subject;

class NotificationSubjectTypeTest extends TestCase
{
    public function testItConverts()
    {
        $type = Mockery::mock(NotificationSubjectType::class)->makePartial();
        $phpValue = new Subject('subject');
        $databaseValue = $type->convertToDatabaseValue($phpValue);
        $this->assertSame($phpValue->subject(), $databaseValue);
        $phpValueConverted = $type->convertToPHPValue($databaseValue);
        $this->assertSame($phpValue->subject(), $phpValueConverted->subject());
    }
}