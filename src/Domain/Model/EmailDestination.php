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
        $this->to = self::toStringArray($to);
        $this->cc = self::toStringArray($cc);
        $this->bcc = self::toStringArray($bcc);
    }

    /**
     * @return EmailAddress[]
     */
    public function to(): array
    {
        return self::toObjectArray($this->to);
    }

    /**
     * @return EmailAddress[]
     */
    public function cc(): array
    {
        return self::toObjectArray($this->cc);
    }

    /**
     * @return EmailAddress[]
     */
    public function bcc(): array
    {
        return self::toObjectArray($this->bcc);
    }

    /**
     * @param EmailAddress[] $emailAddresses
     * @return string[[
     */
    private static function toStringArray(array $emailAddresses)
    {
        return array_map(function (EmailAddress $emailAddress) {
            return $emailAddress->emailAddress();
        }, $emailAddresses);
    }

    /**
     * @param string[] $emailAddresses
     * @return EmailAddress[]
     */
    private static function toObjectArray(array $emailAddresses): array
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

    /**
     * @return string
     */
    public function jsonRepresentation(): string
    {
        return json_encode([
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromJsonRepresentation(string $jsonRepresentation): Destination
    {
        $decoded = json_decode($jsonRepresentation, true);

        return new EmailDestination(
            self::toObjectArray($decoded['to']),
            self::toObjectArray($decoded['cc']),
            self::toObjectArray($decoded['bcc'])
        );
    }
}
