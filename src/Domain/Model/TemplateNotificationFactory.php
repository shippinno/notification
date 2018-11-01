<?php

namespace Shippinno\Notification\Domain\Model;

use Shippinno\Template\LoadFailedException;
use Shippinno\Template\RenderFailedException;
use Shippinno\Template\Template;
use Shippinno\Template\TemplateNotFoundException;

class TemplateNotificationFactory
{
    /**
     * @var Template
     */
    private $template;

    /**
     * @var string
     */
    private $subjectTemplateNameFormat = '%s.subject';

    /**
     * @var string
     */
    private $bodyTemplateNameFormat = '%s.body';

    /**
     * @param Template $template
     */
    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    /**
     * @param string $templateName
     * @param array $templateVariables
     * @param Destination $destination
     * @param DeduplicationKey|null $deduplicationKey
     * @return Notification
     * @throws LoadFailedException
     * @throws RenderFailedException
     * @throws TemplateNotFoundException
     */
    public function createFromTemplate(
        string $templateName,
        array $templateVariables,
        Destination $destination,
        DeduplicationKey $deduplicationKey = null
    ) {
        $subject = $this->template->render(
            sprintf($this->subjectTemplateNameFormat, $templateName),
            $templateVariables
        );
        $body = $this->template->render(
            sprintf($this->bodyTemplateNameFormat, $templateName),
            $templateVariables
        );

        return new Notification(
            NotificationId::create(),
            $destination,
            new Subject($subject),
            new Body($body),
            $deduplicationKey,
            $templateName,
            $templateVariables
        );
    }
}
