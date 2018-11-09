<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

class LockAwareSendNotification extends SendNotification
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @param GatewayRegistry $gatewayRegistry
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(
        GatewayRegistry $gatewayRegistry,
        NotificationRepository $notificationRepository
    ) {
        parent::__construct($gatewayRegistry);
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * {@inheritdoc}
     * @throws NotificationLockedException
     */
    public function execute(Notification $notification): void
    {
        if ($notification->isLocked()) {
            throw new NotificationLockedException($notification);
        }
        $notification->lock();
        $this->notificationRepository->persist($notification);
        parent::execute($notification);
        $notification->unlock();
        $this->notificationRepository->persist($notification);
    }
}
