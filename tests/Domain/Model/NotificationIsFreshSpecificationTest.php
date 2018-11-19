<?php

namespace Shippinno\Notification\Domain\Model;

use PHPUnit\Framework\TestCase;

class NotificationIsFreshSpecificationTest extends TestCase
{
    public function testPositiveSpecification()
    {
        $this->assertTrue(
            (new NotificationIsFreshSpecification)
                ->isSatisfiedBy($this->freshNotification())
        );
        $this->assertFalse(
            (new NotificationIsFreshSpecification)
                ->isSatisfiedBy($this->lockedNotification())
        );
        $this->assertFalse(
            (new NotificationIsFreshSpecification)
                ->isSatisfiedBy($this->failedNotification())
        );
        $this->assertFalse(
            (new NotificationIsFreshSpecification)
                ->isSatisfiedBy($this->sentNotification())
        );
        $this->assertSame(
            '(a.lockedAt IS NULL) AND (a.failedAt IS NULL) AND (a.sentAt IS NULL)',
            (new NotificationIsFreshSpecification)->whereExpression('a')
        );
    }

    public function testNegativeSpecification()
    {
        $this->assertTrue(
            (new NotificationIsFreshSpecification(false))
                ->isSatisfiedBy($this->lockedNotification())
        );
        $this->assertTrue(
            (new NotificationIsFreshSpecification(false))
                ->isSatisfiedBy($this->failedNotification())
        );
        $this->assertTrue(
            (new NotificationIsFreshSpecification(false))
                ->isSatisfiedBy($this->sentNotification())
        );
        $this->assertFalse(
            (new NotificationIsFreshSpecification(false))
                ->isSatisfiedBy($this->freshNotification())
        );
        $this->assertSame(
            '(a.lockedAt IS NOT NULL) OR (a.failedAt IS NOT NULL) OR (a.sentAt IS NOT NULL)',
            (new NotificationIsFreshSpecification(false))->whereExpression('a')
        );
    }

    private function freshNotification()
    {
        return NotificationBuilder::notification()->build();
    }

    private function lockedNotification()
    {
        $notification = NotificationBuilder::notification()->build();
        $notification->lock();
        return $notification;
    }

    private function failedNotification()
    {
        $notification = NotificationBuilder::notification()->build();
        $notification->markFailed('reason');
        return $notification;
    }

    private function sentNotification()
    {
        $notification = NotificationBuilder::notification()->build();
        $notification->markSent();
        return $notification;
    }
}
