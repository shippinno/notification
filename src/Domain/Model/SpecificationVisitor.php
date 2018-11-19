<?php

namespace Shippinno\Notification\Domain\Model;

use Doctrine\Common\Collections\Criteria;
use Tanigami\Specification\AndSpecification;
use Tanigami\Specification\OrSpecification;
use Tanigami\Specification\Specification;

class SpecificationVisitor
{
    public function walkAnd(AndSpecification $specification): Criteria
    {
        return $this->dispatch($specification->one())->andWhere(
            $this->dispatch($specification->other())->getWhereExpression()
        );
    }

    public function walkOr(OrSpecification $specification): Criteria
    {
        return $this->dispatch($specification->one())->orWhere(
            $this->dispatch($specification->other())->getWhereExpression()
        );
    }

    public function dispatch(Specification $specification): Criteria
    {
        switch (true) {
            case ($specification instanceof AndSpecification):
                return $this->walkAnd($specification);
            case ($specification instanceof OrSpecification):
                return $this->walkOr($specification);
            case ($specification instanceof NotificationMetadataSpecification):
                return $specification->criteria();
            default:
                throw new \RuntimeException();
        }
    }
}
