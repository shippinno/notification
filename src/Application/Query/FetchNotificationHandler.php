<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Query;

use Shippinno\Notification\Application\DataTransformer\NotificationDataTransformer;
use Shippinno\Notification\Domain\Model\NotificationId;
use Shippinno\Notification\Domain\Model\NotificationNotFoundException;
use Shippinno\Notification\Domain\Model\NotificationRepository;

class FetchNotificationHandler
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
     * @param FetchNotification $query
     * @return mixed
     * @throws NotificationNotFoundException
     */
    public function handle(FetchNotification $query)
    {
        $notificationId = new NotificationId($query->notificationId());
        $notification = $this->notificationRepository->notificationOfId($notificationId);
        if (is_null($notification)) {
            throw new NotificationNotFoundException($notificationId);
        }
        $this->notificationDataTransformer->write($notification);

        return $this->notificationDataTransformer->read();
    }
}
