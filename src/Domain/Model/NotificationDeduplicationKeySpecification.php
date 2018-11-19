<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Tanigami\Specification\Specification;

class NotificationDeduplicationKeySpecification extends Specification
{
    /**
     * @var DeduplicationKey
     */
    private $deduplicationKey;

    /**
     * @param DeduplicationKey $deduplicationKey
     */
    public function __construct(DeduplicationKey $deduplicationKey)
    {
        $this->deduplicationKey = $deduplicationKey;
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function isSatisfiedBy($notification): bool
    {
        if (is_null($notification->deduplicationKey())) {
            return false;
        }

        return $notification->deduplicationKey()->equals($this->deduplicationKey);
    }

    /**
     * {@inheritdoc}
     */
    public function whereExpression(string $alias): string
    {
        return sprintf("%s.deduplicationKey = '%s'", $alias, $this->deduplicationKey->key());
    }
}
