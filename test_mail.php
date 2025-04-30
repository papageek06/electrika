<?php
require 'vendor/autoload.php';

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

$transport = Transport::fromDsn('smtp://localhost:2025');
$mailer = new Mailer($transport);

$email = (new Email())
    ->from('test@local.dev')
    ->to('test@local.dev')
    ->subject('Test SMTP brut')
    ->text('Mail envoyé sans Symfony.');

$mailer->send($email);

echo "✅ Mail brut envoyé via PHP\n";
