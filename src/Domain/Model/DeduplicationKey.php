<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

class DeduplicationKey
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->key();
    }
}
