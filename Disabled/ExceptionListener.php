<?php
// src/EventListener/ExceptionListener.php
namespace Disabled;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class ExceptionListener implements EventSubscriberInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedException) {
            // Render your custom view for the 403 error
            $response = new Response();
            $response->setContent($this->twig->render('bundles/TwigBundle/Exception/error403.html.twig'));
            $event->setResponse($response);
        } elseif ($exception instanceof NotFoundHttpException) {
            // Render your custom view for the 404 error
            $response = new Response();
            $response->setContent($this->twig->render('bundles/TwigBundle/Exception/error404.html.twig'));
            $event->setResponse($response);
        } elseif ($exception instanceof MethodNotAllowedException) {
            // Render your custom view for the 405 error
            $response = new Response();
            $response->setContent($this->twig->render('bundles/TwigBundle/Exception/error405.html.twig'));
            $event->setResponse($response);
        } else {
            // Render your custom view for the 500 error
            $response = new Response();
            $response->setContent($this->twig->render('bundles/TwigBundle/Exception/error500.html.twig'));
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', -1],
        ];
    }
}
