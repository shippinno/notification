<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

use Shippinno\Notification\Domain\Model\GatewayRegistry;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationNotFoundException;
use Shippinno\Notification\Domain\Model\NotificationRepository;

class SendNotificationHandler
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
     * @param SendNotification $command
     * @throws NotificationNotFoundException
     */
    public function handle(SendNotification $command): void
    {
        $notificationId = new NotificationId($command->notificationId());
        $notification = $this->notificationRepository->notificationOfId($notificationId);
        if (is_null($notification)) {
            throw new NotificationNotFoundException($notificationId);
        }
        $this->gatewayRegistry->get($notification->destination())->send($notification);
        $notification->markSent();
    }
}
