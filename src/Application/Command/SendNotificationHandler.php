<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationNotFoundException;
use Shippinno\Notification\Domain\Model\NotificationRepository;
use Shippinno\Notification\Domain\Model\SendNotification as SendNotificationService;

class SendNotificationHandler
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
        $this->sendNotificationService->execute($notification);
        $this->notificationRepository->persist($notification);
    }
}
