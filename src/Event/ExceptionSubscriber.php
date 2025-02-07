<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Psr\Log\LoggerInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $this->logger->error('Exception occurred: ' . $exception->getMessage());
        
        if ($exception instanceof AuthenticationException) {
            $response = new JsonResponse(['error' => 'Invalid credentials'], 401);
        } elseif ($exception instanceof AccessDeniedException) {
            $response = new JsonResponse(['error' => 'Access denied'], 403);
        } else {
            $response = new JsonResponse(['error' => 'Internal Server Error', 'message' => $exception->getMessage()], 500);
        }

        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }
}