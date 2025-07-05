<?php

namespace App\Tests\Service;

use App\Service\EmailService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailServiceTest extends TestCase
{
    public function testSendEmailCallsMailerSend()
    {
        $adminEmail = 'admin@example.com';
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(TemplatedEmail::class));

        $service = new EmailService($adminEmail, $mailer);
        $service->sendEmail('user@example.com', 'filecontent', 'file.txt', ['foo' => 'bar'], 'Sujet', 'template.html.twig');

        echo "testSendEmailCallsMailerSend OK\n";
    }

    public function testSendEmailNoAttachementCallsMailerSend()
    {
        $adminEmail = 'admin@example.com';
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(TemplatedEmail::class));

        $service = new EmailService($adminEmail, $mailer);
        $service->sendEmailNoAttachement('user@example.com', ['foo' => 'bar'], 'Sujet', 'template.html.twig');

        echo "testSendEmailNoAttachementCallsMailerSend OK\n";
    }
}
