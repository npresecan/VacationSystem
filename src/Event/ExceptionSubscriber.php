<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Psr\Log\LoggerInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;
    private RequestStack $requestStack;
    private LoggerInterface $logger;

    public function __construct(RouterInterface $router, RequestStack $requestStack, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $this->logger->error('Exception occurred: ' . $exception->getMessage());
        
        $session = $this->requestStack->getSession();

        if (!$session->isStarted()) {
            $session->start();
        }

        if (!$session->has('flashes')) {
            $session->set('flashes', []);
        }
        
        $session->getFlashBag()->add('error', 'You do not have permission to access this page.');
        $session->save();
        $event->setResponse(new RedirectResponse($this->router->generate('login')));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }
}