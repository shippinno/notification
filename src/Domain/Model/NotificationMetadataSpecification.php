<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Adbar\Dot;
use Doctrine\Common\Collections\Expr\Comparison;
use RuntimeException;
use Tanigami\Specification\Specification;

class NotificationMetadataSpecification extends Specification
{
    /**
     * @var Comparison
     */
    private $expression;

    /**
     * @param string $key
     * @param string $operator
     * @param mixed $value
     */
    public function __construct(string $key, string $operator, $value)
    {
        $this->expression = new Comparison($key, $operator, $value);
    }

    /**
     * @param Notification $notification
     * @return bool
     */
    public function isSatisfiedBy($notification): bool
    {
        $value = (new Dot($notification->metadata()))->get($this->expression->getField());
        if (is_null($value)) {
            return false;
        }
        $operator = $this->expression->getOperator();
        $expressionValue = $this->expression->getValue()->getValue();
        switch ($operator) {
            case Comparison::EQ:
                return $value === $expressionValue;
            case Comparison::NEQ:
                return $value !== $expressionValue;
            case Comparison::CONTAINS:
                return strpos($value, $expressionValue) !== false;
            case Comparison::GT:
                return $value > $expressionValue;
            case Comparison::GTE:
                return $value >= $expressionValue;
            case Comparison::LT:
                return $value < $expressionValue;
            case Comparison::LTE:
                return $value <= $expressionValue;
            case Comparison::IN:
                return in_array($value, $expressionValue, true);
            default:
                throw new RuntimeException(sprintf('Unknown operator: %s', $operator));
        }
    }
}
