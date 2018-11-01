<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use DateTimeImmutable;
use LogicException;

class Notification
{
    /**
     * @var NotificationId
     */
    protected $notificationId;

    /**
     * @var Destination
     */
    protected $destination;

    /**
     * @var Subject
     */
    protected $subject;

    /**
     * @var Body
     */
    protected $body;

    /**
     * @var null|DeduplicationKey
     */
    protected $deduplicationKey;

    /**
     * @var null|string
     */
    protected $templateName;

    /**
     * @var array|null
     */
    protected $templateVariables;

    /**
     * @var DateTimeImmutable
     */
    protected $createdAt;

    /**
     * @var null|DateTimeImmutable
     */
    protected $sentAt;

    /**
     * @param NotificationId $notificationId
     * @param Destination $destination
     * @param Subject $subject
     * @param Body $body
     * @param DeduplicationKey|null $deduplicationKey
     * @param string|null $templateName
     * @param array|null $templateVariables
     */
    public function __construct(
        NotificationId $notificationId,
        Destination $destination,
        Subject $subject,
        Body $body,
        DeduplicationKey $deduplicationKey = null,
        string $templateName = null,
        array $templateVariables = null
    ) {
        $this->notificationId = $notificationId;
        $this->destination = $destination;
        $this->subject = $subject;
        $this->body = $body;
        $this->deduplicationKey = $deduplicationKey;
        $this->templateName = $templateName;
        $this->templateVariables = $templateVariables;
        $this->createdAt = new DateTimeImmutable;
        $this->sentAt = null;
    }

    /**
     * @return NotificationId
     */
    public function notificationId(): NotificationId
    {
        return $this->notificationId;
    }

    /**
     * @return Destination
     */
    public function destination(): Destination
    {
        return $this->destination;
    }

    /**
     * @return Subject
     */
    public function subject(): Subject
    {
        return $this->subject;
    }

    /**
     * @return Body
     */
    public function body(): Body
    {
        return $this->body;
    }

    /**
     * @return null|DeduplicationKey
     */
    public function deduplicationKey(): ?DeduplicationKey
    {
        return $this->deduplicationKey;
    }

    /**
     * @return DateTimeImmutable
     */
    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function sentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return !is_null($this->sentAt());
    }

    /**
     * @return bool
     */
    public function markSent(): bool
    {
        if ($this->isSent()) {
            throw new LogicException(sprintf('Notification is already sent: %s', $this->notificationId->id()));
        }
        $this->sentAt = new DateTimeImmutable;
    }
}


