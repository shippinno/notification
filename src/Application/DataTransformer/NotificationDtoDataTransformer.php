<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\DataTransformer;

use DateTime;
use Shippinno\Notification\Domain\Model\Notification;

class NotificationDtoDataTransformer implements NotificationDataTransformer
{
    /**
     * @var Notification
     */
    private $notification;

    /**
     * {@inheritdoc}
     */
    public function write(Notification $notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @return array
     */
    public function read()
    {
        return [
            'id' => $this->notification->notificationId()->id(),
            'destination' => json_decode($this->notification->destination()->typedJsonRepresentation(), true),
            'subject' => $this->notification->subject()->subject(),
            'body' => $this->notification->body()->body(),
            'deduplication_key' => $this->notification->deduplicationKey()
                ? $this->notification->deduplicationKey()->key()
                : null,
            'metadata' => $this->notification->metadata(),
            'created_at' => $this->notification->createdAt()->format(DateTime::W3C),
            'failed_at' => $this->notification->failedAt()
                ? $this->notification->failedAt()->format(DateTime::W3C)
                : null,
            'failed_for' => $this->notification->failedFor(),
            'sent_at' => $this->notification->sentAt()
                ? $this->notification->sentAt()->format(DateTime::W3C)
                : null,
            'locked_at' => $this->notification->lockedAt()
                ? $this->notification->lockedAt()->format(DateTime::W3C)
                : null,
            'is_fresh' => $this->notification->isFresh(),
        ];
    }
}