<?php
namespace App\Service;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\MyCustomEvent;

class MyEventService
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function doSomething(): void
    {
        // Logique métier...
        $data = "Coucou, je viens de lancer un événement 🚀";

        // Déclenchement
        $event = new MyCustomEvent($data);
        $this->eventDispatcher->dispatch($event);
    }
}
