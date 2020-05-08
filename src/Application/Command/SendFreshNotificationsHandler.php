<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationIsFreshSpecification;
use Shippinno\Notification\Domain\Model\NotificationRepository;
use Shippinno\Notification\Domain\Model\SendNotification as SendNotificationService;

class SendFreshNotificationsHandler
{
    use LoggerAwareTrait;

    /**
     * @var NotificationRepository
     */
    protected $notificationRepository;

    /**
     * @var SendNotificationService
     */
    protected $sendNotificationService;

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
        $specification = new NotificationIsFreshSpecification;
        if (!is_null($command->specification())) {
            $specification = $specification->and($command->specification());
        }
        $notifications = $this->notificationRepository->query(
            $specification,
            null,
            100
        );
        $this->logger->debug(sprintf('Sending %s fresh notifications.', count($notifications)));
        $sent = 0;
        foreach ($notifications as $notification) {
            $this->send($notification);
            $sent++;
        }
        $this->logger->debug(sprintf('Sent %s notifications successfully.', $sent));
    }

    /**
     * @param Notification $notification
     */
    protected function send(Notification $notification): void
    {
        $this->sendNotificationService->execute($notification);
        $this->notificationRepository->persist($notification);
    }
}
