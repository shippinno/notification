<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

class SlackChannelDestination extends Destination
{
    /**
     * @var string
     */
    private $channel;

    /**
     * @param string $channel
     */
    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function channel(): string
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function destinationType(): string
    {
        return get_class($this);
    }
}
