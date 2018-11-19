<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Tanigami\Specification\Specification;

class NotificationIsLockedSpecification extends Specification
{
    /**
     * @var bool
     */
    private $expectsToBeLocked;

    /**
     * @param bool $expectsToBeLocked
     */
    public function __construct(bool $expectsToBeLocked = true)
    {
        $this->expectsToBeLocked = $expectsToBeLocked;
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function isSatisfiedBy($notification): bool
    {
        return $this->expectsToBeLocked === $notification->isLocked();
    }

    /**
     * {@inheritdoc}
     */
    public function whereExpression(string $alias): string
    {
        return sprintf(
            $this->expectsToBeLocked
                ? '%s.lockedAt IS NOT NULL'
                : '%s.lockedAt IS NULL',
            $alias
        );
    }
}
