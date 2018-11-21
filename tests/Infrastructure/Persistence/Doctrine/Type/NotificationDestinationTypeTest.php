<?php

namespace Shippinno\Notification\Infrastructure\Persistence\Doctrine\Type;

use Mockery;
use PHPUnit\Framework\TestCase;
use Shippinno\Email\SmtpConfiguration;
use Shippinno\Notification\Domain\Model\EmailDestination;
use Shippinno\Notification\Domain\Model\SlackChannelDestination;
use Tanigami\ValueObjects\Web\EmailAddress;

class NotificationDestinationTypeTest extends TestCase
{
    public function testItConvertsEmailDestination()
    {
        $type = Mockery::mock(NotificationDestinationType::class)->makePartial();
        $phpValue = new EmailDestination(
            [new EmailAddress('to1@example.com'), new EmailAddress('to2@example.com')],
            [new EmailAddress('cc1@example.com'), new EmailAddress('cc2@example.com')],
            [new EmailAddress('bcc1@example.com'), new EmailAddress('bcc2@example.com')],
            new SmtpConfiguration('HOST', 25, 'USERNAME', 'PASSWORD')
        );
        $databaseValue = $type->convertToDatabaseValue($phpValue);
        $this->assertEquals(
            [
                'type' => 'EmailDestination',
                'to' => ['to1@example.com', 'to2@example.com'],
                'cc' => ['cc1@example.com', 'cc2@example.com'],
                'bcc' => ['bcc1@example.com', 'bcc2@example.com'],
                'smtpConfiguration' => [
                    'host' => 'HOST',
                    'port' => 25,
                    'username' => 'USERNAME',
                    'password' => 'PASSWORD',
                ],
            ],
            json_decode($databaseValue, true)
        );
        $phpValueConverted = $type->convertToPHPValue($databaseValue);
        $this->assertInstanceOf(EmailDestination::class, $phpValueConverted);
        $this->assertEquals(
            [new EmailAddress('to1@example.com'), new EmailAddress('to2@example.com')],
            $phpValueConverted->to()
        );
        $this->assertEquals(
            [new EmailAddress('cc1@example.com'), new EmailAddress('cc2@example.com')],
            $phpValueConverted->cc()
        );
        $this->assertEquals(
            [new EmailAddress('bcc1@example.com'), new EmailAddress('bcc2@example.com')],
            $phpValueConverted->bcc()
        );
        $this->assertEquals('HOST', $phpValueConverted->smtpConfiguration()->host());
        $this->assertEquals(25, $phpValueConverted->smtpConfiguration()->port());
        $this->assertEquals('USERNAME', $phpValueConverted->smtpConfiguration()->username());
        $this->assertEquals('PASSWORD', $phpValueConverted->smtpConfiguration()->password());
    }

    public function testItConvertsSlackChannelDestination()
    {
        $type = Mockery::mock(NotificationDestinationType::class)->makePartial();
        $phpValue = new SlackChannelDestination('CHANNEL');
        $databaseValue = $type->convertToDatabaseValue($phpValue);
        $this->assertEquals(
            [
                'type' => 'SlackChannelDestination',
                'channel' => 'CHANNEL',
            ],
            json_decode($databaseValue, true)
        );
        $phpValueConverted = $type->convertToPHPValue($databaseValue);
        $this->assertInstanceOf(SlackChannelDestination::class, $phpValueConverted);
        $this->assertEquals('CHANNEL', $phpValueConverted->channel());
    }
}