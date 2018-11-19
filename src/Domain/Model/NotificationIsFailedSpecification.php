<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Tanigami\Specification\Specification;

class NotificationIsFailedSpecification extends Specification
{
    /**
     * @var bool
     */
    private $expectsToBeFailed;

    /**
     * @param bool $expectsToBeFailed
     */
    public function __construct(bool $expectsToBeFailed = true)
    {
        $this->expectsToBeFailed = $expectsToBeFailed;
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function isSatisfiedBy($notification): bool
    {
        return $this->expectsToBeFailed === $notification->isFailed();
    }

    /**
     * {@inheritdoc}
     */
    public function whereExpression(string $alias): string
    {
        return sprintf(
            $this->expectsToBeFailed
                ? '%s.failedAt IS NOT NULL'
                : '%s.failedAt IS NULL',
            $alias
        );
    }
}
