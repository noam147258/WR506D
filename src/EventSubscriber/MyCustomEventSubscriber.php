<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\MyCustomEvent;

class MyCustomEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            MyCustomEvent::class => 'onMyCustomEvent',
        ];
    }

    public function onMyCustomEvent(MyCustomEvent $event): void
    {
        // Logique de traitement de l'événement
        $data = $event->getData();
    }
}
