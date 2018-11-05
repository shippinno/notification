<?php

namespace Shippinno\Notification\Domain\Model;

trait Notifier
{
    /**
     * @var TemplateNotificationFactory
     */
    protected $factory;

    /**
     * @var NotificationRepository
     */
    protected $notificationRepository;

    protected function notify(
        Destination $destination,
        DeduplicationKey $deduplicationKey
    ) {
        $notification = $this->factory->createFromTemplate($n, $v, $destination, $deduplicationKey);
        $this->notificationRepository->add($notification);
    }
}