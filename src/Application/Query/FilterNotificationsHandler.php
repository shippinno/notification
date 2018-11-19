<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Query;

use Shippinno\Notification\Application\DataTransformer\NotificationDataTransformer;
use Shippinno\Notification\Domain\Model\Notification;
use Shippinno\Notification\Domain\Model\NotificationRepository;

class FilterNotificationsHandler
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var NotificationDataTransformer
     */
    private $notificationDataTransformer;

    /**
     * @param NotificationRepository $notificationRepository
     * @param NotificationDataTransformer $notificationDataTransformer
     */
    public function __construct(
        NotificationRepository $notificationRepository,
        NotificationDataTransformer $notificationDataTransformer
    ) {
        $this->notificationRepository = $notificationRepository;
        $this->notificationDataTransformer = $notificationDataTransformer;
    }

    /**
     * @param FilterNotifications $query
     * @return array
     */
    public function handle(FilterNotifications $query): array
    {
        $notifications = $this->notificationRepository->query(
            $query->specification(),
            $query->orderings(),
            $query->maxResults(),
            $query->firstResult()
        );

        return array_map(function (Notification $notification) {
            $this->notificationDataTransformer->write($notification);
            return $this->notificationDataTransformer->read();
        }, $notifications);
    }
}
