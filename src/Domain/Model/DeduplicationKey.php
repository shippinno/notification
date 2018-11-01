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
     * @param DeduplicationKey $other
     * @return bool
     */
    public function equals(DeduplicationKey $other): bool
    {
        return $this->key() === $other->key();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->key();
    }
}
