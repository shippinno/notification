<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Query;

use Tanigami\Specification\Specification;

class FilterNotifications
{
    /**
     * @var Specification
     */
    private $specification;

    /**
     * @var array|null
     */
    private $orderings;

    /**
     * @var int|null
     */
    private $maxResults;

    /**
     * @var int|null
     */
    private $firstResult;

    /**
     * @param Specification $specification
     * @param array|null $orderings
     * @param int|null $maxResults
     * @param int|null $firstResult
     */
    public function __construct(
        Specification $specification,
        array $orderings = null,
        int $maxResults = null,
        int $firstResult = null
    ) {
        $this->specification = $specification;
        $this->orderings = $orderings;
        $this->maxResults = $maxResults;
        $this->firstResult = $firstResult;
    }

    /**
     * @return Specification
     */
    public function specification(): Specification
    {
        return $this->specification;
    }

    /**
     * @return array|null
     */
    public function orderings(): ?array
    {
        return $this->orderings;
    }

    /**
     * @return int|null
     */
    public function maxResults(): ?int
    {
        return $this->maxResults;
    }

    /**
     * @return int|null
     */
    public function firstResult(): ?int
    {
        return $this->firstResult;
    }
}
