<?php

declare(strict_types=1);

namespace App\Feature\DeadLineNotify\EventListener;

use App\Feature\DeadLineNotify\Service\DeadLineNotifyService;
use App\Feature\PlankaAuthenticator\Event\PlankaAuthenticatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class PlankaAuthenticatedEventListener
{
    public function __construct(private readonly DeadLineNotifyService $service)
    {
    }

    public function __invoke(PlankaAuthenticatedEvent $event): void
    {
        $this->service->handle($event->getIntegrator());
    }
}