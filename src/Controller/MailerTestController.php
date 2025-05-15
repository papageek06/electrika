<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailTestController extends AbstractController
{
    #[Route('/mail-test', name: 'app_mail_test')]
    public function testMail(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('marc.longmar@gmail.com')
                ->to('tonadresse@gmail.com')
                ->subject('Test SMTP en prod')
                ->text('Ceci est un test d’envoi de mail depuis le serveur de production.');

            $mailer->send($email);

            return new Response('✅ Mail envoyé avec succès.');
        } catch (TransportExceptionInterface $e) {
            return new Response('❌ Erreur d\'envoi de mail : ' . $e->getMessage());
        } catch (\Throwable $e) {
            return new Response('❌ Autre erreur : ' . $e->getMessage());
        }
    }
}

