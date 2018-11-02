<?php

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Maknz\Slack\Client;
use Maknz\Slack\Message;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Shippinno\Notification\Domain\Model\Body;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\SlackChannelDestination;
use Shippinno\Notification\Domain\Model\Subject;

class SlackGatewayTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test()
    {
        $destination = new SlackChannelDestination('random');
        $notification = new Notification(
            $destination,
            new Subject('SUBJECT'),
            new Body('BODY')
        );
        $client = Mockery::spy(Client::class);
        $gateway = new SlackGateway($client);
        $gateway->send($notification);
        $client
            ->shouldHaveReceived('sendMessage')
            ->once()
            ->with(Mockery::on(function (Message $message) {
                return
                    $message->getChannel() === 'random' &&
                    $message->getText() === "SUBJECT\n\nBODY";
            }));
    }


}
