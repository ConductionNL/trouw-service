<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Service\TrouwService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;


class WebHookSubscriber implements EventSubscriberInterface
{
    private $params;
    private $trouwService;
    private $serializer;

    public function __construct(ParameterBagInterface $params, TrouwService $trouwService, SerializerInterface $serializer)
    {
        $this->params = $params;
        $this->trouwService = $trouwService;
        $this->serializer = $serializer;
    }

    public static function getSubscribedEvents()
    {
        return [

        ];
    }

    public function getWebHook(GetResponseForControllerResultEvent $event)
    {
        $webHook = $event->getControllerResult();
        $taskUri = json_decode($event->getRequest()->getContent(), true)['task'];
        $resourceUri = json_decode($event->getRequest()->getContent(), true)['resource'];

        $this->trouwService->getWebHook($taskUri, $resourceUri);

    }
}
