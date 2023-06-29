<?php

declare(strict_types=1);

namespace App\Feature\DragFinishCardToArchiveBoard\EventListener;

use App\Feature\DragFinishCardToArchiveBoard\Service\DragFinishCardToArchiveBoardService;
use App\Feature\PlankaAuthenticator\Event\PlankaAuthenticatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(priority: 80)]
final class PlankaAuthenticatedEventListener
{
    public function __construct(private readonly DragFinishCardToArchiveBoardService $service)
    {
    }

    public function __invoke(PlankaAuthenticatedEvent $event): void
    {
        $this->service->handle($event->getIntegrator());
    }
}