<?php
declare(strict_types=1);

namespace Shippinno\Notification\Infrastructure\Domain\Model;

use Maknz\Slack\Client;
use Maknz\Slack\Message;
use Shippinno\Notification\Domain\Model\Destination;
use Shippinno\Notification\Domain\Model\Gateway;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationNotSentException;
use Shippinno\Notification\Domain\Model\SlackChannelDestination;
use Throwable;

class SlackGateway extends Gateway
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSend(Notification $notification): void
    {
        /** @var SlackChannelDestination $destination */
        $destination = $notification->destination();
        $text = $notification->subject()->subject()."\n\n";
        $text .= $notification->body()->body();
        $message = new Message($this->client);
        $message->setAllowMarkdown(true);
        $message->setText($text);
        if ($destination instanceof SlackChannelDestination) {
            $message->setChannel($destination->channel());
        }
        try {
            $message->send();
        } catch (Throwable $e) {
            throw new NotificationNotSentException($notification, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendsToDestination(Destination $destination): bool
    {
        return $destination instanceof SlackChannelDestination;
    }
}
