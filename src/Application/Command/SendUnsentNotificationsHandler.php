<?php

namespace Shippinno\Notification\Application\Command;

use Shippinno\Notification\Domain\Model\GatewayRegistry;
use Shippinno\Notification\Domain\Model\NotificationNotSentException;
use Shippinno\Notification\Domain\Model\NotificationRepository;

class SendUnsentNotificationsHandler
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var GatewayRegistry
     */
    private $gatewayRegistry;

    /**
     * @param NotificationRepository $notificationRepository
     * @param GatewayRegistry $gatewayRegistry
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        GatewayRegistry $gatewayRegistry
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->gatewayRegistry = $gatewayRegistry;
    }

    /**
     * @param SendUnsentNotifications $command
     * @throws NotificationNotSentException
     */
    public function handle(SendUnsentNotifications $command): void
    {
        $notifications = $this->notificationRepository->unsentNotifications();
        foreach ($notifications as $notification) {
            $gateway = $this->gatewayRegistry->get($notification->destination());
            $gateway->send($notification);
            $notification->markSent();
        }
    }
}
