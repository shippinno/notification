<?php
declare(strict_types=1);

namespace Shippinno\Notification\Domain\Model;

use Shippinno\Email\SmtpConfiguration;
use Tanigami\ValueObjects\Web\EmailAddress;

class EmailDestination extends Destination
{
    /**
     * @var string[]
     */
    protected $to;

    /**
     * @var string[]
     */
    protected $cc;

    /**
     * @var string[]
     */
    protected $bcc;

    /**
     * @var SmtpConfiguration
     */
    protected $smtpConfiguration;

    /**
     * @var null|string
     */
    protected $from;

    /**
     * @param array $to
     * @param array $cc
     * @param array $bcc
     * @param SmtpConfiguration|null $smtpConfiguration
     * @param EmailAddress|null $from
     */
    public function __construct(
        array $to,
        array $cc = [],
        array $bcc = [],
        SmtpConfiguration $smtpConfiguration = null,
        EmailAddress $from = null
    ) {
        $this->to = self::toStringArray($to);
        $this->cc = self::toStringArray($cc);
        $this->bcc = self::toStringArray($bcc);
        $this->smtpConfiguration = $smtpConfiguration;
        $this->from = is_null($from) ? null : $from->emailAddress();
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
     * @return null|SmtpConfiguration
     */
    public function smtpConfiguration(): ?SmtpConfiguration
    {
        return $this->smtpConfiguration;
    }

    /**
     * @return null|EmailAddress
     */
    public function from(): ?EmailAddress
    {
        return is_null($this->from) ? null : new EmailAddress($this->from, true);
    }

    /**
     * @param EmailAddress[] $emailAddresses
     * @return string[]
     */
    protected static function toStringArray(array $emailAddresses)
    {
        return array_map(function (EmailAddress $emailAddress) {
            return $emailAddress->emailAddress();
        }, $emailAddresses);
    }

    /**
     * @param string[] $emailAddresses
     * @return EmailAddress[]
     */
    protected static function toObjectArray(array $emailAddresses): array
    {
        return array_map(function (string $emailAddress) {
            return new EmailAddress($emailAddress, true);
        }, $emailAddresses);
    }

    /**
     * @return string
     */
    public function jsonRepresentation(): string
    {
        $smtpConfiguration = is_null($this->smtpConfiguration) ? [] : [
            'host' => $this->smtpConfiguration->host(),
            'port' => $this->smtpConfiguration->port(),
            'username' => $this->smtpConfiguration->username(),
            'password' => $this->smtpConfiguration->password(),
        ];

        return json_encode([
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'smtpConfiguration' => $smtpConfiguration,
            'from' => $this->from
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
            self::toObjectArray($decoded['bcc']),
            $decoded['smtpConfiguration'] === []
                ? null
                : new SmtpConfiguration(
                    $decoded['smtpConfiguration']['host'],
                    $decoded['smtpConfiguration']['port'],
                    $decoded['smtpConfiguration']['username'],
                    $decoded['smtpConfiguration']['password']
                ),
            is_null($decoded['from']) ? null : new EmailAddress($decoded['from'], true)
        );
    }
}
