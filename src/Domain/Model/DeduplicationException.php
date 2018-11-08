<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Exception;

class DeduplicationException extends Exception
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
        parent::__construct(
            sprintf(
                'Notification of deduplication key (%s) already exists.',
                $this->deduplicationKey()->key()
            )
        );
    }

    /**
     * @return DeduplicationKey
     */
    public function deduplicationKey(): DeduplicationKey
    {
        return $this->deduplicationKey;
    }
}
