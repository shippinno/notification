<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

use Tanigami\Specification\Specification;

class SendFreshNotifications
{
    /**
     * @var null|Specification
     */
    private $specification;

    /**
     * @param Specification|null $specification
     */
    public function __construct(Specification $specification = null)
    {
        $this->specification = $specification;
    }

    /**
     * @return null|Specification
     */
    public function specification(): ?Specification
    {
        return $this->specification;
    }
}
