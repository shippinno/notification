<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use InvalidArgumentException;

class SendNotification
{
    /**
     * @var GatewayRegistry
     */
    private $gatewayRegistry;

    /**
     * @param GatewayRegistry $gatewayRegistry
     */
    public function __construct(GatewayRegistry $gatewayRegistry)
    {
        $this->gatewayRegistry = $gatewayRegistry;
    }

    /**
     * @param Notification $notification
     */
    public function execute(Notification $notification): void
    {
        try {
            $gateway = $this->gatewayRegistry->get($notification->destination());
        } catch (InvalidArgumentException $e) {
            $notification->markFailed('Gateway not found: ' . $e->__toString());

            return;
        }
        try {
            $gateway->send($notification);
            $notification->markSent();
        } catch (NotificationNotSentException $e) {
            $notification->markFailed('Gateway failed to send: ' . $e->__toString());
        }
    }
}
