<?php
declare(strict_types=1);

namespace Shippinno\Notification\Application\Command;

class SendFreshNotifications
{
    /**
     * @var array
     */
    private $metadataSpecs;

    /**
     * @param array $metadataSpecs
     */
    public function __construct(array $metadataSpecs = [])
    {
        $this->metadataSpecs = $metadataSpecs;
    }

    /**
     * @return array
     */
    public function metadataSpecs(): array
    {
        return $this->metadataSpecs;
    }
}
