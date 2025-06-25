<?php
namespace App\EventSuscriber;

use App\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;

class ValidationExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private RouterInterface $router)
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

        if (!$exception instanceof ValidationException) {
            return;
        }

        $request = $event->getRequest();

        $messages = [];
        foreach ($exception->getViolations() as $violation) {
            $messages[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        if ($request->isXmlHttpRequest() || 0 === strpos($request->headers->get('Content-Type', ''), 'application/json')) {
            // Respuesta JSON para peticiones API
            $response = new JsonResponse([
                'error' => 'Validation failed',
                'details' => $messages,
            ], 400);
        } else {
            // Petición HTML: flash message y redirección
            $session = $request->getSession();
            if ($session) {
                if (!$session->isStarted()) {
                    $session->start();
                }
                $session->getFlashBag()->add('error', implode('<br>', $messages));
            }

            $url = $this->router->generate('app_user_crud_index');
            $response = new RedirectResponse($url);
        }

        $event->setResponse($response);
    }
}