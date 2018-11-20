<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Shippinno\Notification\Domain\Model\NotificationIsFreshSpecification;
use Shippinno\Notification\Domain\Model\NotificationRepository;
use Shippinno\Notification\Domain\Model\SendNotification as SendNotificationService;

class SendFreshNotificationsHandler
{
    use LoggerAwareTrait;

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
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        SendNotificationService $sendNotificationService,
        LoggerInterface $logger = null
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->sendNotificationService = $sendNotificationService;
        $this->logger = is_null($logger) ? new NullLogger : $logger;
    }

    /**
     * @param SendFreshNotifications $command
     */
    public function handle(SendFreshNotifications $command): void
    {
        $notifications = $this->notificationRepository->query(
            (new NotificationIsFreshSpecification)->and($command->specification()),
            ['notificationId' => 'ASC'],
            100
        );
        $this->logger->debug(sprintf('Sending %s fresh notifications.', count($notifications)));
        $sent = 0;
        foreach ($notifications as $notification) {
            $this->sendNotificationService->execute($notification);
            if ($notification->isSent()) {
                $sent = $sent + 1;
            }
            $this->notificationRepository->persist($notification);
        }
        $this->logger->debug(sprintf('Sent %s notifications successfully.', $sent));
    }
}
