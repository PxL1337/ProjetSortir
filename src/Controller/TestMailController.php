<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
    #[Route('/test/email', name: 'app_test_email')]
    public function sendTestEmail(MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from('sortir@pixdev.ovh')
            ->to('val.pxl.vp@gmail.com')
            ->subject('Test Email')
            ->html('<p>This is a test email.</p>');

        $mailer->send($email);

        return new Response('Test email sent!');
    }
}

