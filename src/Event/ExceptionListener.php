<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;

class ExceptionListener
{
    private RouterInterface $router;
    private FlashBagInterface $flashBag;
    private LoggerInterface $logger;

    public function __construct(RouterInterface $router, FlashBagInterface $flashBag, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $this->logger->error('Exception occurred: ' . $exception->getMessage());

        if ($exception instanceof AccessDeniedException) {
            $this->flashBag->add('error', 'You do not have permission to access this page.');
            $response = new RedirectResponse($this->router->generate('login'));
            $event->setResponse($response);
            return;
        }

        $this->flashBag->add('error', 'An unexpected error occurred. Please try again later.');
        $event->setResponse(new Response('<h1>Something went wrong</h1>', Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}