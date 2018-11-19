<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Tanigami\Specification\Specification;

class NotificationIsSentSpecification extends Specification
{
    /**
     * @var bool
     */
    private $expectsToBeSent;

    /**
     * @param bool $expectsToBeSent
     */
    public function __construct(bool $expectsToBeSent = true)
    {
        $this->expectsToBeSent = $expectsToBeSent;
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function isSatisfiedBy($notification): bool
    {
        return $this->expectsToBeSent === $notification->isSent();
    }

    /**
     * {@inheritdoc}
     */
    public function whereExpression(string $alias): string
    {
        return sprintf(
            $this->expectsToBeSent
                ? '%s.sentAt IS NOT NULL'
                : '%s.sentAt IS NULL',
            $alias
        );
    }
}
