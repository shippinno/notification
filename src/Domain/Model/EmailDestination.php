<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Tanigami\ValueObjects\Web\EmailAddress;

class EmailDestination extends Destination
{
    /**
     * @var string[]
     */
    private $to;

    /**
     * @var string[]
     */
    private $cc;

    /**
     * @var string[]
     */
    private $bcc;

    /**
     * @param EmailAddress[] $to
     * @param EmailAddress[] $cc
     * @param EmailAddress[] $bcc
     */
    public function __construct(array $to, array $cc = [], array $bcc = [])
    {
        $this->to = $this->toStringArray($to);
        $this->cc = $this->toStringArray($cc);
        $this->bcc = $this->toStringArray($bcc);
    }

    /**
     * @return EmailAddress[]
     */
    public function to(): array
    {
        return $this->toObjectArray($this->to);
    }

    /**
     * @return EmailAddress[]
     */
    public function cc(): array
    {
        return $this->toObjectArray($this->cc);
    }

    /**
     * @return EmailAddress[]
     */
    public function bcc(): array
    {
        return $this->toObjectArray($this->bcc);
    }

    /**
     * @param EmailAddress[] $emailAddresses
     * @return string[[
     */
    private function toStringArray(array $emailAddresses)
    {
        return array_map(function (EmailAddress $emailAddress) {
            return $emailAddress->emailAddress();
        }, $emailAddresses);
    }

    /**
     * @param string[] $emailAddresses
     * @return EmailAddress[]
     */
    private function toObjectArray(array $emailAddresses): array
    {
        return array_map(function (string $emailAddress) {
            return new EmailAddress($emailAddress);
        }, $emailAddresses);
    }

    /**
     * {@inheritdoc}
     */
    public function destinationType(): string
    {
        return get_class($this);
    }
}
