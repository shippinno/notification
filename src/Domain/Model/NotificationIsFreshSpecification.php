<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Tanigami\Specification\AnyOfSpecification;
use Tanigami\Specification\OneOfSpecification;
use Tanigami\Specification\Specification;

class NotificationIsFreshSpecification extends Specification
{
    /**
     * @var bool
     */
    private $expectsToBeFresh;

    /**
     * @param bool $expectsToBeFresh
     */
    public function __construct(bool $expectsToBeFresh = true)
    {
        $this->expectsToBeFresh = $expectsToBeFresh;
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function isSatisfiedBy($notification): bool
    {
        return $this->expectsToBeFresh === $notification->isFresh();
    }

    /**
     * {@inheritdoc}
     */
    public function whereExpression(string $alias): string
    {
        $specifications = [
            new NotificationIsLockedSpecification(!$this->expectsToBeFresh),
            new NotificationIsFailedSpecification(!$this->expectsToBeFresh),
            new NotificationIsSentSpecification(!$this->expectsToBeFresh),
        ];
        $composite = $this->expectsToBeFresh
            ? new AnyOfSpecification(...$specifications)
            : new OneOfSpecification(...$specifications);

        return $composite->whereExpression($alias);
    }
}
