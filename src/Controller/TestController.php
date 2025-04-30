<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/mail-test', name: 'app_event_mail_test')]
    public function mailTest(MailerInterface $mailer): Response
    {
        $email = (new \Symfony\Component\Mime\Email())
            ->from('test@local.dev')
            ->to('test@local.dev')
            ->subject('Test MailDev depuis EventController')
            ->text('Ceci est un e-mail de test envoyé depuis MailDev.');

        $mailer->send($email);

        return new Response('✅ E-mail envoyé à MailDev depuis EventController.');
    }
    #[Route('/env-check', name: 'env_check')]
public function check(): Response
{
    return new Response('MAILER_DSN = ' . $_ENV['MAILER_DSN']);
}
}
