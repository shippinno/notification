<?php

namespace Shippinno\Notification\Domain\Model;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use PHPUnit\Framework\TestCase;
use Shippinno\Template\Liquid;
use Tanigami\ValueObjects\Web\EmailAddress;

class TemplateNotificationFactoryTest extends TestCase
{
    public function testItCreatesNotificationFromTemplate()
    {
        $filesystem = new Filesystem(new MemoryAdapter);
        $filesystem->write('SomeNotification__subject.liquid', 'Hello {{ you }} !!');
        $filesystem->write('SomeNotification__body.liquid', 'Good bye {{ her }} :)');
        $template = new Liquid($filesystem);
        $factory = new TemplateNotificationFactory($template);
        $notification = $factory->create(
            'SomeNotification',
            ['you' => 'Shippinno', 'her' => 'Jessica'],
            new EmailDestination([new EmailAddress('to@example.com')])
        );
        $this->assertSame('Hello Shippinno !!', $notification->subject()->subject());
        $this->assertSame('Good bye Jessica :)', $notification->body()->body());
    }
}
