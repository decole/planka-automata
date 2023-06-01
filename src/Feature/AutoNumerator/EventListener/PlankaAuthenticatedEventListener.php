<?php

namespace App\Feature\AutoNumerator\EventListener;

use App\Feature\AutoNumerator\Service\AutoNumerateCardService;
use App\Feature\PlankaAuthenticator\Event\PlankaAuthenticatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class PlankaAuthenticatedEventListener
{
    public function __construct(private readonly AutoNumerateCardService $service)
    {
    }

    public function __invoke(PlankaAuthenticatedEvent $event): void
    {
        $this->service->handle($event->getIntegrator());
    }
}