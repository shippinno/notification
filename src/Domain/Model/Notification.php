<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use DateTime;
use DateTimeImmutable;
use LogicException;

class Notification
{
    /**
     * @var null|NotificationId
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
     * @var null|DateTimeImmutable
     */
    protected $failedAt;

    /**
     * @var null|string
     */
    protected $failedFor;

    /**
     * @var null|DateTimeImmutable
     */
    protected $lockedAt;

    /**
     * @param Destination $destination
     * @param Subject $subject
     * @param Body $body
     * @param DeduplicationKey|null $deduplicationKey
     * @param string|null $templateName
     * @param array|null $templateVariables
     */
    public function __construct(
        Destination $destination,
        Subject $subject,
        Body $body,
        DeduplicationKey $deduplicationKey = null,
        string $templateName = null,
        array $templateVariables = null
    ) {
        $this->destination = $destination;
        $this->subject = $subject;
        $this->body = $body;
        $this->deduplicationKey = $deduplicationKey;
        $this->templateName = $templateName;
        $this->templateVariables = $templateVariables;
        $this->createdAt = new DateTimeImmutable;
        $this->sentAt = null;
        $this->failedAt = null;
        $this->failedFor = null;
        $this->lockedAt = null;
    }

    /**
     * @return null|NotificationId
     */
    public function notificationId(): ?NotificationId
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
     * @return void
     */
    public function markSent(): void
    {
        $this->assertNotSent();
        $this->unmarkFailed();
        $this->sentAt = new DateTimeImmutable;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function failedAt(): ?DateTimeImmutable
    {
        return $this->failedAt;
    }

    /**
     * @return null|string
     */
    public function failedFor(): ?string
    {
        return $this->failedFor;
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        return !is_null($this->failedAt());
    }

    /**
     * @param string $reason
     */
    public function markFailed(string $reason): void
    {
        $this->assertNotSent();
        $this->failedAt = new DateTimeImmutable;
        $this->failedFor = $reason;
    }

    /**
     * @return void
     */
    public function unmarkFailed(): void
    {
        $this->failedAt = null;
        $this->failedFor = null;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function lockedAt(): ?DateTimeImmutable
    {
        return $this->lockedAt;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return !is_null($this->lockedAt());
    }

    public function lock(): void
    {
        $this->lockedAt = new DateTimeImmutable;
    }

    /**
     * @return void
     */
    public function unlock(): void
    {
        $this->lockedAt = null;
    }

    /**
     * @return bool
     */
    public function isFresh(): bool
    {
        return !$this->isLocked() && !$this->isFailed() &&  !$this->isSent();
    }

    /**
     * @return void
     */
    private function assertNotSent(): void
    {
        if ($this->isSent()) {
            throw new LogicException(
                sprintf(
                    'Notification is already sent: "%s" created at %s',
                    $this->subject()->subject(),
                    $this->createdAt()->format(DateTime::W3C)
                )
            );
        }
    }

    /**
     * For InMemoryNotificationRepository only.
     *
     * @param NotificationId $notificationId
     */
    public function setNotificationId(NotificationId $notificationId): void
    {
        $this->notificationId = $notificationId;
    }
}

