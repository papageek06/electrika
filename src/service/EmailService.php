<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class EmailService {

    private string $adminEmail;
    private MailerInterface $mailer;

    public function __construct(string $adminEmail, MailerInterface $mailer) {

        $this->adminEmail = $adminEmail;
        $this->mailer = $mailer;
    }

    public function sendEmail($emailUser, $attachedFile, $nameFile, $data, $subject, $template) {


$email = (new TemplatedEmail())
    ->from($this->adminEmail)
    ->to($this->adminEmail)
    ->subject('Test de base')
    ->html('<p>Email de test sans template</p>');

$this->mailer->send($email);

    }

    public function sendEmailNoAttachement($emailUser, $data, $subject, $template) {


        $email = (new TemplatedEmail())
        ->from($this->adminEmail)
        ->to($this->adminEmail)
        ->cc($emailUser)
        ->subject($subject)
        ->htmlTemplate($template)
        ->context($data);

        $this->mailer->send($email);

    }
    
}


?>