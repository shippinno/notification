<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

use Tanigami\Specification\Specification;

class SendFreshNotifications
{
    /**
     * @var Specification
     */
    private $specification;

    /**
     * @param Specification $specification
     */
    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
    }

    /**
     * @return Specification
     */
    public function specification(): Specification
    {
        return $this->specification;
    }
}
