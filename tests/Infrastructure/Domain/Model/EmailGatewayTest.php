<?php

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Shippinno\Email\EmailNotSentException;
use Shippinno\Email\SendEmail;
use Shippinno\Notification\Domain\Model\Body;
use Shippinno\Notification\Domain\Model\Destination;
use Shippinno\Notification\Domain\Model\EmailDestination;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\Subject;
use Tanigami\ValueObjects\Web\Email;
use Tanigami\ValueObjects\Web\EmailAddress;

class EmailGatewayTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testItSendsNotificationAsEmail()
    {
        $notification = new Notification(
            new EmailDestination(
                [new EmailAddress('to@example.com')],
                [new EmailAddress('cc@example.com')],
                [new EmailAddress('bcc@example.com')]
            ),
            new Subject('SUBJECT'),
            new Body('BODY')
        );
        $sendEmail = Mockery::spy(SendEmail::class);
        $gateway = new EmailGateway($sendEmail, new EmailAddress('from@example.com'));
        $gateway->send($notification);
        $sendEmail
            ->shouldHaveReceived('execute')
            ->with(Mockery::on(function (Email $email) {
                return
                    $email->subject() == 'SUBJECT' &&
                    $email->body() == 'BODY' &&
                    $email->from()->equals(new EmailAddress('from@example.com')) &&
                    $email->tos() == [new EmailAddress('to@example.com')] &&
                    $email->ccs() == [new EmailAddress('cc@example.com')] &&
                    $email->bccs() == [new EmailAddress('bcc@example.com')];
            }));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid destination type: INVALID_TYPE
     */
    public function testItThrowsExceptionIfDestinationTypeIsInvalid()
    {
        $destination = Mockery::mock(Destination::class);
        $destination->shouldReceive(['type' => 'INVALID_TYPE']);
        $notification = new Notification($destination, new Subject('subject'), new Body('body'));
        $gateway = new EmailGateway(
            Mockery::mock(SendEmail::class),
            new EmailAddress('from@example.com')
        );
        $gateway->send($notification);
    }

    /**
     * @expectedException \Shippinno\Notification\Domain\Model\NotificationNotSentException
     * @expectedExceptionMessage SUBJECT
     */
    public function testItThrowsExceptionIfNotificationWasNotSent()
    {
        $destination = Mockery::mock(Destination::class);
        $destination->shouldReceive(['destinationType' => 'INVALID_TYPE']);
        $notification = new Notification(
            new EmailDestination([new EmailAddress('to@example.com')]),
            new Subject('SUBJECT'),
            new Body('body')
        );
        $sendEmail = Mockery::mock(SendEmail::class);
        $sendEmail->shouldReceive('execute')->andThrow(new EmailNotSentException());
        $gateway = new EmailGateway($sendEmail, new EmailAddress('from@example.com'));
        $gateway->send($notification);
    }
}
