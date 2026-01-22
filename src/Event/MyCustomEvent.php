<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class MyCustomEvent extends Event
{
    private string $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function getData(): string
    {
        return $this->data;
    }
}
