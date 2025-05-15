<?php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailTestController extends AbstractController
{
    #[Route('/mail-test', name: 'app_mail_test')]
    public function testMail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('marc.longmar@gmail.com')
            ->to('tonadresse@gmail.com') // mets ici ton adresse à toi
            ->subject('Test mail prod')
            ->text('Ceci est un test d’envoi de mail depuis le serveur de production.');

        $mailer->send($email);

        return new Response('Test mail envoyé.');
    }
}
