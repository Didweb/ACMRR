<?php
namespace App\EventSuscriber;

use App\Exception\BusinessException;
use App\Service\RedirectUrlResolver;
use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Utils\JsonResponseFactory;

class ValidationExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RouterInterface $router,
        private RedirectUrlResolver $redirectUrlResolver)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if ($exception instanceof ValidationException) {
            $response = $this->handleValidationException($exception, $request);
            $event->setResponse($response);
            return;
        }

        if ($exception instanceof BusinessException) {
            $response = $this->handleBusinessException($exception, $request);
            $event->setResponse($response);
            return;
        }
    }

    
    private function handleValidationException(ValidationException $exception, Request $request): Response
    {
        $messages = [];
        foreach ($exception->getViolations() as $violation) {
            $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        if ($this->isApiRequest($request)) {
            return JsonResponseFactory::error($messages);
        }

        $session = $request->getSession();
        if ($session && !$session->isStarted()) {
            $session->start();
        }
        $session->getFlashBag()->add('error', implode('<br>', $messages));

        $redirectUrl = $this->redirectUrlResolver->__invoke($request);
        return new RedirectResponse($redirectUrl);
    }

    private function handleBusinessException(BusinessException $exception, Request $request): Response
    {
        $message = $exception->getMessage();

        if ($this->isApiRequest($request)) {
            return JsonResponseFactory::error($message);
        }

        $session = $request->getSession();
        if ($session && !$session->isStarted()) {
            $session->start();
        }
        $session->getFlashBag()->add('error', $message);

        $redirectUrl = $this->redirectUrlResolver->__invoke($request);
        return new RedirectResponse($redirectUrl);
    }

    private function isApiRequest(Request $request): bool
    {
        return $request->isXmlHttpRequest()
            || 0 === strpos($request->headers->get('Content-Type', ''), 'application/json');
    }
}