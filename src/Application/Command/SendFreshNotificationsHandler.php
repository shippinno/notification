<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

use Shippinno\Notification\Domain\Model\NotificationRepository;
use Shippinno\Notification\Domain\Model\SendNotification as SendNotificationService;

class SendFreshNotificationsHandler
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var SendNotificationService
     */
    private $sendNotificationService;

    /**
     * @param NotificationRepository $notificationRepository
     * @param SendNotificationService $sendNotificationService
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        SendNotificationService $sendNotificationService
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->sendNotificationService = $sendNotificationService;
    }

    /**
     * @param SendFreshNotifications $command
     */
    public function handle(SendFreshNotifications $command): void
    {
        $notifications = $this->notificationRepository->unsentNotifications();
        foreach ($notifications as $notification) {
            $this->sendNotificationService->execute($notification);
        }
    }
}
